<?php

/**
 * // Licensed under the GPL v3
* JD Progressbar module
* @package     JD Open Table 
* @copyright   Copyright (C) 2019 Joomdev, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
 */
// No direct access
// Licensed under the GPL v3
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
$layout = $params->get('layout', 'default');
require JModuleHelper::getLayoutPath('mod_jdprogressbar', $layout);
