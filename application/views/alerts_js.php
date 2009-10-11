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
		jQuery(function() {
			var moved=false;
			
			// Now initialise the map
			var options = {
			units: "dd"
			, numZoomLevels: 16
			, controls:[]};
			var map = new OpenLayers.Map('divMap', options);
			var default_map = <?php echo $default_map; ?>;
			if (default_map == 2)
			{
				var map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth");
			}
			else if (default_map == 3)
			{
				var map_layer = new OpenLayers.Layer.Yahoo("yahoo");
			}
			else if (default_map == 4)
			{
				var map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap");
			}
			else
			{
				var map_layer = new OpenLayers.Layer.Google("google");
			}
			
			map.addLayer(map_layer);
			
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.Attribution());
			map.addControl(new OpenLayers.Control.MousePosition());
			
			// Create the Circle/Radius layer
			var radiusLayer = new OpenLayers.Layer.Vector("Radius Layer");
			
			// Create the markers layer
			var markers = new OpenLayers.Layer.Markers("Markers");
			map.addLayers([radiusLayer, markers]);
			
			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			
			// create a marker positioned at a lon/lat
			var marker = new OpenLayers.Marker(myPoint);
			markers.addMarker(marker);
			
			// draw circle around point
			drawCircle(<?php echo $longitude; ?>,<?php echo $latitude; ?>);
			
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, 9);
			
			// Detect Map Clicks
			map.events.register("click", map, function(e){
				var lonlat = map.getLonLatFromViewPortPx(e.xy);
			    m = new OpenLayers.Marker(lonlat);
				markers.clearMarkers();
		    	markers.addMarker(m);
		
				drawCircle(lonlat.lon,lonlat.lat);
							
				// Update form values (jQuery)
				$("#alert_lat").attr("value", lonlat.lat);
				$("#alert_lon").attr("value", lonlat.lon);
			});
			
			// Detect Dropdown Select
			$("#alert_city").change(function() {
				var lonlat = $(this).val().split(",");
				if ( lonlat[0] && lonlat[1] )
				{
					l = new OpenLayers.LonLat(lonlat[0], lonlat[1]);
					m = new OpenLayers.Marker(l);
					markers.clearMarkers();
			    	markers.addMarker(m);
					map.setCenter(l, 9);
					
					drawCircle(lonlat[0],lonlat[1]);
					
					// Update form values (jQuery)
					$("#alert_lat").attr("value", lonlat[1]);
					$("#alert_lon").attr("value", lonlat[0]);
				}
			});
			
			// Draw circle around point
			function drawCircle(lon,lat)
			{
				radiusLayer.destroyFeatures();
				var circOrigin = new OpenLayers.Geometry.Point(lon,lat);
				var circStyle = OpenLayers.Util.extend( {},OpenLayers.Feature.Vector.style["default"] );
				var circleFeature = new OpenLayers.Feature.Vector(
					OpenLayers.Geometry.Polygon.createRegularPolygon( circOrigin, 0.20, 40, 0 ),
					null,
					circStyle
				);
				radiusLayer.addFeatures( [circleFeature] );
			}
			
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