<?php
/**
 * Patch testing component for the Joomla! CMS
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace PatchTester\Controller;

use PatchTester\Model\PullModel;

/**
 * Controller class to apply patches
 *
 * @since  2.0
 */
class ApplyController extends AbstractController
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
			$model = new PullModel(null, \JFactory::getDbo());

			// Initialize the state for the model
			$model->setState($this->initializeState($model));

			if ($model->apply($this->getInput()->getUint('pull_id')))
			{
				$msg = \JText::_('COM_PATCHTESTER_APPLY_OK');

				// Check if the SHA's were different and alert the user
				if ($model->getState()->get('pull.sha_different', false))
				{
					$this->getApplication()->enqueueMessage(
						\JText::sprintf(
							'COM_PATCHTESTER_DIFFERENT_SHA',
							substr($model->getState()->get('pull.state_sha'), 0, 10),
							substr($model->getState()->get('pull.applied_sha'), 0, 10)
						),
						'warning'
					);
				}
			}
			else
			{
				$msg = \JText::_('COM_PATCHTESTER_NO_FILES_TO_PATCH');
			}

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
