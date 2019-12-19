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

class DJEventsHelper {
	
	protected static $params = array();
	protected static $assets = false;
	protected static $modules = array();
	
	public static function getParams($cat_id = 0) {
		$cat_id = (int)$cat_id;
		if (!isset(self::$params[$cat_id])) {
			if ($cat_id == 0) {
				self::$params[$cat_id] = JComponentHelper::getParams('com_djevents');
			} else {
				$globalParams = JComponentHelper::getParams('com_djevents');
				$db = JFactory::getDbo();
				$db->setQuery('SELECT params FROM #__djev_cats WHERE id='.$cat_id);
				$groupParams = $db->loadResult();
				if (!empty($groupParams)) {
					$groupParams = new JRegistry($groupParams);
					$globalParams->merge($groupParams); 
				}
				self::$params[$cat_id] = $globalParams;
			}
		}
		return self::$params[$cat_id];
	}
	
	public static function setAssets($cat_id = 0){
        if (!self::$assets) {
            $params = self::getParams($cat_id);
            
            $lang = JFactory::getLanguage();
            $lang->load('com_djevents', JPATH_ROOT, 'en-GB', false, false);
            $lang->load('com_djevents', JPATH_ROOT.JPath::clean('/components/com_djevents'), 'en-GB', false, false);
            $lang->load('com_djevents', JPATH_ROOT, null, true, false);
            $lang->load('com_djevents', JPATH_ROOT.JPath::clean('/components/com_djevents'), null, true, false);
            
            JHtml::_('bootstrap.framework');
            
            $theme = $params->get('theme', 'bootstrap');
            $document = JFactory::getDocument();
            
            //$document->addScript('https://code.jquery.com/jquery-migrate-3.0.1.js');
            
            $document->addStyleSheet(JUri::base(true).'/components/com_djevents/themes/'.$theme.'/css/theme.css');
            $document->addStyleSheet('//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        
            self::$assets = true;
        }
    }
    
    public static function getModules($position, $style = 'xhtml')
    {
    	if (!isset(self::$modules[$position])) {
    		
    		self::$modules[$position] = '';
    		$document	= JFactory::getDocument();
    		$renderer	= $document->loadRenderer('module');
    		$modules	= JModuleHelper::getModules($position);
    		$params		= array('style' => $style);
    		
    		if(count($modules)) {
    			
    			self::$modules[$position] = '<div class="'.$position.'-modules">';
    			
	    		ob_start();
	    		foreach ($modules as $module) {
	    			echo $renderer->render($module, $params);
	    		}
	    		self::$modules[$position] .= ob_get_clean();
	    		self::$modules[$position] .= '</div>';
    		}
    	}
		
    	return self::$modules[$position];
    }
    
    public static function getBSClasses() {
    
    	$classes = new JObject;
    
    	if(version_compare(JVERSION, '4', '>=')) { // Bootstrap 4
    		$classes->set('row', 'row');
    		$classes->set('col', 'col-md-');
    	} else { // Boostrap 2.3.2
    		$classes->set('row', 'row-fluid');
    		$classes->set('col', 'span');
    	}
    
    	return $classes;
    }
}