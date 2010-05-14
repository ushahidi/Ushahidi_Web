<?php
/**
 * Report submit js file.
 *
 * Handles javascript stuff related to report submit function.
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
		// jQuery Textbox Hints Plugin
		// Will move to separate file later or attach to forms plugin
		jQuery.fn.hint = function (blurClass) {
		  if (!blurClass) { 
		    blurClass = 'texthint';
		  }

		  return this.each(function () {
		    // get jQuery version of 'this'
		    var $input = jQuery(this),

		    // capture the rest of the variable to allow for reuse
		      title = $input.attr('title'),
		      $form = jQuery(this.form),
		      $win = jQuery(window);

		    function remove() {
		      if ($input.val() === title && $input.hasClass(blurClass)) {
		        $input.val('').removeClass(blurClass);
		      }
		    }

		    // only apply logic if the element has the attribute
		    if (title) { 
		      // on blur, set value to title attr if text is blank
		      $input.blur(function () {
		        if (this.value === '') {
		          $input.val(title).addClass(blurClass);
		        }
		      }).focus(remove).blur(); // now change all inputs to title

		      // clear the pre-defined text when form is submitted
		      $form.submit(remove);
		      $win.unload(remove); // handles Firefox's autocomplete
			  $(".btn_find").click(remove);
		    }
		  });
		};

		$().ready(function() {
			// validate signup form on keyup and submit
			$("#reportForm").validate({
				rules: {
					incident_title: {
						required: true,
						minlength: 3
					},
					incident_description: {
						required: true,
						minlength: 3
					},
					incident_date: {
						required: true,
						date: true
					},
					incident_hour: {
						required: true,
						range: [1,12]
					},
					incident_minute: {
						required: true,
						range: [0,60]
					},
					incident_ampm: {
						required: true
					},
					"incident_category[]": {
						required: true,
						minlength: 1
					},
					latitude: {
						required: true,
						range: [-90,90]
					},
					longitude: {
						required: true,
						range: [-180,180]
					},
					location_name: {
						required: true
					},
					"incident_news[]": {
						url: true
					},
					"incident_video[]": {
						url: true
					}
				},
				messages: {
					incident_title: {
						required: "Please enter a Title",
						minlength: "Your Title must consist of at least 3 characters"
					},
					incident_description: {
						required: "Please enter a Description",
						minlength: "Your Description must be at least 3 characters long"
					},
					incident_date: {
						required: "Please enter a Date",
						date: "Please enter a valid Date"
					},
					incident_hour: {
						required: "Please enter an Hour",
						range: "Please enter a valid Hour"
					},
					incident_minute: {
						required: "Please enter a Minute",
						range: "Please enter a valid Minute"
					},
					incident_ampm: {
						required: "Please enter either AM or PM"
					},
					"incident_category[]": {
						required: "Please select at least one Category",
						minlength: "Please select at least one Category"
					},
					latitude: {
						required: "Please select a valid point on the map",
						range: "Please select a valid point on the map"
					},
					longitude: {
						required: "Please select a valid point on the map",
						range: "Please select a valid point on the map"
					},
					location_name: {
						required: "Please enter a Location Name"
					},
					"incident_news[]": {
						url: "Please enter a valid News link"
					},
					"incident_news[]": {
						url: "Please enter a valid Video link"
					}	
				},
				groups: {
					incident_date_time: "incident_date incident_hour",
					latitude_longitude: "latitude longitude"
				},
				errorPlacement: function(error, element) {
					if (element.attr("name") == "incident_date" || element.attr("name") == "incident_hour" || element.attr("name") == "incident_minute" )
					{
						error.append("#incident_date_time");
					}else if (element.attr("name") == "latitude" || element.attr("name") == "longitude"){
						error.insertAfter("#find_text");
					}else if (element.attr("name") == "incident_category[]"){
						error.insertAfter("#categories");
					}else{
						error.insertAfter(element);
					}
				}
			});
		});
		
		function addFormField(div, field, hidden_id, field_type) {
			var id = document.getElementById(hidden_id).value;
			$("#" + div).append("<div class=\"report_row\" id=\"" + field + "_" + id + "\"><input type=\"" + field_type + "\" name=\"" + field + "[]\" class=\"" + field_type + " long2\" /><a href=\"#\" class=\"add\" onClick=\"addFormField('" + div + "','" + field + "','" + hidden_id + "','" + field_type + "'); return false;\">add</a><a href=\"#\" class=\"rem\"  onClick='removeFormField(\"#" + field + "_" + id + "\"); return false;'>remove</a></div>");

			$("#" + field + "_" + id).effect("highlight", {}, 800);

			id = (id - 1) + 2;
			document.getElementById(hidden_id).value = id;
		}

		function removeFormField(id) {
			var answer = confirm("Are You Sure You Want To Delete This Item?");
		    if (answer){
				$(id).remove();
		    }
			else{
				return false;
		    }
		}
		
		
		$(document).ready(function() {
			var map;
			var thisLayer;
			var proj_4326 = new OpenLayers.Projection('EPSG:4326');
			var proj_900913 = new OpenLayers.Projection('EPSG:900913');
			
			// Now initialise the map
			var options = {
			units: "m"
			, numZoomLevels: 16
			, controls:[],
			projection: proj_900913,
			'displayProjection': proj_4326
			};
			map = new OpenLayers.Map('divMap', options);

			var default_map = <?php echo $default_map; ?>;
			if (default_map == 2)
			{
				map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else if (default_map == 3)
			{
				map_layer = new OpenLayers.Layer.Yahoo("yahoo", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else if (default_map == 4)
			{
				map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else
			{
				map_layer = new OpenLayers.Layer.Google("google", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
				
			// Add the layer to the map object
			map.addLayer(map_layer);
			
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoom());
			map.addControl(new OpenLayers.Control.MousePosition(
					{ div: 	document.getElementById('mapMousePosition'), numdigits: 5 
				}));    
			map.addControl(new OpenLayers.Control.Scale('mapScale'));
            map.addControl(new OpenLayers.Control.ScaleLine());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			// Create the markers layer
			var markers = new OpenLayers.Layer.Markers("Markers");
			map.addLayer(markers);
			
			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			myPoint.transform(proj_4326, map.getProjectionObject());
			
			// create a marker positioned at a lon/lat
			var marker = new OpenLayers.Marker(myPoint);
			markers.addMarker(marker);
			
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, <?php echo $default_zoom; ?>);
			
			// Detect Map Clicks
			map.events.register("click", map, function(e){
				var lonlat = map.getLonLatFromViewPortPx(e.xy);
				var lonlat2 = map.getLonLatFromViewPortPx(e.xy);
			    m = new OpenLayers.Marker(lonlat);
				markers.clearMarkers();
		    	markers.addMarker(m);
				
				lonlat2.transform(proj_900913,proj_4326);	
				// Update form values (jQuery)
				$("#latitude").attr("value", lonlat2.lat);
				$("#longitude").attr("value", lonlat2.lon);
			});
			
			// Detect Dropdown Select
			$("#select_city").change(function() {
				var lonlat = $(this).val().split(",");
				if ( lonlat[0] && lonlat[1] )
				{
					l = new OpenLayers.LonLat(lonlat[0], lonlat[1]);
					l.transform(proj_4326, map.getProjectionObject());
					m = new OpenLayers.Marker(l);
					markers.clearMarkers();
			    	markers.addMarker(m);
					map.setCenter(l, <?php echo $default_zoom; ?>);
					
					// Update form values (jQuery)
					$("#location_name").attr("value", $('#select_city :selected').text());
										
					$("#latitude").attr("value", lonlat[1]);
					$("#longitude").attr("value", lonlat[0]);
				}
			});
			
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
								
									// Update form values (jQuery)
									$("#latitude").attr("value", point.lat());
									$("#longitude").attr("value", point.lng());
									$("#location_name").attr("value", $("#location_find").val());
									$('#find_loading').html('');
								}
							}
						);
					}
				}
				return false;
			});
			
			// Textbox Hints
			$("#location_find").hint();
			
			// Toggle Date Editor
			$('a#date_toggle').click(function() {
		    	$('#datetime_edit').show(400);
				$('#datetime_default').hide();
		    	return false;
			});
			
			// Category treeview
	      $("#category-column-1,#category-column-2").treeview({
	        persist: "location",
	        collapsed: true,
	        unique: false
	      });
	
		});
