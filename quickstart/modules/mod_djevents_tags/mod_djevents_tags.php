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

defined ('_JEXEC') or die('Restricted access');

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

$app = JFactory::getApplication();
$db = JFactory::getDbo();

// ORDER BY RAND is not optimal, consider to use following
// SELECT MAX(id) FROM #__djev_tags;
// SELECT * FROM #__djev_tags WHERE id IN (list of random numbers generated in php 10x MAX(id)) LIMIT X;

$order = $params->get('order', 'count') == 'count' ? ' ORDER BY count DESC ' : ' ORDER BY RAND() ';
$limit = ' LIMIT ' . (int)$params->get('limit', 20);

$query = 'SELECT t.*, count(x.id) as count FROM #__djev_tags t, #__djev_tags_xref x WHERE t.id=x.tag_id GROUP BY t.id '. $order . $limit;

$db->setQuery($query);

$tags = $db->loadObjectList();

if(!$tags) return;

require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'djevents.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'route.php');

DJEventsHelper::setAssets();
$lang = JFactory::GetLanguage();
$lang->load('com_djevents');

require(JModuleHelper::getLayoutPath('mod_djevents_tags'));
