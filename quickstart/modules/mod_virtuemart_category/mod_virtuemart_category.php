<?php
defined('_JEXEC') or  die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
* Best selling Products module for VirtueMart
* @version $Id: mod_virtuemart_category.php 1160 2014-05-06 20:35:19Z milbo $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2011-2015 The Virtuemart Team
*
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* @link https://virtuemart.net
*----------------------------------------------------------------------
* This code creates a list of the bestselling products
* and displays it wherever you want
*----------------------------------------------------------------------
*/

if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');

VmConfig::loadConfig();
vmLanguage::loadJLang('mod_virtuemart_category', true);
vmJsApi::jQuery();
vmJsApi::cssSite();

/* Setting */
$categoryModel = VmModel::getModel('Category');
$category_id = $params->get('Parent_Category_id', 0);
$class_sfx = $params->get('class_sfx', '');
$moduleclass_sfx = $params->get('moduleclass_sfx','');
$layout = $params->get('layout','default');
$active_category_id = vRequest::getInt('virtuemart_category_id', '0');
$vendorId = '1';

$level = (int)$params->get('level','2');
$media = (int)$params->get('media', 0);

$categories = array();
vmSetStartTime('categories');
VirtueMartModelCategory::rekurseCategories($vendorId, $category_id, $categories, $level, 0, 0,true, '', 'c.ordering, category_name', 'ASC', true);
vmTime('my categories module time','categories');
//vmdebug('my categories in category module',$categories);
$categoryModel->categoryRecursed = 0;
$parentCategories = $categoryModel->getCategoryRecurse($active_category_id,0);

/* Load tmpl default */
require(JModuleHelper::getLayoutPath('mod_virtuemart_category',$layout));
?>