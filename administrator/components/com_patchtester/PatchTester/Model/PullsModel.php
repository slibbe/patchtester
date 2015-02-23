<?php
/**
 * Patch testing component for the Joomla! CMS
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace PatchTester\Model;

use Joomla\Registry\Registry;

use PatchTester\Helper;

/**
 * Model class for the pulls list view
 *
 * @since  2.0
 */
class PullsModel extends \JModelDatabase
{
	/**
	 * The object context
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $context;

	/**
	 * Array of fields the list can be sorted on
	 *
	 * @var    array
	 * @since  2.0
	 */
	protected $sortFields = array();

	/**
	 * Instantiate the model.
	 *
	 * @param   string            $context  The model context.
	 * @param   Registry          $state    The model state.
	 * @param   \JDatabaseDriver  $db       The database adpater.
	 *
	 * @since   2.0
	 */
	public function __construct($context, Registry $state = null, \JDatabaseDriver $db = null)
	{
		parent::__construct($state, $db);

		$this->context    = $context;
		$this->sortFields = array('a.pull_id', 'a.title', 'applied');
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
		$db = $this->getDb();

		$db->setQuery(
		   $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__patchtester_tests'))
				->where($db->quoteName('applied') . ' = 1')
		);

		return $db->loadObjectList('pull_id');
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   2.0
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();

		$items = $this->_getList($query, $this->getStart(), $this->getState()->get('list.limit'));

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  \JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   2.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDb();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState()->get('list.select', 'a.*'));
		$query->from($db->quoteName('#__patchtester_pulls', 'a'));

		// Join the tests table to get applied patches
		$query->select($db->quoteName('t.id', 'applied'));
		$query->join('LEFT', $db->quoteName('#__patchtester_tests', 't') . ' ON t.pull_id = a.pull_id');

