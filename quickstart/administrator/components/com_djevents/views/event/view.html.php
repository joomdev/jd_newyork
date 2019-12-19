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

jimport('joomla.application.component.view');

class DJEventsViewEvent extends JViewLegacy {
	protected $state;
	protected $item;
	protected $form;
	
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		
		$this->classes = DJEventsHelper::getBSClasses();
		
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
		
		$this->document->addScript(JURI::base(true).'/components/com_djevents/assets/js/album.js');
		$api_key = $params->get('map_api_key','');
		if(!empty($api_key)) $api_key = '&key='.$api_key;
		$this->document->addScript('//maps.google.com/maps/api/js?sensor=false'.$api_key);
		$this->document->addScript(JURI::base(true).'/components/com_djevents/assets/js/googlemap.js');
		
		$latitude = $this->item->id ? $this->item->latitude : $params->get('map_latitude', '51.76745147292665');
		$longitude = $this->item->id ? $this->item->longitude : $params->get('map_longitude', '19.456850811839104');
		$zoom = $this->item->id ? $this->item->zoom : $params->get('map_zoom', '15');
		
		$this->document->addScriptDeclaration("
			jQuery(window).on('load', function(){
				new DJEventsMap('gmap',{latitude: $latitude, longitude: $longitude, zoom: $zoom });
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
		
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);

		$text = $isNew ? JText::_( 'COM_DJEVENTS_NEW' ) : JText::_( 'COM_DJEVENTS_EDIT' );
		JToolBarHelper::title(   JText::_( 'COM_DJEVENTS_EVENT' ).': <small><small>[ ' . $text.' ]</small></small>', 'generic.png' );

		JToolBarHelper::apply('event.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('event.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('event.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		//JToolBarHelper::custom('event.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		JToolBarHelper::cancel('event.cancel', 'JTOOLBAR_CANCEL');
	}
}