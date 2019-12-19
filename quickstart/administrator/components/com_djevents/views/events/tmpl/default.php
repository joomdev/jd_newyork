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

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$featured_states = array(
		0	=> array(
				'task'				=> 'featured',
				'text'				=> '',
				'active_title'		=> 'COM_DJEVENTS_FEATURED',
				'inactive_title'	=> 'COM_DJEVENTS_FEATURED',
				'tip'				=> true,
				'active_class'		=> 'unfeatured',
				'inactive_class'	=> 'featured'
		),
		1	=> array(
				'task'				=> 'unfeatured',
				'text'				=> '',
				'active_title'		=> 'COM_DJEVENTS_FEATURED',
				'inactive_title'	=> 'COM_DJEVENTS_FEATURED',
				'tip'				=> true,
				'active_class'		=> 'featured',
				'inactive_class'	=> 'unfeatured'
		)
);

?>
<form action="<?php echo JRoute::_('index.php?option=com_djevents&view=events');?>" method="post" name="adminForm" id="adminForm">
<div class="<?php echo $this->classes->row ?>">
	<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="<?php echo $this->classes->col ?>2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="<?php echo $this->classes->col ?>10">
	<?php else: ?>
	<div id="j-main-container">
	<?php endif;?>	
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>"  />
			</div>
			<div class="btn-group pull-left">
				<?php echo JHtml::calendar($this->state->get('filter.from'), 'filter_from', 'filter_from', '%Y-%m-%d', array('class'=>'input-small', 'placeholder'=>JText::_('COM_DJEVENTS_SEARCH_FROM'))); ?>
			</div>
			<div class="btn-group pull-left">
				<?php echo JHtml::calendar($this->state->get('filter.to'), 'filter_to', 'filter_to', '%Y-%m-%d', array('class'=>'input-small', 'placeholder'=>JText::_('COM_DJEVENTS_SEARCH_TO'))); ?>
			</div>
			
			<div class="btn-group pull-left">
				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn" onclick="jQuery('#filter_search').val(''); jQuery('#filter_from').val(''); jQuery('#filter_to').val(''); this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			
			<div class="btn-group pull-right hidden-phone">
				<select name="filter_city" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJEVENTS_SELECT_OPTION_CITY');?></option>
					<?php 
						echo JHtml::_('select.options', $this->cities, 'value', 'text', $this->state->get('filter.city'), true);
					?>
				</select>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<select name="filter_category" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJEVENTS_SELECT_OPTION_CATEGORY');?></option>
					<?php 
						echo JHtml::_('select.options', $this->categories, 'value', 'text', $this->state->get('filter.category'), true);
					?>
				</select>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<select name="filter_published" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJEVENTS_SELECT_OPTION_STATE');?></option>
					<?php 
						echo JHtml::_('select.options',array(JHtml::_('select.option', '1', 'JPUBLISHED'),JHtml::_('select.option', '0', 'JUNPUBLISHED'),JHtml::_('select.option', '-1', 'JARCHIVED')), 'value', 'text', $this->state->get('filter.published'), true);
					?>
				</select>
			</div>
			
		</div>
		<div class="clearfix"> </div>
		
		<div class="alert alert-info"><?php echo JText::_('COM_DJEVENTS_PAST_EVENTS_HINT');?><a class="close" data-dismiss="alert" href="#">&times;</a></div>
		
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="25%">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_EVENT', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_EVENT_START_DATE', 't.start', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_EVENT_END_DATE', 't.end', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_CATEGORY', 'c.name', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_DJEVENTS_EVENT_CREATED', 'a.created', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'events.', $canCheckin); ?>
						<?php endif; ?>
						<?php if (!$canCheckin): ?>
							<?php echo $this->escape($item->title); ?>
						<?php else: ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_DJEVENTS_EDIT_TOOLTIP' );?>::<?php echo $this->escape($item->title); ?>">
							<a href="<?php echo JRoute::_('index.php?option=com_djevents&task=event.edit&id='.$item->id);?>">
								<?php echo $this->escape($item->title); ?></a>
							</span>
						<?php endif; ?>
					</td>
					<td class="nowrap">
						<?php echo JFactory::getDate($item->start)->format('Y-m-d'); ?>
						<span <?php echo $item->start_time ? '':'style="color: #ddd;"';?>>
						<?php echo JFactory::getDate($item->start)->format('H:i'); ?></span>
					</td>
					<td class="nowrap">
						<?php echo JFactory::getDate($item->end)->format('Y-m-d'); ?>
						<span <?php echo $item->end_time ? '':'style="color: #ddd;"';?>>
						<?php echo JFactory::getDate($item->end)->format('H:i'); ?></span>
					</td>
					<td class="center">
						<?php echo $item->category_name; ?>
					</td>
					<td class="nowrap">
						<?php echo JHtml::_('date', $item->created, 'Y-m-d H:i:s'); ?>
					</td>
					<td class="center">
						<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'events.', true, 'cb'	); ?>
						<?php echo JHtml::_('jgrid.state', $featured_states, (bool)$item->featured, $i, 'events.', true); ?>
						</div>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
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
	</div>
</div>
</form>
