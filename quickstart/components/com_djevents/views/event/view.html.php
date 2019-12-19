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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class DJEventsViewEvent extends JViewLegacy {
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->_addPath('template', JPATH_COMPONENT.  '/themes/bootstrap/views/eventslist');
		$params = DJEventsHelper::getParams();
		$theme = $params->get('theme', 'bootstrap');
		if ($theme && $theme != 'bootstrap') {
			$this->_addPath('template', JPATH_COMPONENT.  '/themes/'.$theme.'/views/eventslist');
		}
	}
	
	function display($tpl = null) {
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->params = DJEventsHelper::getParams();
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$menus		= $app->getMenu('site');
		$menu  = $menus->getActive();
		
		if(empty($this->item)) {
			throw new Exception(JText::_('COM_DJEVENTS_EVENT_NOT_FOUND'), 404);
		}
		
		if($this->item->published != 1 && ($user->guest || $this->item->created_by != $user->id)) {
			throw new Exception(JText::_('COM_DJEVENTS_NO_PERMISSIONS'), 403);
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		if(!empty($this->item->image)) {
			
			if(!$this->item->thumb = DJEventsImageResizer::createThumbnail($this->item->image, 'media/djevents/images', $this->params->get('width'),
					$this->params->get('height'), $this->params->get('resizing'), $this->params->get('quality'))) {
				$this->item->thumb = $this->item->image;
			}
			if(strcasecmp(substr($this->item->thumb, 0, 4), 'http') != 0 && !empty($this->item->thumb)) {
				$this->item->thumb = JURI::root(true).'/'.$this->item->thumb;
			}
		}
		
		$this->images = $this->get('Media');
		$this->tags = $this->get('Tags');
		
		foreach($this->images as $item) {
			
			if(!$item->thumb = DJEventsImageResizer::createThumbnail($item->image, 'media/djevents/images', $this->params->get('thumb_width'), 
					$this->params->get('thumb_height'), $this->params->get('thumb_resizing'), $this->params->get('thumb_quality'))) {
				$item->thumb = $item->image;
			}
			if(!$item->small = DJEventsImageResizer::createThumbnail($item->image, 'media/djevents/images', $this->params->get('small_width'),
					$this->params->get('small_height'), 'crop', $this->params->get('small_quality'))) {
				$item->small = $item->image;
			}
			if(strcasecmp(substr($item->image, 0, 4), 'http') != 0 && !empty($item->image)) {
				$item->image = JURI::root(true).'/'.$item->image;
			}
			if(strcasecmp(substr($item->thumb, 0, 4), 'http') != 0 && !empty($item->thumb)) {
				$item->thumb = JURI::root(true).'/'.$item->thumb;
			}
			if(strcasecmp(substr($item->small, 0, 4), 'http') != 0 && !empty($item->small)) {
				$item->small = JURI::root(true).'/'.$item->small;
			}
		}
		
		$this->item->link = DJEventsHelperRoute::getEventRoute($this->item->id.':'.$this->item->alias, $this->item->start, $this->item->cat_id, $this->item->city_id);
		
		$location = array();
		if(!empty($this->item->location)) $location[] = $this->item->location;
		if(!empty($this->item->address)) $location[] = $this->item->address;
		if(!empty($this->item->city_name)) $location[] = $this->item->city_name;
		if(!empty($this->item->post_code)) $location[] = $this->item->post_code;
		$this->item->displayLocation = implode(', ', $location);
		
		$canDefer = preg_match('/(?i)msie [6-9]/',$_SERVER['HTTP_USER_AGENT']) ? false : true;
		
		JHtml::_('jquery.framework');
		DJEventsHelper::setAssets();
		$this->document->addStyleSheet(JURI::root(true).'/media/djextensions/magnific/magnific.css');
		$this->document->addScript(JURI::root(true).'/media/djextensions/magnific/magnific.js', array('mime'=>'text/javascript', 'defer'=>$canDefer));
		$this->document->addScript(JURI::root(true).'/components/com_djevents/assets/js/magnific-init.js', array('mime'=>'text/javascript', 'defer'=>$canDefer));
		
		$api_key = $this->params->get('map_api_key','');
		if(!empty($api_key)) $api_key = '&key='.$api_key;
		$lang_tag = explode('-', JFactory::getLanguage()->getTag())[0];
		$this->document->addScript('//maps.google.com/maps/api/js?sensor=false&language='.$lang_tag.$api_key);
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	protected function _prepareDocument() {
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;
		$heading		= null;
		$document= JFactory::getDocument();
		$menu = $menus->getActive();
				
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		$title = $this->params->get('page_title', '');
		
		$metakeys = null;
		$metadesc = null;

		if ($menu && ($menu->query['option'] != 'com_events' || $menu->query['view'] == 'eventlists')) {
			
			if (!empty($this->item->metatitle)) {
				$title = $this->item->metatitle;
			}
			else if ($this->item->title) {
				$title = $this->item->title;
			}
			
			$path = array(array('title' => $this->item->title, 'link' => ''));
			//$path[] = array('title' => JText::_('COM_DJEVENTS_EVENTSLIST'), 'link' => DJEventsHelperRoute::getEventsListRoute());
			
			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
			
		} else if (!empty($menu)) {
			if ($this->params->get('menu-meta_description')) {
				$metadesc = $this->params->get('menu-meta_description');
			}
			if ($this->params->get('menu-meta_keywords')) {
				$metakeys = $this->params->get('menu-meta_keywords');
			}
		}
		
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			if ($app->getCfg('sitename_pagetitles', 0) == '2') {
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			} else {
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
		}
		$this->document->setTitle($title);

		if (!empty($this->item->metadesc))
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!empty($metadesc)) 
		{
			$this->document->setDescription($metadesc);
		}

		if (!empty($this->item->metakey))
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!empty($metakeys)) 
		{
			$this->document->setMetadata('keywords', $metakeys);
		}
		
		
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		$this->document->addCustomTag('<meta property="og:title" content="'.trim($title).'" />');
		$this->document->addCustomTag('<meta property="og:url" content="'.JRoute::_($this->item->link).'" />');
		if (isset($this->item->thumb)) {
			$this->document->addCustomTag('<meta property="og:image" content="'.$this->item->thumb.'" />');
		}
		
	}

}

?>