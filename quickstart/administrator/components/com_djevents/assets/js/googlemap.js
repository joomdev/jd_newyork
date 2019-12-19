/**
 * @version $Id$
 * @package DJ-MediaTools
 * @subpackage DJ-MediaTools slideshow layout
 * @copyright Copyright (C) 2017  DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

(function($){
	
	var DJEventsMap = window.DJEventsMap = window.DJEventsMap || function(map, options){
		
		this.options = {
			latitude: 0,
			longitude: 0,
			zoom: 10,
			disableautopan: false
		};
		
		this.init(map, options);
	};

	DJEventsMap.prototype.init = function(map,options){
		
		var self = this;
		
		if (typeof google == 'undefined' || !document.getElementById(map)) return;
		
		jQuery.extend(self.options, options);
		
		var point =  new google.maps.LatLng(options.latitude,options.longitude);

		var mapOptions = {
			center: point,
			zoom: options.zoom,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		self.map = new google.maps.Map(document.getElementById(map), mapOptions);
		
		var markerOptions = {
			position: point,
			map: self.map,
			disableAutoPan: options.disableautopan,
			animation: google.maps.Animation.DROP,
			draggable: true
		};

		self.marker = new google.maps.Marker(markerOptions);

		google.maps.event.addListener(self.map, 'click',  function(e) {
			if (e.latLng) {
				document.getElementById('jform_latitude').value = e.latLng.lat();
				document.getElementById('jform_longitude').value = e.latLng.lng();
				document.getElementById('jform_zoom').value = self.map.getZoom();

				self.marker.setPosition(e.latLng);
				self.map.setCenter(e.latLng);
			}
		});

		google.maps.event.addListener(self.marker, "dragend", function(e) {
			if (e.latLng) {
				document.getElementById('jform_latitude').value = e.latLng.lat();
				document.getElementById('jform_longitude').value = e.latLng.lng();
				document.getElementById('jform_zoom').value = self.map.getZoom();

				self.map.setCenter(e.latLng);
			}
		});

		google.maps.event.addListener(self.map, "zoom_changed", function() {
			document.getElementById('jform_zoom').value = self.map.getZoom();
		});
		
		self.geocoder = new google.maps.Geocoder();
		
		$('#find_address').click(function(e){
			e.preventDefault();
			self.updateMap();
		});
		
		$('#jform_city_id').change(function(e){
			document.getElementById("google_address").value = '';
			self.updateMap();
		});
		
		var timer = null;
		$('#jform_address, #jform_post_code, #jform_city_id_new').on('keyup', function(){
			
			clearTimeout(timer);
			
			timer = setTimeout(function(){
				
				var address = document.getElementById("jform_address").value;
				var city = document.getElementById("jform_city_id_new").value;
				if(!city) {
					var cityselect =  document.getElementById("jform_city_id");
					if (cityselect) {
						
						city = cityselect.selectedIndex ? cityselect.options[cityselect.selectedIndex].text : '';
					}
				}
				var postcode = document.getElementById("jform_post_code").value;
				
				address = (address ? address + ', ' : '') + city + (postcode ? ', ' + postcode : '');
				document.getElementById("google_address").value = address;
				
			}, 500);
		});
		
		$('#jform_latitude, #jform_longitude, #jform_zoom').on('keyup', function(){
			
			clearTimeout(timer);
			
			timer = setTimeout(function(){
				
				self.showOnMap();
				
			}, 500);
		});
	};
	
	DJEventsMap.prototype.updateMap = function(){
		
		var self = this;
		
		var address = document.getElementById("google_address").value;
		
		if(!address) {
			
			var address = document.getElementById("jform_address").value;
			var city = document.getElementById("jform_city_id_new").value;
			if(!city) {
				var cityselect =  document.getElementById("jform_city_id");
				if (cityselect) {
					city = cityselect.options[cityselect.selectedIndex].text;
				}
			}
			var postcode = document.getElementById("jform_post_code").value;
			
			address = (address ? address + ', ' : '') + city + (postcode ? ', ' + postcode : '');
			document.getElementById("google_address").value = address;
		}
		
		if(self.geocoder) {
			self.geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					point = results[0].geometry.location;
					self.map.setCenter(point);
					self.marker.setPosition(point);
					document.getElementById('jform_latitude').value=point.lat();
					document.getElementById('jform_longitude').value=point.lng();
					document.getElementById('jform_zoom').value=self.map.getZoom();
				}
				else {
					alert(address + " not found");
				}
			});
		}
	};
	
	DJEventsMap.prototype.showOnMap = function(){
		
		var self = this;
		
		var latitude 	= document.getElementById('jform_latitude').value;
		var longitude 	= document.getElementById('jform_longitude').value;
		var zoom		= parseInt(document.getElementById('jform_zoom').value);
		
		var point =  new google.maps.LatLng(latitude,longitude);
		
		self.map.setCenter(point);
		self.marker.setPosition(point);
		if(isNaN(zoom)) zoom = 10;
		else if(zoom < 0) zoom = 0;
		else if(zoom > 21) zoom = 21;
		self.map.setZoom(zoom);
	};
	
})(jQuery);