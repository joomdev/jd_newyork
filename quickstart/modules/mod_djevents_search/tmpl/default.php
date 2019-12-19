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

$input = JFactory::getApplication()->input;
?>

<form action="<?php echo JRoute::_('index.php?option=com_djevents&task=search'); ?>" method="post" name="djevents-search-form" class="djev_search <?php echo $params->get('search_layout',0) ? 'djev_search_horz':'djev_search_vertical' ?>">
	<fieldset class="djev_mod_search djev_clearfix">
		
		<?php if($params->get('show_input', 1)) { ?>
			<div class="djev_search_el djev_search_field">
				<input type="text" name="search" class="input-large form-control" value="<?php echo $input->getString('search'); ?>" placeholder="<?php echo JText::_('COM_DJEVENTS_SEARCH_PLACEHOLDER') ?>" />
			</div>
		<?php } ?>
		
		<?php if($params->get('show_category', 1)) { ?>
			<div class="djev_search_el djev_search_category">
				<select name="cid" class="input-large form-control" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJEVENTS_SELECT_CATEGORY');?></option>
					<?php 
						echo JHtml::_('select.options', $options['categories'], 'value', 'text', $input->get('cid'), true);
					?>
				</select>
			</div>
		<?php } ?>
		
		<?php if($params->get('show_city', 1)) { ?>
			<div class="djev_search_el djev_search_city">
				<select name="city" class="input-large form-control" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJEVENTS_SELECT_CITY');?></option>
					<?php 
						echo JHtml::_('select.options', $options['cities'], 'value', 'text', $input->get('city'), true);
					?>
				</select>
			</div>
		<?php } ?>
		
		<?php if($params->get('show_from', 1)) { ?>
			<div class="djev_search_el djev_search_from">
				<?php echo JHtml::calendar($input->get('from'), 'from', 'from'.$module->id, '%Y-%m-%d', array('class'=>'input-small', 'placeholder'=>JText::_('COM_DJEVENTS_SEARCH_FROM'))); ?>
			</div>
		<?php } ?>	
			
		<?php if($params->get('show_to', 1)) { ?>
			<div class="djev_search_el djev_search_to">
				<?php echo JHtml::calendar($input->get('to'), 'to', 'to'.$module->id, '%Y-%m-%d', array('class'=>'input-small', 'placeholder'=>JText::_('COM_DJEVENTS_SEARCH_TO'))); ?>
			</div>
		<?php } ?>
		
		<div class="djev_search_el djev_search_button">
				<input type="submit" class="btn btn-primary" value="<?php echo JText::_('COM_DJEVENTS_SEARCH'); ?>" />
		</div>
		
	</fieldset>
    
    <input type="hidden" name="option" value="com_djevents" />
	<input type="hidden" name="view" value="eventslist" />
	<input type="hidden" name="task" value="search" />
	<input type="submit" style="display: none;"/>
</form>
