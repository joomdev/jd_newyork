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

$date = JFactory::getDate($params->get('start_date','now'));
$days = $date->format('t');	
$first = (int) JFactory::getDate($date->format('Y-m').'-01')->format('N');
$weekday = 1;
$today = JFactory::getDate()->format('Y-m-d');
?>

<div class="djev_calendar_month" data-baseurl="<?php echo JUri::base(); ?>" data-module="<?php echo $params->get('module',0) ?>" data-tooltip-open="<?php echo $params->get('tooltip_open','mouseenter') ?>" data-tooltip-close="<?php echo $params->get('tooltip_close','click') ?>" data-tooltip-position="<?php echo $params->get('tooltip_position','top') ?>">
	<div class="djev_calendar_wrap" data-month="<?php echo $date->format('Y-m')?>">
		<div class="djev_calendar_head">
			<?php echo $date->format('F Y') ?>
		</div>
		<table class="djev_calendar_table">
			<tr class="djev_table_head">
				<th><?php echo JText::_('MON'); ?></th>
				<th><?php echo JText::_('TUE'); ?></th>
				<th><?php echo JText::_('WED'); ?></th>
				<th><?php echo JText::_('THU'); ?></th>
				<th><?php echo JText::_('FRI'); ?></th>
				<th><?php echo JText::_('SAT'); ?></th>
				<th><?php echo JText::_('SUN'); ?></th>
			</tr>
			<tr><?php while($weekday < $first) { echo '<td> </td>'; $weekday++; } ?>
			<?php for($d = 1; $d <= $days; $d++) { ?>
					<?php 
					$current = $date->format('Y-m').'-'.str_pad($d, 2, "0", STR_PAD_LEFT);
					$class = ($today == $current ? ' today' : '');
					$link = JRoute::_(DJEventsHelperRoute::getEventsListRoute().'&from='.$current.'&to='.$current);
					
					ob_start();
					require JModuleHelper::getLayoutPath('mod_djevents_calendar', 'day_events');
					$content = ob_get_clean();
					
					if(!empty($content)) {
						$class .= ' active-day';
					}
					?>
					<td class="djev_calendar_table_day<?php echo $class ?>">
						<?php if(!empty($content)) { ?>
							<a href="<?php echo $link ?>" data-content="<?php echo htmlspecialchars($content); ?>"><?php echo $d ?></a>
						<?php } else { ?>
							<span><?php echo $d ?></span>
						<?php } ?>
					</td>
			<?php
				if($weekday % 7 == 0) {
					echo '</tr>'; 
					if($d<$days) echo '<tr>';
					$weekday = 1; 
				} else if($d == $days) {
					while($weekday < 7) { echo '<td> </td>'; $weekday++; }
					echo '</tr>';
				} else {
					$weekday++;
				}
			}
			?>
			
		</table>
	</div>
	
	<div class="djev_navi">
		<a class="prev-month" href="#" aria-label="<?php echo JText::_('MOD_DJEVENTS_CALENDAR_PREV_MONTH') ?>" title="<?php echo JText::_('MOD_DJEVENTS_CALENDAR_PREV_MONTH') ?>"><span class="fa fa-angle-left" aria-hidden="true"></span></a>
		<a class="next-month" href="#" aria-label="<?php echo JText::_('MOD_DJEVENTS_CALENDAR_NEXT_MONTH') ?>" title="<?php echo JText::_('MOD_DJEVENTS_CALENDAR_NEXT_MONTH') ?>"><span class="fa fa-angle-right" aria-hidden="true"></span></a>
	</div>
	
	<div class="djev_loader"><span class="fa fa-refresh fa-spin fa-3x" aria-hidden="true"></span></div>
	
	<?php if($params->get('show_link')) { ?>
		<div class="djev_calendar_more">
			<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute()); ?>"><?php echo JText::_('MOD_DJEVENTS_CALENDAR_SHOW_ALL') ?></a>
		</div>
	<?php } ?>
	
</div>
