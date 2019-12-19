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

defined ('_JEXEC') or die('Restricted access'); ?>

<?php if (count($this->list) > 0){ ?>
	<div class="djev_items djev_clearfix <?php echo $this->list_class ?>">
		<?php foreach ($this->list as $day => $items) { ?>
			<?php if($this->params->get('weekly_day_heading', 1)) {
				$dayLink = DJEventsHelperRoute::getEventsListRoute().'&from='.$day.'&to='.$day; ?>
				<h4 class="day"><a href="<?php echo JRoute::_($dayLink);?>"><?php echo ucfirst(JFactory::getDate($day)->format($this->params->get('weekly_day_date_format', 'l, j F'))); ?></a></h4>
			<?php } ?>
			<?php foreach ($items as $item) { ?>
			<div class="djev_item djev_clearfix <?php echo ($item->published == 1) ? '':'djev_notpublished'; ?> <?php echo $item->featured == 1 ? 'djev_featured':'' ?>">
				<div class="djev_item_in">
					<?php if($this->params->get('weekly_event_poster', 1) && !empty($item->thumb)) { ?>
					<div class="djev_image_wrap">
						<a href="<?php echo JRoute::_($item->link)?>">
							<img class="djev_image" src="<?php echo $item->thumb; ?>" alt="<?php echo $this->escape($item->thumb_title) ?>" />
						</a>
					</div>
					<?php } ?>
					<div class="djev_item_content">
						
						<?php if($this->params->get('weekly_event_title', 1)) { ?>
						<h2 class="djev_item_title">
							<a href="<?php echo JRoute::_($item->link)?>">
								<?php echo $this->escape($item->title); ?></a>
						</h2>
						<?php } ?>
						
						<?php if($this->params->get('weekly_event_time', 1)) { ?>
						<h4 class="djev_time">
							<?php 
							$displayData = array('item' => $item, 'params' => $this->params);
							$layout = new JLayoutFile('com_djevents.event_time', null, array('component'=> 'com_djevents'));
							echo $layout->render($displayData); ?>
						</h4>
						<?php } ?>
						
						<?php if($this->params->get('weekly_event_intro', 1)) { ?>
						<div class="djev_intro">
							<?php echo $item->intro; ?>
						</div>
						<?php } ?>
						
						<div class="djev_info">
							<?php if($this->params->get('weekly_event_city', 1) && (!empty($item->city_name) || $item->location)) { ?>
								<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute(0, $item->city_id))?>" class="djev_city">
									<i class="fa fa-map-marker"></i> <?php echo (!empty($item->city_name) ? $item->city_name : $item->location) ?>
								</a>
							<?php } ?>
							
							<?php if($this->params->get('weekly_event_category', 1)) {
								$category = $this->categories[$item->cat_id]; ?>
							<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute($item->cat_id, 0))?>" class="djev_category" style="<?php echo $category->style ?>">
								<?php if($category->icon_type == 'fa') { ?>
									<span class="<?php echo $category->fa_icon ?>" aria-hidden="true"></span>
								<?php } else if($category->icon_type == 'image') { ?>
									<img src="<?php echo JURI::root(true).'/'.$category->image_icon ?>" alt="" />
								<?php } ?>
								<span><?php echo $item->category_name ?></span>
							</a>
							<?php } ?>
							
							<?php if($this->params->get('weekly_event_readmore', 1)) { ?>
								<a href="<?php echo JRoute::_($item->link)?>" class="btn btn-primary pull-right"><?php echo JText::_('COM_DJEVENTS_READMORE') ?></a>
							<?php } ?>
						</div>
					</div>
					<div class="djev_clear"></div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
<?php } ?>