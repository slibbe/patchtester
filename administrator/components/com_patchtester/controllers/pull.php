<?php
/**
 * @package        PatchTester
 * @copyright      Copyright (C) 2011 Ian MacLennan, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Pull controller class
 *
 * @package        PatchTester
 */
class PatchtesterControllerPull extends JControllerLegacy
{
	public function apply()
	{
		try
		{
			$this->getModel('pull')
				->apply(JFactory::getApplication()->input->getInt('pull_id'));

			$msg = JText::_('COM_PATCHTESTER_APPLY_OK');
			$type = 'success';
		}
		catch (Exception $e)
		{
			$msg = $e->getMessage();
			$type = 'error';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_patchtester&view=pulls', false), $msg, $type);
	}

	public function revert()
	{
		try
		{
			$this->getModel('pull')
				->revert(JFactory::getApplication()->input->getInt('pull_id'));

			$msg = JText::_('COM_PATCHTESTER_REVERT_OK');
			$type = 'success';
		}
		catch (Exception $e)
		{
			$msg = $e->getMessage();
			$type = 'error';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_patchtester&view=pulls', false), $msg, $type);
	}

}
