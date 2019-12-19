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

class DJEventsViewMyEvents extends JViewLegacy {
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->_addPath('template', JPATH_COMPONENT.  '/themes/bootstrap/views/myevents');
		$params = DJEventsHelper::getParams();
		$theme = $params->get('theme', 'bootstrap');
		if ($theme && $theme != 'bootstrap') {
			$this->_addPath('template', JPATH_COMPONENT.  '/themes/'.$theme.'/views/myevents');
		}
	}
	
	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		if ((bool)$user->guest) {
			$return_url = base64_encode(JURI::getInstance()->toString());
			$app->enqueueMessage(JText::_('COM_DJEVENTS_PLEASE_LOGIN'), 'notice');
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return_url, false));
			return true;
		}
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->params		= $app->getParams('com_djevents');
		
		$this->categories = $this->get('Categories');
		$this->cities = $this->get('Cities');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		/* prepare thumbnail image */
		foreach($this->items as $key => &$item) {
			
			$item->link = DJEventsHelperRoute::getEventRoute($item->id.':'.$item->alias, $item->start, $item->cat_id, $item->city_id);
			
			if(empty($item->image)) continue;
			
			if(!$item->thumb = DJEventsImageResizer::createThumbnail($item->image, 'media/djevents/images', $this->params->get('small_width'), 
					$this->params->get('small_height'), 'crop', $this->params->get('small_quality'))) {
				$item->thumb = $item->image;
			}
			if(strcasecmp(substr($item->thumb, 0, 4), 'http') != 0 && !empty($item->thumb)) {
				$item->thumb = JURI::root(true).'/'.$item->thumb;
			}
		}
		unset($item);
				
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
	
		if (!empty($metadesc)) 
		{
			$this->document->setDescription($metadesc);
		}

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
		$this->document->addCustomTag('<meta property="og:url" content="'.JRoute::_(DJEventsHelperRoute::getMyEventsRoute()).'" />');		
	}

}