		// Filter by search
		$search = $this->getState()->get('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where(
				'(' . $db->quoteName('a.title') . ' LIKE ' . $search . ') OR ' .
				'(' . $db->quoteName('a.pull_id') . ' LIKE ' . $search . ') '
			);
		}

		// Filter for applied patches
		$applied = $this->getState()->get('filter.applied');

		if (!empty($applied))
		{
			// Not applied patches have a NULL value, so build our value part of the query based on this
			if ($applied == 'no')
			{
				$value = ' IS NULL';
			}
			else
			{
				$value = ' = 1';
			}

			$query->where($db->quoteName('applied') . $value);
		}

		// Handle the list ordering.
		$ordering  = $this->getState()->get('list.ordering');
		$direction = $this->getState()->get('list.direction');

		if (!empty($ordering))
		{
			$query->order($db->escape($ordering) . ' ' . $db->escape($direction));
		}

		// If $ordering is by applied patches, then append sort on pull_id also
		if ($ordering === 'applied')
		{
			$query->order('a.pull_id ' . $db->escape($direction));
		}

		return $query;
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  \JPagination  A JPagination object for the data set.
	 *
	 * @since   2.0
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Create the pagination object.
		$limit = (int) $this->getState()->get('list.limit') - (int) $this->getState()->get('list.links');
		$page = new \JPagination($this->getTotal(), $this->getStart(), $limit);

		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
	}

	/**
	 * Retrieves the array of authorized sort fields
	 *
	 * @return  array
	 *
	 * @since   2.0
	 */
	public function getSortFields()
	{
		return $this->sortFields;
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   2.0
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . $this->getState()->get('list.start');
		$id .= ':' . $this->getState()->get('list.limit');
		$id .= ':' . $this->getState()->get('list.ordering');
		$id .= ':' . $this->getState()->get('list.direction');

		return md5($this->context . ':' . $id);
	}

	/**
	 * Method to get the starting number of items for the data set.
	 *
	 * @return  integer  The starting number of items available in the data set.
	 *
	 * @since   2.0
	 */
	public function getStart()
	{
		$store = $this->getStoreId('getStart');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$start = $this->getState()->get('list.start');
		$limit = $this->getState()->get('list.limit');
		$total = $this->getTotal();

		if ($start > $total - $limit)
		{
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 *
	 * @since   2.0
	 */
	public function getTotal()
	{
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the total.
		$query = $this->_getListQuery();

		$total = (int) $this->_getListCount($query);

		// Add the total to the internal cache.
		$this->cache[$store] = $total;

		return $this->cache[$store];
	}

	/**
	 * Method to request new data from GitHub
	 *
	 * @return  void
	 *
	 * @since   2.0
	 * @throws  \RuntimeException
	 */
	public function requestFromGithub()
	{
		// Get the Github object
		$github = Helper::initializeGithub();
		$rate   = $github->authorization->getRateLimit();

		// If over the API limit, we can't build this list
		if ($rate->resources->core->remaining == 0)
		{
			throw new \RuntimeException(
				\JText::sprintf('COM_PATCHTESTER_API_LIMIT_LIST', \JFactory::getDate($rate->resources->core->reset))
			);
		}

		// Sanity check, ensure there aren't any applied patches
		if (count($this->getAppliedPatches()) >= 1)
		{
			throw new \RuntimeException(\JText::_('COM_PATCHTESTER_ERROR_APPLIED_PATCHES'));
		}

		$pulls = array();
		$page  = 0;

		do
		{
			$page++;

			try
			{
				$items = $github->pulls->getList($this->getState()->get('github_user'), $this->getState()->get('github_repo'), 'open', $page, 100);
			}
			catch (\DomainException $e)
			{
				throw new \RuntimeException(\JText::sprintf('COM_PATCHTESTER_ERROR_GITHUB_FETCH', $e->getMessage()));
			}

			$count = is_array($items) ? count($items) : 0;

			if ($count)
			{
				$pulls = array_merge($pulls, $items);
			}
		}
		while ($count);

		// Dump the old data now
		$this->getDb()->truncateTable('#__patchtester_pulls');

		// If there are no pulls to insert then bail
		if (empty($pulls))
		{
			return;
		}

		$data = array();

		foreach ($pulls as $pull)
		{
			// Build the data object to store in the database
			$pullData = array($pull->number, $pull->title, $pull->body, $pull->html_url);
			$data[] = implode($this->getDb()->quote($pullData), ',');
		}

		$this->getDb()->setQuery(
			$this->db->getQuery(true)
				->insert('#__patchtester_pulls')
				->columns('pull_id, title, description, pull_url')
				->values($data)
		);

		try
		{
			$this->getDb()->execute();
		}
		catch (\RuntimeException $e)
		{
			throw new \RuntimeException(\JText::sprintf('COM_PATCHTESTER_ERROR_INSERT_DATABASE', $e->getMessage()));
		}
	}

	/**
	 * Gets an array of objects from the results of database query.
	 *
	 * @param   \JDatabaseQuery|string  $query       The query.
	 * @param   integer                 $limitstart  Offset.
	 * @param   integer                 $limit       The number of records.
	 *
	 * @return  array  An array of results.
	 *
	 * @since   2.0
	 * @throws  RuntimeException
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$this->getDb()->setQuery($query, $limitstart, $limit);
		$result = $this->getDb()->loadObjectList();

		return $result;
	}

	/**
	 * Returns a record count for the query.
	 *
	 * @param   \JDatabaseQuery|string  $query  The query.
	 *
	 * @return  integer  Number of rows for query.
	 *
	 * @since   2.0
	 */
	protected function _getListCount($query)
	{
		// Use fast COUNT(*) on JDatabaseQuery objects if there no GROUP BY or HAVING clause:
		if ($query instanceof \JDatabaseQuery && $query->type == 'select' && $query->group === null && $query->having === null)
		{
			$query = clone $query;
			$query->clear('select')->clear('order')->select('COUNT(*)');

			$this->getDb()->setQuery($query);

			return (int) $this->getDb()->loadResult();
		}
		// Otherwise fall back to inefficient way of counting all results.
		else
		{
			$this->getDb()->setQuery($query)->execute();

			return (int) $this->getDb()->getNumRows();
		}
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @return  \JDatabaseQuery  A JDatabaseQuery object
	 *
	 * @since   2.0
	 */
	protected function _getListQuery()
	{
		// Capture the last store id used.
		static $lastStoreId;

		// Compute the current store id.
		$currentStoreId = $this->getStoreId();

		// If the last store id is different from the current, refresh the query.
		if ($lastStoreId != $currentStoreId || empty($this->query))
		{
			$lastStoreId = $currentStoreId;
			$this->query = $this->getListQuery();
		}

		return $this->query;
	}
}
