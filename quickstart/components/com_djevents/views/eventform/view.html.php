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

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class DJEventsViewEventform extends JViewLegacy {
	
	protected $state;
	protected $item;
	protected $form;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->_addPath('template', JPATH_COMPONENT.  '/themes/bootstrap/views/eventform');
		$params = DJEventsHelper::getParams();
		$theme = $params->get('theme', 'bootstrap');
		if ($theme && $theme != 'bootstrap') {
			$this->_addPath('template', JPATH_COMPONENT.  '/themes/'.$theme.'/views/eventform');
		}
	}
	
	public function display($tpl = null)
	{
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->params = DJEventsHelper::getParams();
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$authorised = false;
		if (empty($this->item->id)) {
			$authorised = $user->authorise('core.create', 'com_djevents') || $user->authorise('event.create', 'com_djevents');
		}
		else {
			if ($user->authorise('core.edit', 'com_djevents')) {
				$authorised = true;
			} else {
				$ownerId	= (int) $this->item->created_by;
				if (!$user->guest && $ownerId == $user->id && $user->authorise('event.edit.own', 'com_djevents')) {
					$authorised = true;
				}
			}
		}
		
		if ($authorised !== true) {
			if ((bool)$user->guest && empty($this->item->id)) {
				$return_url = base64_encode(JURI::getInstance()->toString());
				$app->enqueueMessage(JText::_('COM_DJEVENTS_PLEASE_LOGIN'), 'notice');
				$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return_url, false));
				return true;
			} else {
				$app->enqueueMessage(JText::_('COM_DJEVENTS_NO_PERMISSIONS'), 'error');
				$app->redirect(JRoute::_(DJEventsHelperRoute::getEventsListRoute(), false));
				return true;
			}
		}
		
		if($this->item->id) {
		
			$this->items = $this->get('Images');
		
			foreach($this->items as $item) {
				if(!$item->thumb = DJEventsImageResizer::createThumbnail($item->image, 'media/djevents/images', 200, 150, 'crop', 80)) {
					$item->thumb = $item->image;
				}
				if(strcasecmp(substr($item->image, 0, 4), 'http') != 0 && !empty($item->image)) {
					$item->image = JURI::root(true).'/'.$item->image;
				}
				if(strcasecmp(substr($item->thumb, 0, 4), 'http') != 0 && !empty($item->thumb)) {
					$item->thumb = JURI::root(true).'/'.$item->thumb;
				}
			}
				
			$this->tags	= $this->get('Tags');
		}
		
		$params = JComponentHelper::getParams( 'com_djevents' );
		
		JHtml::_('jquery.framework');
		DJEventsHelper::setAssets();
		
		$this->document->addScript(JURI::base(true).'/administrator/components/com_djevents/assets/js/album.js');
		$api_key = $this->params->get('map_api_key','');
		if(!empty($api_key)) $api_key = '&key='.$api_key;
		$this->document->addScript('//maps.google.com/maps/api/js?sensor=false'.$api_key);
		$this->document->addScript(JURI::base(true).'/administrator/components/com_djevents/assets/js/googlemap.js');
		
		$latitude = $this->item->id ? $this->item->latitude : $params->get('map_latitude', '51.76745147292665');
		$longitude = $this->item->id ? $this->item->longitude : $params->get('map_longitude', '19.456850811839104');
		$zoom = $this->item->id ? $this->item->zoom : $params->get('map_zoom', '15');
		
		$this->document->addScriptDeclaration("
			jQuery(window).on('load', function(){
				new DJEventsMap('gmap',{latitude: $latitude, longitude: $longitude, zoom: $zoom });
				
				/* fix showon feature for the frontend editing */
				jQuery('[data-showon]').each(function() {
					var target = jQuery(this), jsondata = jQuery(this).data('showon');
					
					// Attach events to referenced element
					jQuery.each(jsondata, function(j, item) {
						var fields = jQuery('[name=\"' + jsondata[j]['field'] + '\"], [name=\"' + jsondata[j]['field'] + '[]\"]');
						
						fields.each(function() {
							var field = jQuery(this);							
							field.parent().click(function(){
								field.trigger('change');
							});
						});
					});
				});
			});
		");
		
		$settings = array();
		$settings['max_file_size'] = $params->get('upload_max_size','10240').'kb';
		$settings['chunk_size'] = $params->get('upload_chunk_size','1024').'kb';
		$settings['resize'] = true;
		$settings['width'] = $params->get('upload_width','1600');
		$settings['height'] = $params->get('upload_height','1200');
		$settings['quality'] = $params->get('upload_quality','90');
		$settings['filter'] = 'jpg,png,gif';
		$settings['onUploadedEvent'] = 'injectUploaded';
		$settings['onAddedEvent'] = 'startUpload';
		//$settings['debug'] = true;
		$this->uploader = DJEventsUploadHelper::getUploader('uploader', $settings);
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	
	protected function _prepareDocument() {
		$app		= JFactory::getApplication();

		$title = ($this->item->id > 0) ? JText::sprintf('COM_DJEVENTS_EVENT_EDIT_HEADING', $this->item->title) : JText::_('COM_DJEVENTS_EVENT_SUBMISSION_HEADING');
		
		$this->params->set('page_heading', $title);
		
		
		if ($app->getCfg('sitename_pagetitles', 0)) {
			if ($app->getCfg('sitename_pagetitles', 0) == '2') {
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			} else {
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) 
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) 
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
?>