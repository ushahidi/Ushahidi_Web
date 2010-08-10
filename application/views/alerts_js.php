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
		var map;
		var map_layer;
		var radius = 20000;
		
		jQuery(function() {
			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			- Units in Metres instead of Degrees					
			*/
			var proj_4326 = new OpenLayers.Projection('EPSG:4326');
			var proj_900913 = new OpenLayers.Projection('EPSG:900913');
			var options = {
				units: "m",
				numZoomLevels: 16,
				controls:[],
				projection: proj_900913,
				'displayProjection': proj_4326
				};
				
			map = new OpenLayers.Map('divMap', options);
			
			/*
			- Select A Mapping API
			- Live/Yahoo/OSM/Google
			- Set Bounds					
			*/

			<?php echo map::layers_js(FALSE); ?>
			map.addLayers(<?php echo map::layers_array(FALSE); ?>);

			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.Attribution());
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			
			// Create the Circle/Radius layer
			var radiusLayer = new OpenLayers.Layer.Vector("Radius Layer");
			
			
			// Create the markers layer
			var markers = new OpenLayers.Layer.Markers("Markers");
			map.addLayers([radiusLayer, markers]);
			
			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			myPoint.transform(proj_4326, proj_900913);
			
			// create a marker positioned at a lon/lat
			var marker = new OpenLayers.Marker(myPoint);
			markers.addMarker(marker);
			
			// draw circle around point
			drawCircle(<?php echo $longitude; ?>,<?php echo $latitude; ?>,radius);
			
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, 9);
			
			
			// Detect Map Clicks
			map.events.register("click", map, function(e){
				var lonlat = map.getLonLatFromViewPortPx(e.xy);
				var lonlat2 = map.getLonLatFromViewPortPx(e.xy);
			    m = new OpenLayers.Marker(lonlat);
				markers.clearMarkers();
		    	markers.addMarker(m);
		
				currRadius = $("#alert_radius").val();
				radius = currRadius * 1000
				
				lonlat2.transform(proj_900913,proj_4326);
				drawCircle(lonlat2.lon,lonlat2.lat, radius);
							
				// Update form values (jQuery)
				$("#alert_lat").attr("value", lonlat2.lat);
				$("#alert_lon").attr("value", lonlat2.lon);
			});
			
			
			// Draw circle around point
			function drawCircle(lon,lat,radius)
			{
				radiusLayer.destroyFeatures();
				var circOrigin = new OpenLayers.Geometry.Point(lon,lat);
				circOrigin.transform(proj_4326, proj_900913);
				
				var circStyle = OpenLayers.Util.extend( {},OpenLayers.Feature.Vector.style["default"] );
				var circleFeature = new OpenLayers.Feature.Vector(
					OpenLayers.Geometry.Polygon.createRegularPolygon( circOrigin, radius, 40, 0 ),
					null,
					circStyle
				);
				radiusLayer.addFeatures( [circleFeature] );
			}			
			
			/* 
			Google GeoCoder
			TODO - Add Yahoo and Bing Geocoding Services
			 */
			$('.btn_find').live('click', function () {
				address = $("#location_find").val();
				if ( typeof GBrowserIsCompatible == 'undefined' ) {
					alert('GeoCoding is only currently supported by Google Maps.\n\nPlease pinpoint the location on the map\nusing your mouse.');
				} else {
					var geocoder = new GClientGeocoder();
					if (geocoder) {
						$('#find_loading').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
						geocoder.getLatLng(
							address,
							function(point) {
								if (!point) {
									alert(address + " not found!\n\n***************************\nFind a city or town close by and zoom in\nto find your precise location");
									$('#find_loading').html('');
								} else {
									var lonlat = new OpenLayers.LonLat(point.lng(), point.lat());
									lonlat.transform(proj_4326,proj_900913);
								
									m = new OpenLayers.Marker(lonlat);
									markers.clearMarkers();
							    	markers.addMarker(m);
									map.setCenter(lonlat, <?php echo $default_zoom; ?>);
								
									newRadius = $("#alert_radius").val();
									radius = newRadius * 1000

									drawCircle(point.lng(),point.lat(), radius);
								
									// Update form values (jQuery)
									$("#alert_lat").attr("value", point.lat());
									$("#alert_lon").attr("value", point.lng());
								
									$('#find_loading').html('');
								}
							}
						);
					}
				}
				return false;
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
						drawCircle(currLon,currLat,radius);
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
		});