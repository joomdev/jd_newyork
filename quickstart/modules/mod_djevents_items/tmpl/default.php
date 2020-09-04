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

$cols = (int)$params->get('columns', 1);
$width = intval(12 / $cols);
?>

<div class="djev_mod_items">

	<div class="djev_mod_items_in">
	
		<div class="<?php echo $classes->row ?>">
		<?php foreach ($items as $key => $item) { 
		
			if($key && $key % $cols == 0) { ?>
			</div><div class="<?php echo $classes->row ?>">
			<?php } ?>
			
			<div class="<?php echo $classes->col.$width ?>">
			
			<div class="djev_item djev_clearfix <?php echo $item->featured == 1 ? 'djev_featured':'' ?><?php echo ((!$params->get('image', 1) || empty($item->thumb)) ? 'noimage':'') ?>">
				
				<?php if($params->get('image', 1) && !empty($item->thumb)) { ?>
				<div class="djev_image_wrap">
					<a href="<?php echo $item->link ?>">
						<img class="djev_image" src="<?php echo $item->thumb; ?>" alt="<?php echo htmlspecialchars($item->thumb_title) ?>" />
					</a>
				</div>
				<?php } ?>
				
				<div class="djev_info djev_clearfix">
					<?php if(!empty($item->online_event)) { ?>
						<span class="djev_city online-event"><?php echo JText::_('COM_DJEVENTS_ONLINE_EVENT'); ?></span>
					<?php }elseif($params->get('city', 1) && (!empty($item->city_name) || $item->location)) { ?>
						<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute(0, $item->city_id))?>" class="djev_city">
							<i class="fa fa-map-marker"></i> <?php echo (!empty($item->city_name) ? $item->city_name : $item->location) ?>
						</a>
					<?php } ?>
					
					<?php
					if($params->get('category', 1)) {
						$category = $categories[$item->cat_id];
					?>
					<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute($item->cat_id, 0))?>" class="djev_category" style="<?php echo $category->style ?>">
					<?php if($category->icon_type == 'fa') { ?>
						<span class="<?php echo $category->fa_icon ?>" aria-hidden="true"></span>
					<?php } else if($category->icon_type == 'image') { ?>
						<img src="<?php echo JURI::root(true).'/'.$category->image_icon ?>" alt="" />
					<?php } ?>
						<span><?php echo $item->category_name ?></span>
					</a>
					<?php } ?>
				</div>
				
				<div class="djev_item_content">
				
					<?php if($params->get('title', 1)) { ?>
					<h4 class="djev_item_title">
						<a href="<?php echo $item->link ?>">
							<?php echo JHtml::_('string.truncate', $item->title, $params->get('title_limit', 50), true, false); ?></a>
					</h4>
					<?php } ?>
					
					<div class="djev_item_info">
						
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
						
						<?php if($params->get('readmore', 1)) { ?>
						<div class="djev_readmore">
							<a href="<?php echo $item->link ?>" class="btn btn-primary"><?php echo JText::_('MOD_DJEVENTS_ITEMS_READMORE') ?></a>
						</div>
						<?php } ?>
					</div>
					
				</div>
				<div class="djev_clear"></div>
			</div>
			</div>
		<?php } ?>
		</div>
		
	</div>

	<?php if($params->get('show_link')) { ?>
		<div class="djev_items_more">
			<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute()); ?>"><?php echo JText::_('MOD_DJEVENTS_ITEMS_SHOW_ALL') ?></a>
		</div>
	<?php } ?>
	
</div>
