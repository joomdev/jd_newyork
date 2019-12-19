<?php
/**
 *  
 * @package    Com_Jdprofiler
 * @author     JoomDev
 * @copyright  Copyright (C) 2019 JoomDev, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Profile controller class.
 *
 * @since  1.6
 */
class JdprofilerControllerProfile extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'profiles';
		parent::__construct();
	}
}
