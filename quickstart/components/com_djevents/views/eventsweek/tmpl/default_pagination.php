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

$app = JFactory::getApplication();

$fromDate = JFactory::getDate($this->state->get('filter.from'));

$prevFromDate = JFactory::getDate($fromDate .' -7 days')->format('Y-m-d');
$nextFromDate = JFactory::getDate($fromDate .' +7 days')->format('Y-m-d');
?>

<?php if($this->params->get('weekly_buttons', 1) || $this->params->get('weekly_list_switcher', 1)) { ?>
<div class="djev_pagination pagination djev_clearfix text-center">
	<?php if($this->params->get('weekly_buttons', 1)) { ?>
		<a class="btn btn-primary pull-left djev_week_prev" href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsWeekRoute($prevFromDate, $app->input->get('cid'), $app->input->get('city'))); ?>"><?php echo JText::_('COM_DJEVENTS_PREVIOUS_WEEK'); ?></a>
		<a class="btn btn-primary pull-right djev_week_next" href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsWeekRoute($nextFromDate, $app->input->get('cid'), $app->input->get('city'))); ?>"><?php echo JText::_('COM_DJEVENTS_NEXT_WEEK'); ?></a>
	<?php } ?>
	<?php if($this->params->get('weekly_list_switcher', 1)) { ?>
		<a class="btn djev_list_link" href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute($app->input->get('cid'), $app->input->get('city')));?>"><?php echo JText::_('COM_DJEVENTS_BROWSE_ALL_EVENTS');?></a>
	<?php } ?>
</div>
<?php } ?>