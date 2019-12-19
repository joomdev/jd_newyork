<?php
/**
 * @package		JD Progress Bar
 * @author		JoomDev https://www.joomdev.com
 * @copyright	Copyright (C) 2009 - 2019 JoomDev.
 * @license		GNU/GPLv2 and later
 */
// no direct access
defined('_JEXEC') or die;

$title = $params->get('title','');
$textColor = $params->get('textColor','');
$dis_precentage = $params->get('dis_precentage','');
$percenateg_position = $params->get('percenateg_position','');
$barProgressColor = $params->get('barProgressColor','');
$items = $params->get('items',[]);
$barHeight = $params->get('barHeight',24);
$barType = $params->get('barType','');
?>

<div id="jd-progressbar" class="jdpb">
	<?php foreach($items as $key => $item) { ?> 
		<?php
		// Let's set the background color
		if($item->type == 'customtype') {
			$barcolor = $item->customcolor;
		} else {
			$barcolor = modJDprogressBarsHelper::GetColor($item->type,$params);
		}
		
		?>
		<?php if($percenateg_position == "outside"){ ?>
			<div class="jdpb-bar-values outside">
				<span class="jdpb-bar-title" style="color:<?php echo $textColor; ?>;"><?php echo $item->title; ?></span>
				<span id="progressbarValue<?php echo $module->id.$key; ?>" class="jdpb-bar-percentage" style="color:<?php echo $textColor; ?>;"><?php echo $item->precentage; ?>%</span>
			</div>	
			<div class="jdpb-bar <?php echo $barType; ?>" style="height: <?php echo $barHeight; ?>px; background-color: <?php echo $barProgressColor; ?>;">
				<div id="jd_progressbar<?php echo $module->id.$key; ?>" class="jdpb--bar progressbar-not-started" style="background-color:<?php echo $barcolor; ?>;"></div>
			</div>			
		<?php }else{ ?>
			<div class="jdpb-bar <?php echo $barType; ?>" style="height: <?php echo $barHeight; ?>px; background-color: <?php echo $barProgressColor; ?>;" >
				<div id="jd_progressbar<?php echo $module->id.$key; ?>" class="jdpb--bar progressbar-not-started" style="background-color:<?php echo $barcolor; ?>">
					<div class="jdpb-bar-values">
						<span class="jdpb-bar-title" style="color:<?php echo $textColor; ?>;"><?php echo $item->title; ?></span>
						<span id="progressbarValue<?php echo $module->id.$key; ?>" class="jdpb-bar-percentage" style="color:<?php echo $textColor; ?>;"><?php echo $item->precentage; ?>%</span>
						
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>


<?php foreach($items as $key => $item) { ?> 
	<script>
		(function ($) {
			// Progressbar
			var initprogressbar = function (_element) {
				var elem = document.getElementById("jd_progressbar<?php echo $module->id.$key; ?>");
				var width = 1;
				var id = setInterval(frame, 10);
				function frame() {
					if (width >= <?php echo $item->precentage; ?>) {
						clearInterval(id);
					} else {
						width++;
						elem.style.width = width + '%';
						<?php if($percenateg_position == "inside"){ ?>
							document.getElementById("progressbarValue<?php echo $module->id.$key; ?>").innerHTML = width * 1 + '%';
						<?php } ?>
						<?php if($percenateg_position == "outside"){ ?>
							document.getElementById("progressbarValue<?php echo $module->id.$key; ?>").innerHTML = width * 1 + '%';
						<?php } ?>
					}
				}
			};
			var elementInViewport = function (element) {
				var _this = element;
				var _this_top = _this.offset().top;
				return (_this_top <= window.pageYOffset + parseInt(window.innerHeight)) && (_this_top >= window.pageYOffset);
			};
			// Events
			var docReady = function () {
				//initprogressbar();
			};
			var winScroll = function(){
				var _element = $('#jd_progressbar<?php echo $module->id.$key; ?>.progressbar-not-started');
				//console.log(_element);
				if(typeof _element != 'undefined' && _element.length!=0 && elementInViewport(_element)){
					$(_element).removeClass('progressbar-not-started');
					initprogressbar(_element);
				}
			};
			$(docReady);
			$(window).scroll(winScroll);
		})(jQuery);
	</script>
<?php } ?>