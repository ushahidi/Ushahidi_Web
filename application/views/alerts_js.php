<?php
/**
 * Alerts js file.
 *
 * Handles javascript stuff related  to alerts function
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
		<?php require_once(APPPATH.'views/map_common_js.php'); ?>
		
		var map;
		var radiusLayer;
		jQuery(function($) {
			
			$(window).load(function(){
			<?php 
				
				/*OpenLayers uses IE's VML for vector graphics.
					We need to wait for IE's engine to finish loading all namespaces (document.namespaces) for VML.
					jQuery.ready is executing too soon for IE to complete it's loading process.
				 */
			?>
			
			// Create the map
			var latitude = <?php echo $latitude; ?>;
			var longitude = <?php echo $longitude; ?>;
			
			map = createMap('divMap', latitude, longitude);
			
			// Add the radius layer
			addRadiusLayer(map, latitude, longitude);
			
			// Draw circle around point
			drawCircle(map, latitude, longitude);
			
			// Detect Map Clicks
			map.events.register("click", map, function(e){
				var lonlat = map.getLonLatFromViewPortPx(e.xy);
				var lonlat2 = map.getLonLatFromViewPortPx(e.xy);
			    m = new OpenLayers.Marker(lonlat);
				markers.clearMarkers();
		    	markers.addMarker(m);
		
				currRadius = $("#alert_radius").val();
				radius = currRadius * 1000
				
				lonlat2.transform(proj_900913, proj_4326);
				drawCircle(map, lonlat2.lat, lonlat2.lon, radius);
							
				// Update form values (jQuery)
				$("#alert_lat").attr("value", lonlat2.lat);
				$("#alert_lon").attr("value", lonlat2.lon);
			});
			
			/* 
			Google GeoCoder
			TODO - Add Yahoo and Bing Geocoding Services
			 */
			$('.btn_find').live('click', function () {
				geoCode();
			});
			
			
			// Alerts Slider
			$("select#alert_radius").selectToUISlider({
				labels: 6,
				labelSrc: 'text',
				sliderOptions: {
					change: function(e, ui) {
						var newRadius = $("#alert_radius").val();
						
						// Convert to Meters
						radius = newRadius * 1000;	
						
						// Redraw Circle
						currLon = $("#alert_lon").val();
						currLat = $("#alert_lat").val();
						drawCircle(map, currLat, currLon, radius);
					}
				}
			}).hide();
			
			
			// Some Default Values		
			$("#alert_mobile").focus(function() {
				$("#alert_mobile_yes").attr("checked",true);
			}).blur(function() {
				if( !this.value.length ) {
					$("#alert_mobile_yes").attr("checked",false);
				}
			});
			
			$("#alert_email").focus(function() {
				$("#alert_email_yes").attr("checked",true);
			}).blur(function() {
				if( !this.value.length ) {
					$("#alert_email_yes").attr("checked",false);
				}
			});
		
		
			// Category treeview
		    $("#category-column-1,#category-column-2").treeview({
		      persist: "location",
			  collapsed: true,
			  unique: false
			  });
			});
		});
		
		
		/**
		 * Google GeoCoder
		 */
		function geoCode()
		{
			$('#find_loading').html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
			address = $("#location_find").val();
			$.post("<?php echo url::site() . 'reports/geocode/' ?>", { address: address },
				function(data){
					if (data.status == 'success'){
						var lonlat = new OpenLayers.LonLat(data.message[1], data.message[0]);
						lonlat.transform(proj_4326,proj_900913);
					
						m = new OpenLayers.Marker(lonlat);
						markers.clearMarkers();
				    	markers.addMarker(m);
						map.setCenter(lonlat, 9);
					
						newRadius = $("#alert_radius").val();
						radius = newRadius * 1000

						drawCircle(data.message[1],data.message[0], radius);
					
						// Update form values (jQuery)
						$("#alert_lat").attr("value", data.message[0]);
						$("#alert_lon").attr("value", data.message[1]);
					} else {
						alert(address + " not found!\n\n***************************\nEnter more details like city, town, country\nor find a city or town close by and zoom in\nto find your precise location");
					}
					$('#find_loading').html('');
				}, "json");
			return false;
		}		
