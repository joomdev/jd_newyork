<?php
/**
 * @package DJ-Events
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_djevents')) {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 500);
}

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

$lang = JFactory::getLanguage();
if ($lang->get('lang') != 'en-GB') {
    $lang = JFactory::getLanguage();
    $lang->load('com_djevents', JPATH_ADMINISTRATOR, 'en-GB', false, false);
    $lang->load('com_djevents', JPATH_COMPONENT_ADMINISTRATOR, 'en-GB', false, false);
    $lang->load('com_djevents', JPATH_ADMINISTRATOR, null, true, false);
    $lang->load('com_djevents', JPATH_COMPONENT_ADMINISTRATOR, null, true, false);
}

// DJ-Events version no.
$db = JFactory::getDBO();
$db->setQuery("SELECT manifest_cache FROM #__extensions WHERE type='component' AND element='com_djevents' LIMIT 1");
$version = json_decode($db->loadResult());
$version = (empty($version->version)) ? 'undefined' : $version->version;

$year = JFactory::getDate()->format('Y');
define('DJEVENTSFOOTER', '<div style="text-align: center; margin: 10px 0;">DJ-Events (ver. '.$version.'), &copy; 2009-'.$year.' Copyright by <a target="_blank" href="http://dj-extensions.com">DJ-Extensions.com</a>, All Rights Reserved.<br /><a target="_blank" href="http://dj-extensions.com"><img src="'.JURI::base().'components/com_djevents/assets/images/djextensions.png" alt="dj-extensions.com" style="margin-top: 20px;"/></a></div>');

jimport('joomla.utilities.string');
jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT.DS.'lib'.DS.'image.php');
require_once(JPATH_COMPONENT.DS.'lib'.DS.'video.php');
require_once(JPATH_COMPONENT.DS.'lib'.DS.'upload.php');
require_once(JPATH_COMPONENT.DS.'lib'.DS.'djlicense.php');
require_once(JPATH_COMPONENT.DS.'lib'.DS.'import.php');

$document = JFactory::getDocument();
if ($document->getType() == 'html') {
	$document->addStyleSheet(JURI::base().'components/com_djevents/assets/css/adminstyle.css');
}

$controller	= JControllerLegacy::getInstance('DJEvents');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
