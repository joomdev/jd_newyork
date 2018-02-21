<?php
/**
 * @version   $Id: install.script.php 20758 2014-04-30 22:41:10Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 *
 */
class JoomlaInstallerScript
{
	/**
	 * @param $type
	 * @param $parent
	 *
	 * @return bool
	 */
	public function preflight($type, $parent)
	{
		JError::raiseWarning(100, 'The RocketLauncher package should not be installed into an existing Joomla instance. It is a stand-alone Joomla installation.');
		return false;
	}
}
