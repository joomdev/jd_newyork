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

if($this->params->get('event_gallery',1)) { ?>
<div class="djev_gallery">
	<?php $image = $this->images[0]; ?>
	<div class="djev_poster">
		<?php if(!empty($image->video)) { ?> 
			<a class="djev_media_link mfp-iframe" href="<?php echo $image->video; ?>">
				<img alt="<?php echo $image->title; ?>" src="<?php echo $this->item->thumb; ?>" />
				<i class="fa fa-play"></i>
			</a>
		<?php } else if(!empty($this->item->thumb)) { ?>
			<a class="djev_media_link" data-title="<?php echo $image->title; ?>" href="<?php echo $image->image; ?>">
				<img alt="<?php echo $image->title; ?>" src="<?php echo $this->item->thumb; ?>" />
			</a>
		<?php } ?>
	</div>

	<?php if (count($this->images) > 1) { ?>
		<div class="djev_thumbnails djev_clearfix">
			<?php foreach($this->images as $key => $image) { 
				if(!$key) continue; // don't display poster again
				?>
				<div class="djev_thumb" style="width: <?php echo (int)$this->params->get('small_width', 100) ?>px">
					<?php if(!empty($image->video)) { ?> 
						<a class="djev_media_link mfp-iframe" href="<?php echo $image->video; ?>">
							<img alt="<?php echo $image->title; ?>" src="<?php echo $image->small; ?>" />
							<i class="fa fa-play"></i>
						</a>
					<?php } else { ?>
						<a class="djev_media_link" data-title="<?php echo $image->title; ?>" href="<?php echo $image->image; ?>">
							<img alt="<?php echo $image->title; ?>" src="<?php echo $image->small; ?>" />
						</a>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</div>
<?php } ?>