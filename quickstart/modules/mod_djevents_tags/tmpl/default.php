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

?>

<div class="djev_events_tags">
	<?php foreach($tags as $tag) { ?>
		<a class="djev_tag btn btn-small" href="<?php echo JRoute::_(DJEventsHelperRoute::getEventsListRoute().'&tag='.$tag->id.':'.$tag->alias); ?>">
			<?php echo $tag->name; ?>
		</a>
	<?php } ?>
</div>
<?php 