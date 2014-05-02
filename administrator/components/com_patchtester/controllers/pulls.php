<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * Pulls controller class
 *
 * @package  PatchTester
 * @since    2.0
 */
class PatchtesterControllerPulls extends JControllerLegacy
{
	/**
	 * Fetch pull request data from GitHub
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function fetch()
	{
		try
		{
			$this->getModel('pulls')->requestFromGithub();

			$msg  = JText::_('COM_PATCHTESTER_FETCH_SUCCESSFUL');
			$type = 'message';
		}
		catch (Exception $e)
		{
			$msg  = $e->getMessage();
			$type = 'error';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_patchtester&view=pulls', false), $msg, $type);
	}
}
