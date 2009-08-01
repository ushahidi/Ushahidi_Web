/**
 * Settings js file.
 *
 * Handles javascript stuff related to settings function.
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

		// Map JS
		$(document).ready(
			function()
			{
				var markers;
				var marker;
				var myPoint;
				var lonlat;
				var DispProj = new OpenLayers.Projection("EPSG:4326");
				var MapProj = new OpenLayers.Projection("EPSG:900913");
				var options = {
				maxResolution: 156543.0339
				, units: "m"
				, projection: MapProj
				, 'displayProjection': DispProj
				, maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34)
				, controls: [	new OpenLayers.Control.Navigation(),
													new OpenLayers.Control.MouseDefaults(),
													new OpenLayers.Control.PanZoom(),
													new OpenLayers.Control.ArgParser() ]
										};
				
				var map = new OpenLayers.Map('map', options);
				var gmap_layer = new OpenLayers.Layer.Google(
									"google",
									{'isBaseLayer': true,'sphericalMercator': true });
				var ve_layer = new OpenLayers.Layer.VirtualEarth(
									"virtualearth",
									{ 'type': VEMapStyle.Road, 'sphericalMercator': true });
				var ymap_layer = new OpenLayers.Layer.Yahoo(
									"yahoo",
									{ 'sphericalMercator': true });
				var osmap_layer = new OpenLayers.Layer.OSM.Mapnik(
									"openstreetmap",
									{ 'sphericalMercator': true });
				
				map.addLayers([gmap_layer, ve_layer, ymap_layer, osmap_layer]);
				
				map.addControl(new OpenLayers.Control.MousePosition());
				
				
				// Transform feature point coordinate to Spherical Mercator
				preFeatureInsert = function(feature) {		
					var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
					OpenLayers.Projection.transform(point, DispProj, MapProj);
				};
				
				
				// Create the markers layer
				markers = new OpenLayers.Layer.Markers("Markers", {
					preFeatureInsert:preFeatureInsert,
					projection: DispProj
				});
				map.addLayer(markers);
				
				
				// create myPoint, a lat/lon object
				myPoint = new OpenLayers.LonLat(<?php echo $default_lon; ?>, <?php echo $default_lat; ?>).transform(DispProj, MapProj);
				
				
				// create a marker using the myPoint lat/lon object
				marker = new OpenLayers.Marker(myPoint);
				markers.addMarker(marker);
				
				
				// set map center and zoom in to default zoom level
				map.setCenter(myPoint, <?php echo $default_zoom; ?>);

				// add info bubble to the marker
				// popup = new OpenLayers.Popup.Anchored("test", myPoint,new OpenLayers.Size(200,200),"Hello!", true);
				
				
				// create new marker at map click location
				map.events.register("click", map, function(e){
					// Update the myPoint global
					myPoint = map.getLonLatFromViewPortPx(e.xy);
					lonlat = map.getLonLatFromViewPortPx(e.xy);
					markers.removeMarker(marker);
					marker = new OpenLayers.Marker(lonlat);
			    	markers.addMarker(marker);
							
					// Update form values (jQuery)
					lonlat = lonlat.transform(MapProj,DispProj);
					$("#default_lat").attr("value", lonlat.lat);
					$("#default_lon").attr("value", lonlat.lon);
				});
				

				// zoom slider detection
				$('select#default_zoom').selectToUISlider({
					labels: 5,
					sliderOptions: {
						change:function(e, ui) {
							var new_zoom = parseInt($("#default_zoom").val());
							$('#zoom_level').html('"' + new_zoom + '"');
							map.setCenter(myPoint, new_zoom);
							markers.removeMarker(marker);
							marker = new OpenLayers.Marker(myPoint);
					    	markers.addMarker(marker);
						}
					}
				}).hide();
				
				
				// detect country dropdown change, then zoom to selected country
				$('#default_country').change(function(){
					address = $('#default_country :selected').text();
					var geocoder = new GClientGeocoder();
					if (geocoder) {
						geocoder.getLatLng(
							address,
							function(point) {
								if (!point) {
									alert(address + " not found");
								} else {
									myPoint = new OpenLayers.LonLat(point.lng(), point.lat()).transform(DispProj, MapProj);
									map.setCenter(myPoint, 3);
									markers.removeMarker(marker);
									marker = new OpenLayers.Marker(myPoint);
							    	markers.addMarker(marker);
									
									// Update form lat/lon values
									$("#default_lat").attr("value", point.lat());
								    $("#default_lon").attr("value", point.lng());
								}
						});
					}
				});
				
				
				// Provider Select JS
				// This could be cleaner ;)
				var i;
				var api_go;

				i = <?php print $default_map ?>;
				if ( i == 1 ){
					api_go = 'http://code.google.com/apis/maps/signup.html';
					$('#api_link').attr('target', '_blank');
					gmap_layer.setVisibility(true);
					ve_layer.setVisibility(false);
					ymap_layer.setVisibility(false);
					osmap_layer.setVisibility(false);
				}else if ( i == 2){
					api_go = 'javascript:alert(\'Your current selection does not require an API key!\')';
					$('#api_link').attr('target', '_top');
					gmap_layer.setVisibility(false);
					ve_layer.setVisibility(true);
					ymap_layer.setVisibility(false);
					osmap_layer.setVisibility(false);
				}else if ( i == 3){
					api_go = 'http://developer.yahoo.com/maps/simple/';
					$('#api_link').attr('target', '_blank');
					gmap_layer.setVisibility(false);
					ve_layer.setVisibility(false);
					ymap_layer.setVisibility(true);
					osmap_layer.setVisibility(false);
				}else if ( i == 4 ){
					api_go = 'http://code.google.com/apis/maps/signup.html';
					$('#api_link').attr('target', '_blank');
					gmap_layer.setVisibility(false);
					ve_layer.setVisibility(false);
					ymap_layer.setVisibility(false);
					osmap_layer.setVisibility(true);
				}
				$('#api_link').attr('href', api_go);

				
				// detect map provider dropdown change
				$('#default_map').change(function(){					
					i = $('#default_map option:selected').val();
					if ( i == 1 ){
						api_go = 'http://code.google.com/apis/maps/signup.html';
						$('#api_link').attr('target', '_blank');
						gmap_layer.setVisibility(true);
						ve_layer.setVisibility(false);
						ymap_layer.setVisibility(false);
						osmap_layer.setVisibility(false);
						$("#api_div_google").show();
						$("#api_div_yahoo").hide();
					}else if ( i == 2){
						api_go = 'javascript:alert(\'Your current selection does not require an API key!\')';
						$('#api_link').attr('target', '_top');
						gmap_layer.setVisibility(false);
						ve_layer.setVisibility(true);
						ymap_layer.setVisibility(false);
						osmap_layer.setVisibility(false);
						$("#api_div_google").hide();
						$("#api_div_yahoo").hide();
					}else if ( i == 3){
						api_go = 'http://developer.yahoo.com/maps/simple/';
						$('#api_link').attr('target', '_blank');
						gmap_layer.setVisibility(false);
						ve_layer.setVisibility(false);
						ymap_layer.setVisibility(true);
						osmap_layer.setVisibility(false);
						$("#api_div_google").hide();
						$("#api_div_yahoo").show();
					}else if (i == 4){
						api_go = 'http://code.google.com/apis/maps/signup.html';
						$('#api_link').attr('target', '_blank');
						gmap_layer.setVisibility(false);
						ve_layer.setVisibility(false);
						ymap_layer.setVisibility(false);
						osmap_layer.setVisibility(true);
						$("#api_div_google").show();
						$("#api_div_yahoo").hide();
					}
					$('#api_link').attr('href', api_go);
				});
				
				
			}
		);
		
		
		// Retrieve Cities From Geonames DB (Ajax)
		function retrieveCities()
		{
			var selected = $("#default_country option[selected]");
			country = selected.val();
			if (!country || country =='') {
				alert('Please select a country from the dropdown');
			}
			else
			{
				$('#cities_loading').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
				$.getJSON("<?php echo url::base() . 'admin/settings/updateCities/' ?>" + country,
					function(data){
						if (data.status == 'success'){
							$('#city_count').show();
							$('#city_count').html(data.response);
							$('#cities_loading').html('');
						} else	{
							alert(data.response);
						}
						$('#cities_loading').html('');
				  	}, "json");
			}
		}
