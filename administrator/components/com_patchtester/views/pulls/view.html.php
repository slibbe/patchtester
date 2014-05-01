<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * View class for a list of pull requests.
 *
 * @package  PatchTester
 * @since    1.0
 */
class PatchtesterViewPulls extends JViewLegacy
{
	/**
	 * Array containing environment errors
	 *
	 * @var    array
	 * @since  2.0
	 */
	protected $envErrors = array();

	/**
	 * Array of open pull requests
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $items;

	/**
	 * Object containing data about applied patches
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $patches;

	/**
	 * State object
	 *
	 * @var    JRegistry
	 * @since  1.0
	 */
	protected $state;

	/**
	 * Pagination object
	 *
	 * @var    JPagination
	 * @since  2.0
	 */
	protected $pagination;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		if (!extension_loaded('openssl'))
		{
			$this->envErrors[] = JText::_('COM_PATCHTESTER_REQUIREMENT_OPENSSL');
		}

		if (!in_array('https', stream_get_wrappers()))
		{
			$this->envErrors[] = JText::_('COM_PATCHTESTER_REQUIREMENT_HTTPS');
		}

		// Only process the data if there are no environment errors
		if (!count($this->envErrors))
		{
			$this->state      = $this->get('State');
			$this->items      = $this->get('Items');
			$this->patches    = $this->get('AppliedPatches');
			$this->pagination = $this->get('Pagination');

			// Check for errors.
			$errors = $this->get('Errors');

			if (count($errors))
			{
				JError::raiseError(500, implode("\n", $errors));

				return false;
			}
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_PATCHTESTER'), 'patchtester');

		if (!count($this->envErrors))
		{
			JToolbarHelper::custom('pulls.fetch', 'delete.png', 'delete_f2.png', 'COM_PATCHTESTER_TOOLBAR_FETCH_DATA', false);
		}

		JToolBarHelper::preferences('com_patchtester');

		JFactory::getDocument()->addStyleDeclaration(
			'.icon-48-patchtester {background-image: url(components/com_patchtester/assets/images/icon-48-patchtester.png);}'
		);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   2.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.title'   => JText::_('JGLOBAL_TITLE'),
			'a.pull_id' => JText::_('COM_PATCHTESTER_PULL_ID'),
			'applied'   => JText::_('JSTATUS')
		);
	}
}
