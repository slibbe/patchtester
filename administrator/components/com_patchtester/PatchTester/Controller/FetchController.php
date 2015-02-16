<?php
/**
 * Patch testing component for the Joomla! CMS
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace PatchTester\Controller;

use PatchTester\Model\PullsModel;

/**
 * Controller class to fetch remote data
 *
 * @since  2.0
 */
class FetchController extends DisplayController
{
	/**
	 * Execute the controller.
	 *
	 * @return  void  Redirects the application
	 *
	 * @since   2.0
	 */
	public function execute()
	{
		try
		{
			// TODO - Decouple the model and context?
			$model = new PullsModel('com_patchtester.fetch', null, \JFactory::getDbo());

			// Initialize the state for the model
			$model->setState($this->initializeState($model));

			$model->requestFromGithub();

			$msg  = \JText::_('COM_PATCHTESTER_FETCH_SUCCESSFUL');
			$type = 'message';
		}
		catch (\Exception $e)
		{
			$msg  = $e->getMessage();
			$type = 'error';
		}

		$this->getApplication()->enqueueMessage($msg, $type);
		$this->getApplication()->redirect(\JRoute::_('index.php?option=com_patchtester', false));
	}
}
