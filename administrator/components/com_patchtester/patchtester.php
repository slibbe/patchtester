<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_patchtester'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('PatchtesterHelper', __DIR__ . '/helpers/patchtester.php');

$controller = JControllerLegacy::getInstance('PatchTester');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
