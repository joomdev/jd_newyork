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

// no direct access
defined('_JEXEC') or die ('Restricted access');
?>

<div class="djev_mod_map">

	<div class="djev_mod_map_in">
	
		<div class="djev_map_wrapper">
			<div id="djev_map<?php echo $module->id ?>" class="djev_map" style="width: 100%; height: <?php echo $params->get('map_height', 500) ?>px;"></div>
		</div>
		
	</div>

</div>

<script type="text/javascript">
	<?php 
	
	$markers = array();
	
	foreach ($points as $items) {
		
		ob_start();
		require JModuleHelper::getLayoutPath('mod_djevents_map', 'default_items');
		$marker_content = ob_get_clean();
		
		$marker_content = str_replace(array("\r", "\n"), "", $marker_content);
		
		$markerIcon = $params->get('map_marker', '');
		
		$marker = new stdClass();
		$marker->txt = $marker_content;
		$marker->latitude = $items[0]->latitude;
		$marker->longitude = $items[0]->longitude;
		$marker->icon = $markerIcon ? JUri::root(false) . $markerIcon : '';
		$markers[] = $marker;
	}
	
	$map_styles = $params->get('map_styles');
	
	if (trim($map_styles) == '') {
		$map_styles = '[]';
	}
	?>

		var markers = <?php echo json_encode($markers); ?>;

        var djev_map;
        var djev_map_markers = [];//new google.maps.InfoWindow();
        var djev_geocoder = new google.maps.Geocoder();
        
		function DJStoreLocatorAddMarker(position,txt,icon)
		{
		    var MarkerOpt =  
		    { 
		        position: position, 
		        icon: icon
		    };
		    
		    var marker = new google.maps.Marker(MarkerOpt);
		    marker.txt=txt;

		    var djev_map_info = new google.maps.InfoWindow();
		    
		    google.maps.event.addListener(marker,"click",function(m)
		    {
			    for (var i = 0; i < djev_map_markers.length; i++) {
			    	djev_map_markers[i].infowindow.close();
				}
		    	djev_map_info.setContent(marker.txt);
		    	djev_map_info.open(djev_map, marker);
		    });

		    marker.infowindow = djev_map_info;
			
		    djev_map_markers.push(marker);
		    
		    return marker;
		}
		    	
		 function DJStoreLocatorMapInit()    
		 {  
            var adLatlng = new google.maps.LatLng(
            	<?php echo $params->get('map_latitude', '51.76745147292665') ?>,
            	<?php echo $params->get('map_longitude', '19.456850811839104') ?>);            
			var MapOptions = {
				zoom: <?php echo (int)$params->get('map_zoom', 12) ?>,
			  	center: adLatlng,
			  	mapTypeId: google.maps.MapTypeId.ROADMAP,
			  	navigationControl: true,
			  	styles: <?php echo $map_styles; ?>
			};
			djev_map = new google.maps.Map(document.getElementById('djev_map<?php echo $module->id ?>'), MapOptions);
			
			<?php if(count($markers)){ ?>
				for (var i=0; i < markers.length; i++) {

					var adLatlng = new google.maps.LatLng(markers[i].latitude, markers[i].longitude);
					DJStoreLocatorAddMarker(adLatlng, markers[i].txt, markers[i].icon);
				}
				for (var i = 0; i < djev_map_markers.length; i++) {
					djev_map_markers[i].setMap(djev_map);
		        }

				// Add a marker clusterer to manage the markers.
		        var markerCluster = new MarkerClusterer(djev_map, djev_map_markers,
		            { imagePath: '<?php echo JURI::base().'modules/mod_djevents_map/assets/images/m'; ?>', maxZoom: 11});
			<?php } ?>
		}
		
		jQuery(window).on('load', function(){ 
			DJStoreLocatorMapInit();
		});
</script>