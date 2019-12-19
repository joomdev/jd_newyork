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

class DJEventsViewEventsWeek extends JViewLegacy {
	
	protected $filterHeading = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->_addPath('template', JPATH_COMPONENT.  '/themes/bootstrap/views/eventsweek');
		$params = DJEventsHelper::getParams();
		$theme = $params->get('theme', 'bootstrap');
		if ($theme && $theme != 'bootstrap') {
			$this->_addPath('template', JPATH_COMPONENT.  '/themes/'.$theme.'/views/eventsweek');
		}
	}
	
	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		$this->events		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->tag 			= $this->get('Tag');
		$this->params		= $app->getParams('com_djevents');
		
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
		
		$this->items = array();
		
		$fromDate = JFactory::getDate($this->state->get('filter.from'));
		$toDate = JFactory::getDate($this->state->get('filter.to'));
		
		/* prepare poster image */
		foreach($this->events as &$item) {
			
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
			
			$tmp = explode(' ', $item->start);
			$start = JFactory::getDate(@$tmp[0]);
			
			if($start->toUnix() < $fromDate->toUnix()) $start = $fromDate;

			$tmp = explode(' ', $item->end);
			$end = JFactory::getDate(@$tmp[0]);
			if($end->toUnix() > $toDate->toUnix()) $end = $toDate;
			//echo '<h4>'.$start->format('Y-m-d').' - '.$end->format('Y-m-d').'</h4>';
			$end = $end->toUnix();
			
			while($start->toUnix() <= $end) {
				$day = $start->format('Y-m-d');
				if(!isset($this->items[$day])) $this->items[$day] = array();
				$this->items[$day][] = clone($item);
				$start = JFactory::getDate($day.' +1 day');
			}
		}
		unset($item);
		
		// set custom heading if events are filtered with search module
		$headings = array();
		if ($fromDate->format('Y') == $toDate->format('Y')) {
			if ($fromDate->format('m') == $toDate->format('m')) {
				$headings[] = $fromDate->format('j') .'-'.$toDate->format('j') . ' ' . JText::_($toDate->format('F')) . ' ' . $toDate->format('Y');
			} else {
				$headings[] = $fromDate->format('j F') .' - '.$toDate->format('j F') . ', ' . $toDate->format('Y');
			}
		} else {
			$headings[] = $fromDate->format('j F Y') . ' - ' . $toDate->format('j F Y');
		}		
		if($city = $app->input->getInt('city')) {
			$cityName = $db->setQuery('SELECT name FROM #__djev_cities WHERE id='.$city)->loadResult();
			$headings[] = JText::sprintf('COM_DJEVENTS_CITY_FILTERING_HEADING', $cityName);
		}
		if($category = $app->input->getInt('cid')) {
			$headings[] = JText::sprintf('COM_DJEVENTS_CATEGORY_FILTERING_HEADING', $this->categories[$category]->name);
		}
		
		// merge headings
		if(count($headings) > 0) {
			$headings = implode(', ', $headings);
			$this->filterHeading = JText::sprintf('COM_DJEVENTS_WEEK_FILTERING_HEADING', $headings);
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
