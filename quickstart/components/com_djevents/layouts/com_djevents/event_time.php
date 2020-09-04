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

$item 	= $displayData['item'];
$params = $displayData['params'];

$start = JFactory::getDate($item->start);
$end = JFactory::getDate($item->end); ?>
	
<span class="djev_time_icon fa fa-clock-o"></span>
<span class="djev_time_from">
	<?php echo ucfirst($start->format($params->get('date_format','l, d f Y'))); ?>
	<?php if($item->start_time) {
		echo JText::_('COM_DJEVENTS_AT') .' '. $start->format($params->get('time_format', 'h:i a')); 
	} ?>
</span>
<?php if($start->format('Ymd')!=$end->format('Ymd')) { ?>
<span class="djev_time_to">
	<span class="djev_time_sep">-</span>
	<?php echo $end->format($params->get('date_format','l, d F Y')); ?>
	<?php if($item->end_time) echo JText::_('COM_DJEVENTS_AT') .' '. $end->format($params->get('time_format', 'h:i a')); ?>
</span>
<?php } else if($item->end_time && $start->format('Hi')!=$end->format('Hi')) { ?>
<span class="djev_time_to">
	<span class="djev_time_sep">-</span>
	<?php if($item->end_time) echo $end->format($params->get('time_format', 'h:i a')); ?>
</span>
<?php }