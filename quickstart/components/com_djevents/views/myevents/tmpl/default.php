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

JHtml::_('behavior.tooltip');

$app = JFactory::getApplication();
$user = JFactory::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

echo DJEventsHelper::getModules('djevents-top');

?>
<?php if ($this->params->get( 'show_page_heading', 1)) : ?>
<h1 class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ) ?>"><?php 
	echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<div id="djevents" class="djev_list<?php echo $this->params->get( 'pageclass_sfx' ).' djev_theme_'.$this->params->get('theme','default') ?>">

	<form action="<?php echo JRoute::_(DJEventsHelperRoute::getMyEventsRoute());?>" method="post" name="adminForm" id="adminForm">

		<div id="filter-bar" class="">
			<div class="filter-search btn-group pull-left">
				<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" class="form-control" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" />
			</div>
			<div class="pull-left">
				<?php echo JHtml::calendar($this->state->get('filter.from'), 'filter_from', 'filter_from', '%Y-%m-%d', array('class'=>'input-small', 'placeholder'=>JText::_('COM_DJEVENTS_SEARCH_FROM'))); ?>
			</div>
			<div class="pull-left">
				<?php echo JHtml::calendar($this->state->get('filter.to'), 'filter_to', 'filter_to', '%Y-%m-%d', array('class'=>'input-small', 'placeholder'=>JText::_('COM_DJEVENTS_SEARCH_TO'))); ?>
			</div>
			
			<div class="btn-group pull-left">
				<button type="submit" class="btn btn-primary"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn" onclick="jQuery('#filter_search').val(''); jQuery('#filter_from').val(''); jQuery('#filter_to').val(''); this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			
			<div class="clear"></div>
			
			<div class="btn-group pull-left">
				<select name="filter_published" class="input-medium form-control" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJEVENTS_SELECT_OPTION_STATE');?></option>
					<?php 
						echo JHtml::_('select.options',array(JHtml::_('select.option', '1', 'JPUBLISHED'),JHtml::_('select.option', '0', 'JUNPUBLISHED')), 'value', 'text', $this->state->get('filter.published'), true);
					?>
				</select>
			</div>
			<div class="btn-group pull-left">
				<select name="filter_category" class="input-medium form-control" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJEVENTS_SELECT_OPTION_CATEGORY');?></option>
					<?php 
						echo JHtml::_('select.options', $this->categories, 'value', 'text', $this->state->get('filter.category'), true);
					?>
				</select>
			</div>
			<div class="btn-group pull-left">
				<select name="filter_city" class="input-medium form-control" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJEVENTS_SELECT_OPTION_CITY');?></option>
					<?php 
						echo JHtml::_('select.options', $this->cities, 'value', 'text', $this->state->get('filter.city'), true);
					?>
				</select>
			</div>
			<div class="btn-group pull-left">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			
		</div>
		<div class="clearfix"> </div>
		
		<div class="alert alert-info"><?php echo JText::_('COM_DJEVENTS_PAST_EVENTS_HINT');?><a class="close" data-dismiss="alert" href="#">&times;</a></div>
		
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="10%"></th>
					<th width="">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_EVENT', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="20%">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_EVENT_START_DATE', 't.start', $listDirn, $listOrder); ?>
					</th>
					<th width="20%">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_EVENT_END_DATE', 't.end', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_CATEGORY', 'c.name', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="10">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out==0;
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php if(!empty($item->thumb)) { ?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_DJEVENTS_EDIT' );?>::<?php echo $this->escape($item->title); ?>">
							<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventFormRoute($item->id));?>">
								<img src="<?php echo $item->thumb ?>" alt="<?php echo htmlspecialchars($item->thumb_title) ?>" />
							</a>
						</span>
						<?php } ?>
					</td>
					<td>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_DJEVENTS_EDIT' );?>::<?php echo $this->escape($item->title); ?>">
						<a href="<?php echo JRoute::_(DJEventsHelperRoute::getEventFormRoute($item->id));?>">
							<?php echo $this->escape($item->title); ?></a>
						</span>
					</td>
					<td class="nowrap">
						<div class="badge">
							<?php echo JFactory::getDate($item->start)->format('Y-m-d'); ?>
						</div>
						<?php if($item->start_time) { ?>
						<div class="badge badge-info">
							<?php echo JFactory::getDate($item->start)->format('H:i'); ?>
						</div>
						<?php } ?>
					</td>
					<td class="nowrap">
						<div class="badge">
							<?php echo JFactory::getDate($item->end)->format('Y-m-d'); ?>
						</div>
						<?php if($item->end_time) { ?>
						<div class="badge badge-info">
							<?php echo JFactory::getDate($item->end)->format('H:i'); ?>
						</div>
						<?php } ?>
					</td>
					<td class="center">
						<?php echo $item->category_name; ?>
					</td>
					<td class="center">
						
						<div class="btn-group">
						<?php if ($user->authorise('event.autopublish', 'com_djevents') || $user->authorise('core.edit.state', 'com_djevents')){ ?>
							<?php if ($item->published) { ?>
							<a class="btn btn-small btn-success" href="<?php echo JRoute::_('index.php?option=com_djevents&task=myevents.unpublish&id='.$item->id.'&'.JSession::getFormToken().'=1'); ?>">
								<span class="icon-eye-open hasTip" aria-hidden="true" title="<?php echo JText::_('COM_DJEVENTS_UNPUBLISH_EVENT') ?>"></span>
							</a>
							<?php } else { ?>
							<a class="btn btn-small btn-danger" href="<?php echo JRoute::_('index.php?option=com_djevents&task=myevents.publish&id='.$item->id.'&'.JSession::getFormToken().'=1'); ?>">
								<span class="icon-eye-close hasTip" aria-hidden="true" title="<?php echo JText::_('COM_DJEVENTS_PUBLISH_EVENT') ?>"></span>
							</a>
							<?php } ?>
						<?php } ?>
						
						<?php if ($user->authorise('core.delete', 'com_djevents') || $user->authorise('event.delete.own', 'com_djevents')) { ?>
						<a class="btn btn-small" href="<?php echo JRoute::_('index.php?option=com_djevents&task=myevents.delete&id='.$item->id.'&'.JSession::getFormToken().'=1'); ?>" 
								onclick="return confirm('<?php echo $this->escape(JText::_('COM_DJEVENTS_DELETE_CONFIRM_MSG')); ?>');">
							<span class="icon-remove hasTip" aria-hidden="true" title="<?php echo JText::_('COM_DJEVENTS_DELETE_EVENT') ?>"></span>
						</a>
						<?php } ?>
						</div>
						
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>

</div>

<?php 
echo DJEventsHelper::getModules('djevents-bottom');
