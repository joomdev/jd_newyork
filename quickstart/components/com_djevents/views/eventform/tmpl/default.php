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

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', '#djevents select');
JHtml::_('script', 'jui/cms.js', false, true);
$user = JFactory::getUser();
?>

<h1 class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ) ?>">
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>

<div id="djevents" class="djev_eventform">

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		var form = document.getElementById('edit-form');
		if (task == 'eventform.cancel') {
			Joomla.submitform(task, form);
		} else if(document.formvalidator.isValid(form)) {
			
			if(!form.jform_city_id.value && !form.jform_city_id_new.value && jQuery('[name="jform[online_event]"]:checked').val() == '0') {
				alert('<?php echo $this->escape(JText::_('COM_DJEVENTS_CITY_VALIDATION_FAILD'));?>');
				return false;
			}
			Joomla.submitform(task, form);
		}
		else {
			alert('<?php echo $this->escape(addslashes(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')));?>');
		}
	}
</script>

<div class="btn-toolbar djev_form_toolbar">
		<button type="button" onclick="Joomla.submitbutton('eventform.save')" class="button btn btn-success">
			<?php echo JText::_('COM_DJEVENTS_SAVE') ?>
		</button>
		<?php if ($user->authorise('core.edit', 'com_djevents') || $user->authorise('event.edit.own', 'com_djevents')) { ?>
		<button type="button" onclick="Joomla.submitbutton('eventform.apply')" class="button btn">
			<?php echo JText::_('COM_DJEVENTS_APPLY') ?>
		</button>
		<?php } ?>
		<button type="button" onclick="Joomla.submitbutton('eventform.cancel')" class="button btn">
			<?php echo JFactory::getApplication()->input->get('id') > 0 ? JText::_('COM_DJEVENTS_CANCEL') : JText::_('COM_DJEVENTS_CLOSE'); ?>
		</button>
</div>

<hr />

