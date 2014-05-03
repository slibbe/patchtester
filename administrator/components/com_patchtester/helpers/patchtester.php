<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * Helper class for the patch tester component
 *
 * @package  PatchTester
 * @since    2.0
 */
abstract class PatchtesterHelper
{
	/**
	 * Initializes the JGithub object
	 *
	 * @return  JGithub
	 *
	 * @since   2.0
	 */
	public static function initializeGithub()
	{
		$params = JComponentHelper::getParams('com_patchtester');

		$options = new JRegistry;

		// If an API token is set in the params, use it for authentication
		if ($params->get('gh_token', ''))
		{
			$options->set('gh.token', $params->get('gh_token', ''));
		}
		// Set the username and password if set in the params
		elseif ($params->get('gh_user', '') && $params->get('gh_password'))
		{
			$options->set('api.username', $params->get('gh_user', ''));
			$options->set('api.password', $params->get('gh_password', ''));
		}
		// Display a message about the lowered API limit without credentials
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_PATCHTESTER_NO_CREDENTIALS'), 'notice');
		}

		return new JGithub($options);
	}
}
