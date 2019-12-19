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

class DJEventsViewEventsList extends JViewLegacy {
	
	protected $filterHeading = null;
	
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
		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->tag 			= $this->get('Tag');
		$this->params		= $app->getParams('com_djevents');
		
		$this->featured		= array();
		$featured_limit = (int)$this->params->get('evlist_featured_limit');
		
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR .'/models');
		$model = JModelLegacy::getInstance('Categories', 'DJEventsModel', array('ignore_request'=>true));
		
		$model->setState('list.start',0);
		$model->setState('list.limit',0);
		
		$this->categories = $model->getItems();
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		if (count($this->items)) {
			$from = JFactory::getDate($this->items[0]->start);
		}
		$this->weekFrom = JFactory::getDate(!empty($from) ? $from : 'now')->format('Y-m-d');
		
		/* prepare poster image */
		foreach($this->items as $key => &$item) {
			
			$item->link = DJEventsHelperRoute::getEventRoute($item->id.':'.$item->alias, $item->start, $item->cat_id, $item->city_id);
			
			if(empty($item->image)) continue;
			
			if(!$item->thumb = DJEventsImageResizer::createThumbnail($item->image, 'media/djevents/images', $this->params->get('thumb_width'), 
					$this->params->get('thumb_height'), $this->params->get('thumb_resizing'), $this->params->get('thumb_quality'))) {
				$item->thumb = $item->image;
			}
			if(strcasecmp(substr($item->thumb, 0, 4), 'http') != 0 && !empty($item->thumb)) {
				$item->thumb = JURI::root(true).'/'.$item->thumb;
			}
			
			if($item->featured && $featured_limit > count($this->featured) && !array_key_exists($item->id, $this->featured)) {
				
				$this->featured[$item->id] = $item;
				unset($this->items[$key]);
			}
		}
		unset($item);
		
		//$featured = $featured_limit - count($this->featured);
				
		if($featured_limit > count($this->featured)) {
			/* there was no featured events on current page 
			 * we need to fetch featured events from next pages */
			$model = JModelLegacy::getInstance('EventsList', 'DJEventsModel', array('ignore_request'=>false));
			
			//$page = $this->pagination->pagesCurrent;
			
			$featured_state = $model->getState();
			$featured_state->set('filter.featured', '1');
			$featured_state->set('list.start', count($this->featured));
			$featured_state->set('list.limit', 10 * $featured_limit); // we have to take more featured events to skip the repeated events
			
			/*
			$model->setState('list.start', ($page - 1) * $featured);
			$model->setState('list.limit', $featured);
			*/
			
			if (!empty($from)) {
				$statefrom = $featured_state->get('filter.from');
				$minfrom = JFactory::getDate($statefrom ? $statefrom : 'now');
				if($minfrom->toUnix() > $from->toUnix()) $from = $minfrom;
				$featured_state->set('filter.from', $from->format('Y-m-d'));
			}
			$featured = $model->getItems();

			/* prepare poster image */
			if(!empty($featured)) {
				
				foreach($featured as &$item) {
					
					if(array_key_exists($item->id, $this->featured)) continue;
					
					$item->link = DJEventsHelperRoute::getEventRoute($item->id.':'.$item->alias, $item->start, $item->cat_id, $item->city_id);
						
					if(!empty($item->image)) {
						
						if(!$item->thumb = DJEventsImageResizer::createThumbnail($item->image, 'media/djevents/images', $this->params->get('thumb_width'),
								$this->params->get('thumb_height'), $this->params->get('thumb_resizing'), $this->params->get('thumb_quality'))) {
							$item->thumb = $item->image;
						}
						if(strcasecmp(substr($item->thumb, 0, 4), 'http') != 0 && !empty($item->thumb)) {
							$item->thumb = JURI::root(true).'/'.$item->thumb;
						}
					}
					
					$this->featured[$item->id] = $item;
					
					if($featured_limit <= count($this->featured)) {
						break;
					}
				}
				unset($item);
			}
		}
		
		// set custom heading if events are filtered with search module
		$headings = array();
		if($city = $app->input->getInt('city')) {
			$cityName = $db->setQuery('SELECT name FROM #__djev_cities WHERE id='.$city)->loadResult();
			$headings[] = JText::sprintf('COM_DJEVENTS_CITY_FILTERING_HEADING', $cityName);
		}
		$from = $app->input->get('from');
		$to = $app->input->get('to');
		if(!empty($from) || !empty($to)) {
			if($from == $to) {
				$headings[] = JText::sprintf('COM_DJEVENTS_ONE_DAY_FILTERING_HEADING', JFactory::getDate($from)->format('l, j F Y'));
			} else if(!empty($from) && !empty($to)) {
				$headings[] = JText::sprintf('COM_DJEVENTS_BETWEEN_FILTERING_HEADING', JFactory::getDate($from)->format('j F Y'), JFactory::getDate($to)->format('j F Y'));
			} else if(!empty($from)) {
				$headings[] = JText::sprintf('COM_DJEVENTS_FROM_FILTERING_HEADING', JFactory::getDate($from)->format('j F Y'));
			} else if(!empty($to)) {
				$headings[] = JText::sprintf('COM_DJEVENTS_TO_FILTERING_HEADING', JFactory::getDate($to)->format('j F Y'));
			}
		}
		if($category = $app->input->getInt('cid')) {
			$headings[] = JText::sprintf('COM_DJEVENTS_CATEGORY_FILTERING_HEADING', $this->categories[$category]->name);
		}
		if($search = $app->input->getString('search')) {
			$headings[] = JText::sprintf('COM_DJEVENTS_SEARCH_PHRASE_FILTERING_HEADING', $search);
		}
		if($this->tag) {
			$headings[] = JText::sprintf('COM_DJEVENTS_TAG_FILTERING_HEADING', $this->tag->name);
		}
		
		
		if(count($headings) > 0) {
			$headings = implode(' ', $headings);
			$this->filterHeading = JText::sprintf('COM_DJEVENTS_EVENTS_LISTING_CUSTOM_HEADING', $headings);
		}
		
		DJEventsHelper::setAssets();
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	
	protected function _prepareDocument() {
		
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;
		$heading	= null;

		$menu = $menus->getActive();
		
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		$title = $this->params->get('page_title', '');
		
		$metakeys = null;
		$metadesc = null;

		if (!empty($menu)) {
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
	/*
		$uri = JFactory::getURI();
		$vars = $uri->getQuery(true);
		unset($vars['order']);
		unset($vars['dir']);
		unset($vars['l']);
		
		$canonical = DJCatalogHelperRoute::getEventsListRoute();
		/*if ($limitstart > 0) {
			$canonical .= '&amp;limitstart='.$limitstart;
		}*/
	/*
		if (!empty($vars)){
			$canonical .= '&'.$uri->buildQuery($vars);
		}
		
		foreach($this->document->_links as $key => $headlink) {
			if ($headlink['relation'] == 'canonical' ) {
				unset($this->document->_links[$key]);
			}
		}
		
		$this->document->addHeadLink(JRoute::_($canonical), 'canonical');
		
		if (!empty($this->item->metadesc))
		{
			$this->document->setDescription($this->item->metadesc);
		}
		else
	*/
		if (!empty($metadesc)) 
		{
			$this->document->setDescription($metadesc);
		}
	/*
		if (!empty($this->item->metakey))
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		else
		*/
		if (!empty($metakeys)) 
		{
			$this->document->setMetadata('keywords', $metakeys);
		}
		
		if ($app->input->get('filtering', false)) {
			$this->document->setMetadata('robots', 'noindex, follow');
		} else if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		$this->document->addCustomTag('<meta property="og:title" content="'.trim($title).'" />');
		$this->document->addCustomTag('<meta property="og:url" content="'.JRoute::_(DJEventsHelperRoute::getEventsListRoute()).'" />');
/*		if ($item_images = DJEventsImageHelper::getImages('category',$this->item->id)) {
			if (isset($item_images[0])) {
				$this->document->addCustomTag('<meta property="og:image" content="'.$item_images[0]->large.'" />');
				//$this->document->setMetadata('og:image', $item_images[0]->large);
			}
		}
		
		if ($this->params->get('rss_enabled', '1') == '1') {
			$this->feedlink =  JRoute::_(DJCatalogHelperRoute::getCategoryRoute($this->item->catslug, $pid).'&format=feed&type=rss&limitstart=0');
			//$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_( DJCatalogHelperRoute::getCategoryRoute($this->item->catslug, $pid) . '&format=feed&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_( DJCatalogHelperRoute::getCategoryRoute($this->item->catslug, $pid) . '&format=feed&type=atom'), 'alternate', 'rel', $attribs);
		}
	*/
		
	}

}