<form action="<?php echo JRoute::_('index.php?option=com_djevents&view=eventform&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="edit-form" class="form-validate" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span12">
			
			<fieldset class="form-horizontal">
			
				<?php echo $this->form->getField('title')->renderField(); ?>
				<?php echo $this->form->getField('alias')->renderField(); ?>
				<?php echo $this->form->getField('cat_id')->renderField(); ?>
				<?php echo $this->form->getField('external_url')->renderField(); ?>
				<?php echo $this->form->getField('price')->renderField(); ?>
				<?php echo $this->form->getField('intro')->renderField(); ?>
				<?php echo $this->form->getField('description')->renderField(); ?>
				
				<div class="control-group">
					<div class="control-label">
						<label for="tags" class=""><?php echo JText::_('COM_DJEVENTS_TAGS') ?></label>
					</div>
					<div class="controls"><textarea name="tags" id="tags" class="form-control"><?php 
						echo isset($this->tags) ? implode(',', $this->tags) : '';
					?></textarea></div>
				</div>
			</fieldset>
			
			<hr />
			
			<fieldset class="form-horizontal">
				<legend><?php echo JText::_('COM_DJEVENTS_EVENT_TIMES'); ?></legend>
				<?php //echo $this->form->getControlGroup('time'); ?>
				<?php foreach ($this->form->getGroup('time') as $field) : ?>
					<?php echo $field->renderField(); ?>
				<?php endforeach; ?>
			</fieldset>
			
			<hr />
			
			<fieldset class="form-horizontal">
				<legend><?php echo JText::_('COM_DJEVENTS_MEDIA') ?></legend>
				<div class="control-group">
					<div id="albumItemsWrap" data-root="<?php echo JURI::root(true); ?>">
						
						<!-- hidden item template for JS -->
						<div class="albumItem" style="display: none;">
							<img />
							<span class="video-icon"></span>
							<label><input type="radio" name="item_poster" value="0" /> <span><?php echo JText::_('COM_DJEVENTS_POSTER')?></span></label>
							<input type="hidden" name="item_id[]" value="0">
							<input type="hidden" name="item_image[]" value="">
							<input type="text" class="editTitle" name="item_title[]" placeholder="<?php echo JText::_('COM_DJEVENTS_IMAGE_CAPTION_HINT') ?>" value="">
							<a href="#" class="delBtn"></a>
						</div>
						
						<div id="albumItems">
							<?php if(isset($this->items)) foreach($this->items as $item) { ?>
								<div class="albumItem">
									<img src="<?php echo $item->thumb; ?>" alt="<?php echo $this->escape($item->title); ?>" />
									<?php if($item->video) { ?><span class="video-icon"></span><?php } ?>
									<label><input type="radio" name="item_poster" value="<?php echo $this->escape($item->id); ?>"<?php echo $item->poster ? ' checked="checked"':'' ?>/> <span><?php echo JText::_('COM_DJEVENTS_POSTER')?></span></label>
									<input type="hidden" name="item_id[]" value="<?php echo $this->escape($item->id); ?>"/>
									<input type="hidden" name="item_image[]" value="<?php echo $this->escape($item->image); ?>"/>
									<input type="text" class="itemInput editTitle" name="item_title[]" value="<?php echo $this->escape($item->title); ?>" placeholder="<?php echo JText::_('COM_DJEVENTS_IMAGE_CAPTION_HINT') ?>" />
									<span class="delBtn"></span>
								</div>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<?php echo $this->form->getField('video')->renderField(); ?>
				<?php echo $this->uploader ?>
								
			</fieldset>
			
			<hr />
			
			<fieldset>
				<legend><?php echo JText::_('COM_DJEVENTS_LOCATION') ?></legend>
				<div class="row-fluid">
					<div class="span6">
					<?php echo $this->form->getField('online_event')->renderField(); ?>
					<script>
						jQuery(function($){
							handleMapVisibility();
							$('[name="jform[online_event]"]').change(function(){
								handleMapVisibility();
							});
							function handleMapVisibility(){
								if($('[name="jform[online_event]"]:checked').val() == '1'){
									$('#gmap').parent().hide('fast');
								}else{
									$('#gmap').parent().show('fast');
								}
							}
						});
					</script>
					<?php echo $this->form->getField('location')->renderField(); ?>
					<?php echo $this->form->getField('address')->renderField(); ?>
					<?php echo $this->form->getField('city_id')->renderField(); ?>
					<?php echo $this->form->getField('post_code')->renderField(); ?>
					<?php echo $this->form->getField('latitude')->renderField(); ?>
					<?php echo $this->form->getField('longitude')->renderField(); ?>
					<?php echo $this->form->getField('zoom')->renderField(); ?>
					</div>
					<div class="span6">
						<div id="gmap" style="width: 100%; height: 400px"></div>
						<hr />
						<div class="input-append input-group">
							<input type="text" id="google_address" class="input-xlarge form-control" value="" placeholder="<?php echo JText::_('COM_DJEVENTS_GOOGLE_ADDRESS') ?>" />
							<a id="find_address" class="btn btn-primary" href="#"><?php echo JText::_('COM_DJEVENTS_FIND') ?></a>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		
	<?php echo $this->form->getField('id')->renderField(); ?>
	<?php echo $this->form->getField('published')->renderField(); ?>
	<?php echo $this->form->getField('featured')->renderField(); ?>
	<?php echo $this->form->getField('created')->renderField(); ?>
	<?php echo $this->form->getField('created_by')->renderField(); ?>
		
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
	</div>
</form>

<hr />

<div class="btn-toolbar djev_form_toolbar">
		<button type="button" onclick="Joomla.submitbutton('eventform.save')" class="button btn btn-success">
			<?php echo JText::_('COM_DJEVENTS_SAVE') ?>
		</button>
		<?php if ($user->authorise('eventform.edit', 'com_djevents') || $user->authorise('eventform.edit.own', 'com_djevents')) { ?>
		<button type="button" onclick="Joomla.submitbutton('eventform.apply')" class="button btn">
			<?php echo JText::_('COM_DJEVENTS_APPLY') ?>
		</button>
		<?php } ?>
		<button type="button" onclick="Joomla.submitbutton('eventform.cancel')" class="button btn">
			<?php echo JFactory::getApplication()->input->get('id') > 0 ? JText::_('COM_DJEVENTS_CANCEL') : JText::_('COM_DJEVENTS_CLOSE'); ?>
		</button>
</div>

</div>