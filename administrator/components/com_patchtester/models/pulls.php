<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * Methods supporting a list of pull requests.
 *
 * @package  PatchTester
 * @since    1.0
 */
class PatchtesterModelPulls extends JModelList
{
	/**
	 * Github object
	 *
	 * @var    JGithub
	 * @since  2.0
	 */
	protected $github;

	/**
	 * Object containing the rate limit data
	 *
	 * @var    object
	 * @since  2.0
	 */
	protected $rate;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'title', 'applied'
			);
		}

		parent::__construct($config);

		// Set up the Github object
		$params = JComponentHelper::getParams('com_patchtester');

		$options = new JRegistry;

		// Set the username and password if set in the params
		if ($params->get('gh_user', '') && $params->get('gh_password'))
		{
			$options->set('api.username', $params->get('gh_user', ''));
			$options->set('api.password', $params->get('gh_password', ''));
		}
		else
		{
			// Display a message about the lowered API limit without credentials
			JFactory::getApplication()->enqueueMessage(JText::_('COM_PATCHTESTER_NO_CREDENTIALS'), 'notice');
		}

		$this->github = new JGithub($options);

		// Store the rate data for reuse during this request cycle
		$this->rate = $this->github->authorization->getRateLimit()->rate;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @note    Calling getState() in this method will result in recursion.
	 * @since   1.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_patchtester');

		$this->setState('params', $params);
		$this->setState('github_user', $params->get('org', 'joomla'));
		$this->setState('github_repo', $params->get('repo', 'joomla-cms'));

		// List state information.
		parent::populateState('a.pull_id', 'desc');
	}

	/**
	 * Retrieves a list of applied patches
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	public function getAppliedPatches()
	{
		$db = $this->getDbo();

		$db->setQuery(
		   $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__patchtester_tests'))
				->where($db->quoteName('applied') . ' = 1')
		);

		try
		{
			$tests = $db->loadObjectList('pull_id');

			return $tests;
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   2.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from($db->quoteName('#__patchtester_pulls', 'a'));

		// Filter by search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where($db->quoteName('a.pull_id') . ' = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')');
			}
		}

		// Handle the list ordering.
		$ordering  = $this->getState('list.ordering');
		$direction = $this->getState('list.direction');

		if (!empty($ordering))
		{
			$query->order($db->escape($ordering) . ' ' . $db->escape($direction));
		}

		return $query;
	}

	/**
	 * Method to request new data from GitHub
	 *
	 * @return  void
	 *
	 * @since   2.0
	 * @throws  RuntimeException
	 */
	public function requestFromGithub()
	{
		// If over the API limit, we can't build this list
		if ($this->rate->remaining > 0)
		{
			// Sanity check, ensure there aren't any applied patches
			if (count($this->getAppliedPatches()) >= 1)
			{
				throw new RuntimeException(JText::_('COM_PATCHTESTER_ERROR_APPLIED_PATCHES'));
			}

			$pulls = array();
			$page  = 0;

			do
			{
				$page++;

				try
				{
					$items = $this->github->pulls->getList($this->getState('github_user'), $this->getState('github_repo'), 'open', $page, 100);
				}
				catch (DomainException $e)
				{
					throw new RuntimeException(JText::sprintf('COM_PATCHTESTER_ERROR_GITHUB_FETCH', $e->getMessage()));
				}

				$count = is_array($items) ? count($items) : 0;

				if ($count)
				{
					$pulls = array_merge($pulls, $items);
				}

			}
			while ($count);

			// Dump the old data now
			$this->getDbo()->truncateTable('#__patchtester_pulls');

			foreach ($pulls as &$pull)
			{
				// Build the data object to store in the database
				$data              = new stdClass;
				$data->pull_id     = $pull->number;
				$data->title       = $pull->title;
				$data->description = $pull->body;
				$data->pull_url    = $pull->html_url;

				// Try to find a Joomlacode issue number
				$matches = array();

				preg_match('#\[\#([0-9]+)\]#', $pull->title, $matches);

				if (isset($matches[1]))
				{
					$data->joomlacode_id = (int) $matches[1];
				}
				else
				{
					preg_match('#(http://joomlacode[-\w\./\?\S]+)#', $pull->body, $matches);

					if (isset($matches[1]))
					{
						preg_match('#tracker_item_id=([0-9]+)#', $matches[1], $matches);

						if (isset($matches[1]))
						{
							$data->joomlacode_id = (int) $matches[1];
						}
					}
				}

				try
				{
					$this->getDbo()->insertObject('#__patchtester_pulls', $data, 'id');
				}
				catch (RuntimeException $e)
				{
					throw new RuntimeException(JText::sprintf('COM_PATCHTESTER_ERROR_INSERT_DATABASE', $e->getMessage()));
				}
			}
		}
		else
		{
			throw new RuntimeException(JText::sprintf('COM_PATCHTESTER_API_LIMIT_LIST', JFactory::getDate($this->rate->reset)));
		}
	}
}
