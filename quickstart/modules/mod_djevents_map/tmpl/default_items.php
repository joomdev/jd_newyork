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

// no direct access
defined('_JEXEC') or die ('Restricted access');

$location = array();
if(!empty($items[0]->location)) $location[] = $items[0]->location;
if(!empty($items[0]->address)) $location[] = $items[0]->address;
if(!empty($items[0]->city_name)) $location[] = $items[0]->city_name;
if(!empty($items[0]->post_code)) $location[] = $items[0]->post_code;
$location = implode(', ', $location);
?>

<div class="djev_map_items">

	<h3 class="djev_location">
		<span class="fa fa-map-marker"></span>
		<span class="djev_location_val"><?php echo $location ?></span>
	</h3>

	<?php foreach ($items as $key => $item) { ?>

	<div class="djev_item djev_clearfix <?php echo $item->featured == 1 ? 'djev_featured':'' ?><?php echo ((!$params->get('image', 1) || empty($item->thumb)) ? 'noimage':'') ?>">

		<?php if($params->get('image', 1) && !empty($item->thumb)) { ?>
		<a class="djev_image_link" href="<?php echo $item->link ?>"> <img class="djev_image"
				src="<?php echo $item->thumb; ?>"
				alt="<?php htmlspecialchars($item->thumb_title) ?>" />
		</a>
		<?php } ?>
		
		<?php if($params->get('category', 1)) {
		$category = $categories[$item->cat_id]; ?>
		<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute($item->cat_id, 0))?>" class="djev_category" style="<?php echo $category->style ?>">
			<?php if($category->icon_type == 'fa') { ?> <span
			class="<?php echo $category->fa_icon ?>" aria-hidden="true"></span> <?php } else if($category->icon_type == 'image') { ?>
			<img src="<?php echo JURI::root(true).'/'.$category->image_icon ?>"
			alt="" /> <?php } ?> <span><?php echo $item->category_name ?> </span>
		</a>
		<?php } ?>
		
		<?php if($params->get('title', 1)) { ?>
		<h4 class="djev_item_title">
			<a href="<?php echo $item->link ?>"> <?php echo JHtml::_('string.truncate', $item->title, $params->get('title_limit', 50), true, false); ?>
			</a>
		</h4>
		<?php } ?>
	
		<?php if($params->get('time', 1)) { ?>
		<h5 class="djev_time">
			<?php
			$displayData = array('item' => $item, 'params' => $params);
			$layout = new JLayoutFile('com_djevents.event_time', null, array('component'=> 'com_djevents'));
			echo $layout->render($displayData); ?>
		</h5>
		<?php } ?>

		<?php if($params->get('intro', 1) && !empty($item->intro)) { ?>
		<div class="djev_intro">
			<?php echo JHtml::_('string.truncate', $item->intro, $params->get('intro_limit', 100), true, false); ?>
		</div>
		<?php } ?>

</div>
<?php } ?>

</div>