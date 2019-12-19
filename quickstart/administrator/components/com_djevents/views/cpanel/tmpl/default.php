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
?>

<form action="index.php" method="post" name="adminForm">
<div class="<?php echo $this->classes->row ?>">
<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="<?php echo $this->classes->col ?>2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="<?php echo $this->classes->col ?>10">
	<?php else: ?>
	<div id="j-main-container">
	<?php endif;?>
	<?php 
		$db = JFactory::getDBO();
		$query = 'SELECT count(id) FROM #__djev_cities WHERE id NOT IN (SELECT DISTINCT city_id FROM #__djev_events)';
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count > 0) {
			$query = 'DELETE FROM #__djev_cities WHERE id NOT IN (SELECT DISTINCT city_id FROM #__djev_events)';
			$db->setQuery($query);
			if($db->execute()) {
				JFactory::getApplication()->enqueueMessage($count.' unused cities have been removed from database');
			}
		}
	?>
		<div class="djc_control_panel clearfix">
			<div class="cpanel-left">
				<div class="cpanel">
					
						<h3><?php echo JText::_('COM_DJEVENTS_CONTENT_MANAGEMENT_HEADING') ?></h3>
					
						<div class="icon">
							<a href="index.php?option=com_djevents&amp;view=categories">
								<img alt="<?php echo JText::_('COM_DJEVENTS_CATEGORIES'); ?>" src="<?php echo JURI::base(); ?>components/com_djevents/assets/images/icon-48-categories.png" />
								<span><?php echo JText::_('COM_DJEVENTS_CATEGORIES'); ?></span>
							</a>
						</div>
						<div class="icon">
							<a href="index.php?option=com_djevents&amp;view=events">
								<img alt="<?php echo JText::_('COM_DJEVENTS_EVENTS'); ?>" src="<?php echo JURI::base(); ?>components/com_djevents/assets/images/icon-48-events.png" />
								<span><?php echo JText::_('COM_DJEVENTS_EVENTS'); ?></span>
							</a>
						</div>
						
						<div style="clear: both"></div>
						
						<h3><?php echo JText::_('COM_DJEVENTS_BASIC_ACTIONS_HEADING') ?></h3>
						
						<div class="icon">
							<?php
							$juri = JUri::getInstance();
							$return_url = base64_encode($juri->toString());
							?>
							<a href="index.php?option=com_config&amp;view=component&amp;component=com_djevents&amp;path=&amp;return=<?php echo urlencode($return_url); ?>">
								<img alt="<?php echo JText::_('JOPTIONS'); ?>" src="<?php echo JURI::base(); ?>components/com_djevents/assets/images/icon-48-config.png" />
								<span><?php echo JText::_('JOPTIONS'); ?></span>
							</a>
						</div>
						
						<div class="icon">
							<a href="http://dj-extensions.com/extensions/dj-events" target="_blank">
								<img alt="<?php echo JText::_('COM_DJEVENTS_DOCUMENTATION'); ?>" src="<?php echo JURI::base(); ?>components/com_djevents/assets/images/icon-48-documentation.png" />
								<span><?php echo JText::_('COM_DJEVENTS_DOCUMENTATION'); ?></span>
							</a>
						</div>
						
						<div style="clear: both"></div>
						
						<h3><?php echo JText::_('COM_DJEVENTS_IMPORT_HEADING') ?></h3>
						
						<div class="icon">
							<a href="#" onclick="if(confirm('<?php echo JText::_('COM_DJEVENTS_IMPORT_JEVENTS_CONFIRM_MSG') ?>')) { document.adminForm.source.value='jevents'; Joomla.submitform('import', document.adminForm);} return false;">
								<img alt="<?php echo JText::_('COM_DJEVENTS_IMPORT_JEVENTS'); ?>" src="<?php echo JURI::base(); ?>components/com_djevents/assets/images/icon-48-jevents.png" />
								<span><?php echo JText::_('COM_DJEVENTS_IMPORT_JEVENTS'); ?></span>
							</a>
						</div>
						
				</div>
			</div>
			<div class="cpanel-right">
				<div class="djlic_cpanel cpanel">
					<div style="float:right;">
						<?php 
						$user = JFactory::getUser();
						if ($user->authorise('core.admin', 'com_djevents')){
							//echo DJLicense::getSubscription(); 
						}?>
					</div>
				</div>
			</div>
		</div>

	<input type="hidden" name="option" value="com_djevents" />
	<input type="hidden" name="c" value="cpanel" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="source" value="" />
	<input type="hidden" name="view" value="cpanel" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</div>
</div>
</form>
<div style="clear: both" class="clr"></div>
<?php echo DJEVENTSFOOTER; ?>