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

// no direct access
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$lang = JFactory::GetLanguage();
$doc = JFactory::getDocument();

if($app->input->get('option')=='com_djevents' && in_array($app->input->get('view','none'), $params->get('hide_on', array()))) return;

$lang->load('com_djevents');

// Include the syndicate functions only once
require_once (JPath::clean(dirname(__FILE__).'/helper.php'));
require_once(JPath::clean(JPATH_BASE.'/components/com_djevents/helpers/djevents.php'));
require_once(JPath::clean(JPATH_BASE.'/components/com_djevents/helpers/route.php'));

$cparams = JComponentHelper::getParams('com_djevents');
$params->def('date_format', $cparams->get('date_format'));
$params->def('time_format', $cparams->get('time_format'));
$params->def('map_latitude', $cparams->get('map_latitude'));
$params->def('map_longitude', $cparams->get('map_longitude'));
$params->def('map_zoom', $cparams->get('map_zoom'));
$params->def('map_marker', $cparams->get('map_marker'));
$params->def('map_styles', $cparams->get('map_styles'));

JHtml::_('jquery.framework');
$api_key = $cparams->get('map_api_key','');
if(!empty($api_key)) $api_key = '&key='.$api_key;
$lang_tag = explode('-', $lang->getTag())[0];
$doc->addScript('//maps.google.com/maps/api/js?sensor=false&language='.$lang_tag.$api_key);
$doc->addScript(JURI::base(true).'/modules/mod_djevents_map/assets/js/markerclusterer.js');

DJEventsHelper::setAssets();

$points = modDJEventsMapHelper::getPoints($params);
$categories = modDJEventsMapHelper::getCategories($params);
$classes = DJEventsHelper::getBSClasses();

require JModuleHelper::getLayoutPath('mod_djevents_map', $params->get('layout','default'));
