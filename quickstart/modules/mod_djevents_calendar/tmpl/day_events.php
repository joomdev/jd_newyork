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

if(isset($items[$current])) {
	
	$i = 0;
	
	foreach($items[$current] as $item) { ?>
		<div class="djev_calendar_event">
 			<span class="time">
 			<?php echo ($item->time ? ($item->start_time ? $item->time : '') : '<i class="fa fa-repeat"></i>') ?>
 			</span>
			<a class="title" href="<?php echo $item->link ?>"><?php echo $item->title ?></a>
		</div>
	<?php 
		$i++;
		
		if($i >= (int)$params->get('events_limit', 5)) break;
	}
	
	$more = count($items[$current]) - $i;
	
	if($more > 0) { ?>
		<div class="djev_calendar_event more_events">
			<?php echo JText::sprintf('MOD_DJEVENTS_CALENDAR_AND_X_EVENTS_MORE', $more) ?>
		</div>
	<?php }
	?>
	
	<div class="djev_calendar_event center djev_calendar_day_link">
	<a href="<?php echo $link ?>">
		<?php echo JText::_('MOD_DJEVENTS_CALENDAR_SHOW_DAY') ?></a>
	</div>
	
	<?php 
}
