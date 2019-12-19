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
?>

<div class="djev_calendar_days" data-baseurl="<?php echo JUri::base(); ?>" data-module="<?php echo $params->get('module',0) ?>" data-tooltip-open="<?php echo $params->get('tooltip_open','mouseenter') ?>" data-tooltip-close="<?php echo $params->get('tooltip_close','click') ?>" data-tooltip-position="<?php echo $params->get('tooltip_position','top') ?>">
	<?php
	$date = JFactory::getDate($params->get('start_date','now'));
	$today = JFactory::getDate()->format('Y-m-d');
	$start = $date->format('Y-m-d');
	$days = $params->get('days', 3);
	?>
		
	<div class="djev_calendar_scroll">
		<div class="djev_calendar_scroll_in">
		<?php
			for($d = 0; $d < $days; $d++) {
								
				$current = $d ? JFactory::getDate($start.' +'.$d.' days')->format('Y-m-d') : $start;
				
				$class = (int)JHtml::_('date', $current, 'N') == 7 ? ' sunday':'';
				$class.= ($today == $current ? ' today':'');
				
				$link = JRoute::_(DJEventsHelperRoute::getEventsListRoute().'&from='.$current.'&to='.$current);
				
				ob_start();
				require JModuleHelper::getLayoutPath('mod_djevents_calendar', 'day_events');
				$content = ob_get_clean();
				
				?>
				<a href="<?php echo $link ?>" class="djev_calendar_day<?php echo $class . (!empty($content) ? ' active-day" 
						data-content="'.htmlspecialchars($content):''); ?>" data-date="<?php echo $current;?>">
					<span class="djev_calendar_day_in">
						<span class="week_day">
							<?php echo JHtml::_('date', $current, 'l'); ?>
						</span>
						<span class="day">
							<?php echo JHtml::_('date', $current, 'd'); ?>
						</span>
						<span class="month">
							<?php echo JHtml::_('date', $current, 'M'); ?>
						</span>
					</span>
				</a>
			<?php
			} ?>
		</div>
	</div>
		
	<div class="djev_navi">
		<a class="prev-days" href="#" aria-label="<?php echo JText::_('MOD_DJEVENTS_CALENDAR_PREV_DAYS') ?>" title="<?php echo JText::_('MOD_DJEVENTS_CALENDAR_PREV_DAYS') ?>"><span class="fa fa-angle-left" aria-hidden="true"></span></a>
		<a class="next-days" href="#" aria-label="<?php echo JText::_('MOD_DJEVENTS_CALENDAR_NEXT_DAYS') ?>" title="<?php echo JText::_('MOD_DJEVENTS_CALENDAR_NEXT_DAYS') ?>"><span class="fa fa-angle-right" aria-hidden="true"></span></a>
	</div>
	
	<div class="djev_loader"><span class="fa fa-refresh fa-spin fa-3x" aria-hidden="true"></span></div>
	
	<?php if($params->get('show_link')) { ?>
		<div class="djev_calendar_more">
			<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute()) ?>"><?php echo JText::_('MOD_DJEVENTS_CALENDAR_SHOW_ALL') ?></a>
		</div>
	<?php } ?>

</div>
<?php 