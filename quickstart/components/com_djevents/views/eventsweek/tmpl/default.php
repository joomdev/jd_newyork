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

<?php if($this->params->get('weekly_heading', 1)) { ?>
	<h1><?php echo $this->filterHeading; ?></h1>
<?php } ?>

<div id="djevents" class="djev_list<?php echo $this->params->get( 'pageclass_sfx' ).' djev_theme_'.$this->params->get('theme','default') ?>">

<?php if($this->params->get('weekly_buttons_position') != 'bottom') { 
	echo $this->loadTemplate('pagination');	
} ?>

<?php if (0 == count($this->items)) { ?>
	<h4><?php echo JText::_('COM_DJEVENTS_NO_ITEMS_THIS_WEEK'); ?></h4>
<?php } ?>

<?php 
$this->list = $this->items;
$this->list_class = '';

echo $this->loadTemplate('items');
?>

<?php if($this->params->get('weekly_buttons_position') != 'top') { 
	echo $this->loadTemplate('pagination');	
} ?>

</div>

<?php echo DJEventsHelper::getModules('djevents-bottom'); ?>