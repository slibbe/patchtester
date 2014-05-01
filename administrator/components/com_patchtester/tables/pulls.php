<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * Pulls Table class
 *
 * @package  PatchTester
 * @since    1.0
 */
class PatchtesterTablePulls extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  JDatabaseDriver object.
	 *
	 * @since   1.0
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__patchtester_pulls', 'id', $db);
	}
}
