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
$user		= JFactory::getUser();
$edit_auth = ($user->authorise('core.edit', 'com_djevents') || ($user->authorise('event.edit.own', 'com_djevents') && $user->id == $this->item->created_by)) ? true : false;

$nullDate = JFactory::getDbo()->getNullDate();
?>

<div id="djevents" class="djev_clearfix djev_event<?php echo $this->params->get( 'pageclass_sfx' ).' djev_theme_'.$this->params->get('theme','default'); if ($this->item->featured == 1) echo ' featured_item'; ?>">
	
	<?php if($this->params->get('event_map',1) && $this->item->latitude!='0.000000000000000' && $this->item->longitude!='0.000000000000000'){ ?>
		<div id="gmap" style="width: 100%; height: 300px"></div>
		<script type="text/javascript">
			jQuery(window).on('load', function(){
				var adLatlng = new google.maps.LatLng(<?php echo $this->item->latitude.','.$this->item->longitude; ?>);
				var MapOptions = {
					zoom: <?php echo $this->item->zoom; ?>,
					center: adLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					navigationControl: true,
					scrollwheel: false
				};
			    var map = new google.maps.Map(document.getElementById("gmap"), MapOptions);

			    var markerOptions = {
					position: adLatlng,
					map: map,
					disableAutoPan: false,
					animation: google.maps.Animation.DROP,
					draggable: false
				};
				var marker = new google.maps.Marker(markerOptions);
			});
		</script>
	<?php } ?>
	
	<div class="djev_event_info" style="width: <?php echo (int)$this->params->get('width', 300) ?>px"><?php 
		
		/* 
		// this big poster image can be also used in the layout
		if(!empty($this->item->thumb)) { ?>
		<div class="djev_big_poster">
			<img src="<?php echo $this->item->thumb; ?>" alt="<?php $this->escape($this->item->thumb_title) ?>" />
		</div>
		<?php } */ ?><?php
		 
		if (!empty($this->images)) {
			echo $this->loadTemplate('images'); 
		}
		
	?></div>
	
	<?php if($this->params->get('event_title',1)) { ?>
	<h1 class="djev_title">
		<?php echo $this->item->title; ?>
		<?php if ($edit_auth) { ?>
			<a class="btn btn-mini button djev_edit_button" href="<?php echo JRoute::_('index.php?option=com_djevents&task=eventform.edit&id='.$this->item->id); ?>"><?php echo JText::_('COM_DJEVENTS_EDIT')?></a>
		<?php } ?>
	</h1>
	<?php } ?>

	<?php if( !empty($this->params->get('event_social_code')) && 'top' == $this->params->get('event_social_position') ) { ?>
	<div class="djev_social djev_social_top">
		<?php echo $this->params->get('event_social_code'); ?>
	</div>
	<?php } ?>

	<?php if($this->params->get('event_time',1)) { ?>
	<h3 class="djev_time djev_infoline">
		<?php 
		$displayData = array('item' => $this->item, 'params' => $this->params);
		$layout = new JLayoutFile('com_djevents.event_time', null, array('component'=> 'com_djevents'));
		echo $layout->render($displayData); ?>
	</h3>
	<?php } ?>

	<?php if($this->params->get('event_location',1) && !empty($this->item->displayLocation)) { ?>
	<h3 class="djev_location djev_infoline">
		<span class="fa fa-map-marker"></span>
		<span class="djev_location_val"><?php echo $this->item->displayLocation?></span>
	</h3>
	<?php } ?>
	
	<?php if($this->params->get('event_price',1) && !empty($this->item->price)) { ?>
	<h3 class="djev_price djev_infoline">
		<span class="fa fa-ticket"></span>
		<span class="djev_price_val"><?php echo str_replace("\n", ' / ', $this->item->price) ?></span>
	</h3>
	<?php } ?>
	
	<?php if($this->params->get('event_url',1) && !empty($this->item->external_url)) { ?>
	<h3 class="djev_url djev_infoline">
		<span class="fa fa-link"></span>
		<span class="djev_url_val"><a href="<?php echo $this->item->external_url ?>" target="_blank"><?php echo JText::_('COM_DJEVENTS_EXTERNAL_URL_TEXT') ?></a></span>
	</h3>
	<?php } ?>
	
	<?php if($this->params->get('event_category',1)) { ?>
	<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute($this->item->cat_id))?>" class="djev_category" style="<?php echo $this->item->category_style ?>">
		<?php if($this->item->icon_type == 'fa') { ?>
			<span class="<?php echo $this->item->fa_icon ?>" aria-hidden="true"></span>
		<?php } else if($this->item->icon_type == 'image') { ?>
			<img src="<?php echo JURI::root(true).'/'.$this->item->image_icon ?>" alt="" />
		<?php } ?>
		<span><?php echo $this->item->category_name ?></span>
	</a>
	<?php } ?>
	
	<div class="djev_description">
		<?php if ($this->params->get('event_intro', 0)) { ?>
		<div class="djev_introtext">
			<?php echo JHTML::_('content.prepare', $this->item->intro); ?>
		</div>
		<?php } ?>
		
		<div class="djev_fulltext">
			<?php echo JHTML::_('content.prepare', $this->item->description); ?>
		</div>
		
		<?php if($this->params->get('event_tags',1) && !empty($this->tags)) { ?>
		<div class="djev_tags">
			<div class="djev_tags_label muted"><?php echo JText::_('COM_DJEVENTS_TAGS') ?>:</div>
			<?php foreach($this->tags as $tag) { ?>
				<a class="djev_tag btn btn-small" href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute().'&tag='.$tag->id.':'.$tag->alias); ?>"><?php echo $tag->name ?></a>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<?php if( !empty($this->params->get('event_social_code')) && 'bottom' == $this->params->get('event_social_position') ) { ?>
	<div class="djev_social djev_social_bottom">
		<?php echo $this->params->get('event_social_code'); ?>
	</div>
	<?php } ?>

</div>