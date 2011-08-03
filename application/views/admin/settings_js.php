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
													new OpenLayers.Control.ArgParser(),
													new OpenLayers.Control.MousePosition(),
													new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) ]
										};
				
				var map = new OpenLayers.Map('map', options);
				
				<?php echo map::layers_js(TRUE); ?>
				map.addLayers(<?php echo map::layers_array(TRUE); ?>);
				
				
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
				
				// When we change the zoom level on the map control itself, also change the slider
				//  which is where the value is actually being passed from when we save the map position
				map.events.on({
					zoomend: function(e) {
						$('select#default_zoom').val(map.getZoom());
						$('select#default_zoom').trigger('click');
					}
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
					
					//>
					//> 25/03/2011 - E.Kala <emmanuel(at)ushahidi.com>
					//> Switched to Google v3 API
					//>
					
					var geocoder = new google.maps.Geocoder();
					
					if (geocoder) {
						geocoder.geocode({ 'address': address },
							function(results, status) {
								if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
									alert(address + " not found");
								} else if (status == google.maps.GeocoderStatus.OK) {
									
									// Get the lat/lon from the result; accuracy of the map center is off because the lookup
									// address does not include the name of the capital city
									var point = results[0].geometry.location;
									myPoint = new OpenLayers.LonLat(point.lng(), point.lat()).transform(DispProj, MapProj);
									
									map.setCenter(myPoint, map.getZoom());
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

				default_map = '<?php print $default_map ?>';
				all_maps = <?php echo $all_maps_json; ?>;
				for (var i in all_maps)
				{
					target = map.getLayersByName(all_maps[i].title);
					if (i == default_map) {
						target[0].setVisibility(true);
						map.setBaseLayer(target[0]);
						
						if (all_maps[i].api_signup) {
							$('#api_link').attr('href', all_maps[i].api_signup);
							$('#api_link').attr('target', '_blank');
						} else {
							$('#api_link').attr('href', 'javascript:alert(\'Your current selection does not require an API key!\')');
							$('#api_link').attr('target', '_top');
						}
						
						if (all_maps[i].openlayers == 'Google') {
							$("#api_div_google").show();
						}
						else
						{
							$("#api_div_google").hide();
						}
						
						if (all_maps[i].openlayers == 'Yahoo') {
							$("#api_div_yahoo").show();
						}
						else
						{
							$("#api_div_yahoo").hide();
						}
					}
					else
					{
						if (target[0]) {
							target[0].setVisibility(false);
						};
					}
				};
				
				// detect map provider dropdown change
				$('#default_map').change(function(){					
					selected_map = $('#default_map option:selected').val();
					for (var i in all_maps)
					{
						target = map.getLayersByName(all_maps[i].title);
						if (i == selected_map)
						{
							if (target[0]) {
								target[0].setVisibility(true);
								map.setBaseLayer(target[0]);

								if (all_maps[i].api_signup) {
									$('#api_link').attr('href', all_maps[i].api_signup);
									$('#api_link').attr('target', '_blank');
								} else {
									$('#api_link').attr('href', 'javascript:alert(\'Your current selection does not require an API key!\')');
									$('#api_link').attr('target', '_top');
								}
								
								if (all_maps[i].openlayers == 'Google') {
									$("#api_div_google").show();
								}
								else
								{
									$("#api_div_google").hide();
								}
								
								if (all_maps[i].openlayers == 'Yahoo') {
									$("#api_div_yahoo").show();
								}
								else
								{
									$("#api_div_yahoo").hide();
								}
							}
						}
						else
						{
							if (target[0]) {
								target[0].setVisibility(false);
							};
						}
					};
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
				$('#cities_loading').html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
				$.getJSON("<?php echo url::site() . 'admin/settings/updateCities/' ?>" + country,
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
