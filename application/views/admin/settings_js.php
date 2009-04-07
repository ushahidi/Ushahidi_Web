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
				var options = {
				maxResolution: 156543.0339
				, units: "m"
				, projection: new OpenLayers.Projection("EPSG:900913")
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

				// Create the markers layer
				var markers = new OpenLayers.Layer.Markers("Markers");
				map.addLayer(markers);
				
				// create a lat/lon object
				var myPoint = new OpenLayers.LonLat(merc_x(<?php echo $default_lon; ?>), merc_y(<?php echo $default_lat; ?>));
				
				// create a marker positioned at a lon/lat
				var marker = new OpenLayers.Marker(myPoint);
				markers.addMarker(marker);
				
				// display the map centered on a latitude and longitude (Google zoom levels)
				map.setCenter(myPoint, <?php echo $default_zoom; ?>);

				// add info bubble to the marker
				// popup = new OpenLayers.Popup.Anchored("test", myPoint,new OpenLayers.Size(200,200),"Hello!", true);

				map.events.register("click", map, function(e){
					var lonlat = map.getLonLatFromViewPortPx(e.xy);
				    m = new OpenLayers.Marker(lonlat);
					markers.clearMarkers();
			    	markers.addMarker(m);
								
					// Update form values (jQuery)
					$("#default_lat").attr("value", unmerc_y(lonlat.lat));
					$("#default_lon").attr("value", unmerc_x(lonlat.lon));
				});
			   
				// Mercator Conversion - Rad/Degrees
				function rad_deg(ang) {
				    return ang * (180.0/Math.PI)
				}
				function deg_rad(ang) {
				    return ang * (Math.PI/180.0)
				}
				function merc_x(lon) {
				    var r_major = 6378137.000;
				    return r_major * deg_rad(lon);
				}
				function unmerc_x(lon) {
				    var r_major = 6378137.000;
				    return rad_deg(lon) / r_major;
				}
				function unmerc_y(y) {
				   var r_major = 6378137.000;
				    var r_minor = 6356752.3142;
				    var temp = r_minor / r_major;
				    var es = 1.0 - (temp * temp);
				    var eccent = Math.sqrt(es);
				    var eccnth = .5 * eccent;
				    var ts = Math.exp(- y / r_major);
				    var phi = Math.PI/2 - 2 * Math.atan(ts);
				    var i = 0;
				    dphi = 1;
				    var M_PI_2 = Math.PI/2;
				    while(Math.abs(dphi) > 0.000000001 && i < 15) {
				      var con = eccent * Math.sin (phi);
				      dphi = M_PI_2 - 2. * Math.atan (ts * Math.pow((1. - con) / 
				                                            (1. + con), eccnth)) - phi;
				      phi += dphi;
				      i++;
				    } 
				    return rad_deg(phi); 
				}
				function merc_y(lat) {
				    if (lat > 89.5)
				        lat = 89.5;
				    if (lat < -89.5)
				        lat = -89.5;
				    var r_major = 6378137.000;
				    var r_minor = 6356752.3142;
				    var temp = r_minor / r_major;
				    var es = 1.0 - (temp * temp);
				    var eccent = Math.sqrt(es);
				    var phi = deg_rad(lat);
				    var sinphi = Math.sin(phi);
				    var con = eccent * sinphi;
				    var com = .5 * eccent; 
				    con = Math.pow(((1.0-con)/(1.0+con)), com);
				    var ts = Math.tan(.5 * ((Math.PI*0.5) - phi))/con;
				    var y = 0 - r_major * Math.log(ts);
				    return y;
				}
				function merc(x,y) {
				    return [merc_x(x),merc_y(y)]; 
				}
				
				// $("#input_google").attr('checked', false);
				$("INPUT[@name=baseLayers]").attr('checked', false);

				// Zoom Slider JS
				$('#zoom1').slider({ 
			      minValue: 0, 
			      maxValue: 15,
			      startValue: <?php print (100 * ($default_zoom/15)); ?>,
			      steps: 15,        
			      range: false,
				  change:function(e,ui){
					var new_zoom = Math.round(ui.value / (100/15));
					$('#zoom_level').html('"' + new_zoom + '"');
					$('#default_zoom').val(new_zoom);
					map.setCenter(new OpenLayers.LonLat(merc_x($("#default_lon").val()), merc_y($("#default_lat").val())), new_zoom);
				  }
			   	});
			
				// Provider Select JS
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
				
				$('#default_country').change(function(){
					var selected = $("#default_country option[@selected]");
					address = selected.text();
					var geocoder = new GClientGeocoder();
					if (geocoder) {
						geocoder.getLatLng(
							address,
							function(point) {
								if (!point) {
									alert(address + " not found");
								} else {
									var lonlat = new OpenLayers.LonLat(merc_x(point.lng()), merc_x(point.lat()));
									m = new OpenLayers.Marker(lonlat);
									markers.clearMarkers();
							    	markers.addMarker(m);
									map.setCenter(lonlat, 3);
									
									// Update form lat/lon values
									$("#default_lat").attr("value", point.lat());
								}   $("#default_lon").attr("value", point.lng());
							}
						);
					}
				});
			}
		);
		
		// Retrieve Cities From Geonames DB (Ajax)
		function retrieveCities()
		{
			var selected = $("#default_country option[@selected]");
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