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

$this->list_class = '';

echo DJEventsHelper::getModules('djevents-top');

?>
<?php if ($this->filterHeading || $this->params->get( 'show_page_heading', 1)) : ?>
<h1 id="sr" class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ) ?>"><?php 
	if($this->filterHeading) {
		echo $this->filterHeading;
	} else {
		echo $this->escape($this->params->get('page_heading')); 
	} ?>
</h1>
<?php endif; ?>

<div id="djevents" class="djev_list<?php echo $this->params->get( 'pageclass_sfx' ).' djev_theme_'.$this->params->get('theme','default') ?>">

<?php if($this->params->get('list_weekly_switcher',0)) { ?>
<div class="djev_layout_change">
	<a class="btn btn-primary" href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsWeekRoute($this->weekFrom, $app->input->get('cid'), $app->input->get('city')));?>"><?php echo JText::_('COM_DJEVENTS_BROWSE_EVENTS_BY_WEEK');?></a>
</div>
<?php } ?>

<?php if(!empty($this->featured)) { 

	$this->list = $this->featured;
	$this->list_class = 'djev_items_featured';
	?>
	<?php if($this->params->get('list_featured_heading',1)) { ?>
		<h3><?php echo JText::_('COM_DJEVENTS_FEATURED_TOP_EVENTS_HEADING'); ?></h3>
	<?php } ?>
	<?php
	echo $this->loadTemplate('items'); ?>
<?php } ?>

<?php 
	$this->list = $this->items;
	$this->list_class = '';
	?>
	<?php if($this->params->get('list_heading',1)) { ?>
		<h3><?php echo JText::_('COM_DJEVENTS_EVENTS_LIST_HEADING'); ?></h3>
	<?php } ?>
	<?php
	echo $this->loadTemplate('items'); ?>

<?php if ($this->pagination->total - (count($this->featured) + count($this->items)) > 0) { ?>
<div class="djev_pagination pagination djev_clearfix">
<?php
	echo $this->pagination->getPagesLinks();
?>
</div>
<?php } ?>

</div>

<?php 
echo DJEventsHelper::getModules('djevents-bottom');
