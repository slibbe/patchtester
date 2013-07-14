<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * PatchTester Controller
 *
 * @package  PatchTester
 * @since    1.0
 */
class PatchTesterController extends JControllerLegacy
{
	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $default_view = 'pulls';

	/**
	 * Method to purge the cache
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function purge()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		jimport('joomla.filesystem.file');

		if (file_exists(JPATH_CACHE . '/patchtester.json') && !JFile::delete(JPATH_CACHE . '/patchtester.json'))
		{
			$msg     = JText::_('COM_PATCHTESTER_PURGE_FAIL');
			$msgType = 'error';
		}
		else
		{
			$msg     = JText::_('COM_PATCHTESTER_PURGE_SUCCESS');
			$msgType = 'message';
		}

		$this->setRedirect('index.php?option=com_patchtester&view=pulls', $msg, $msgType);
	}
}
