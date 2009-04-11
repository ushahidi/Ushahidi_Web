/**
 * Edit reports js file.
 *
 * Handles javascript stuff related to edit report function.
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

		/* Dynamic categories */
		$(document).ready(function() {
			$('#category_add').hide();
		    $('#add_new_category').click(function() { 
		        var category_name = $("input#category_name").val();
		        var category_description = $("input#category_description").val();
		        var category_color = $("input#category_color").val();

		        //trim the form fields
                        //Removed ".toUpperCase()" from name and desc for Ticket #38
		        category_name = category_name.replace(/^\s+|\s+$/g, '');
		        category_description = category_description.replace(/^\s+|\s+$/g,'');
		        category_color = category_color.replace(/^\s+|\s+$/g, '').toUpperCase();
        
		        if (!category_name || !category_description || !category_color) {
		            alert("Please fill in all the fields");
		            return false;
		        }
        
		        //category_color = category_color.toUpperCase();

		        re = new RegExp("[^ABCDEF0123456789]"); //Color values are in hex
		        if (re.test(category_color) || category_color.length != 6) {
		            alert("Please use the Color picker to help you choose a color");
		            return false;
		        }
		
				$.post("<?php echo url::base() . 'admin/reports/save_category/' ?>", { category_title: category_name, category_description: category_description, category_color: category_color },
					function(data){
						if ( data.status == 'saved')
						{
							// alert(category_name+" "+category_description+" "+category_color);
					        $('#user_categories').append("<li><label><input type=\"checkbox\"name=\"incident_category[]\" value=\""+data.id+"\" class=\"check-box\" checked />"+category_name+"</label></li>");
							$('#category_add').hide();
						}
						else
						{
							alert("Your submission had errors!!");
						}
					}, "json");
		        return false; 
		    });
		}); 


		// Date Picker JS
		$(document).ready(function() {
			$("#incident_date").datepicker({ 
			    showOn: "both", 
			    buttonImage: "<?php echo url::base() ?>media/img/icon-calendar.gif", 
			    buttonImageOnly: true 
			});
		});
		
		function addFormField(div, field, hidden_id, field_type) {
			var id = document.getElementById(hidden_id).value;
			$("#" + div).append("<div class=\"row link-row second\" id=\"" + field + "_" + id + "\"><input type=\"" + field_type + "\" name=\"" + field + "[]\" class=\"" + field_type + " long\" /><a href=\"#\" class=\"add\" onClick=\"addFormField('" + div + "','" + field + "','" + hidden_id + "','" + field_type + "'); return false;\">add</a><a href=\"#\" class=\"rem\"  onClick='removeFormField(\"#" + field + "_" + id + "\"); return false;'>remove</a></div>");

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
		
		function deletePhoto (id, div)
		{
			var answer = confirm("Are You Sure You Want To Delete This Photo?");
		    if (answer){
				$("#" + div).effect("highlight", {}, 800);
				$.get("<?php echo url::base() . 'admin/reports/deletePhoto/' ?>" + id);
				$("#" + div).remove();
		    }
			else{
				return false;
		    }
		}
		
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
			map.addControl(new OpenLayers.Control.MousePosition());
			
			// Create the markers layer
			var markers = new OpenLayers.Layer.Markers("Markers");
			map.addLayer(markers);
			
			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			
			// create a marker positioned at a lon/lat
			var marker = new OpenLayers.Marker(myPoint);
			markers.addMarker(marker);
			
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, <?php echo $default_zoom; ?>);
			
			// Detect Map Clicks
			map.events.register("click", map, function(e){
				var lonlat = map.getLonLatFromViewPortPx(e.xy);
			    m = new OpenLayers.Marker(lonlat);
				markers.clearMarkers();
		    	markers.addMarker(m);
							
				// Update form values (jQuery)
				$("#latitude").attr("value", lonlat.lat);
				$("#longitude").attr("value", lonlat.lon);
			});
			
			$("#findAddress").click(function () {
				var selected = $("#country_id option[@selected]");
				address = $("#location_name").val() + ', ' + selected.text();
				var geocoder = new GClientGeocoder();
				if (geocoder) {
					geocoder.getLatLng(
						address,
						function(point) {
							if (!point) {
								alert(address + " not found!\n\n***************************\nFind a city or town close by and zoom in\nto find your precise location");
							} else {
								var lonlat = new OpenLayers.LonLat(point.lng(), point.lat());
								m = new OpenLayers.Marker(lonlat);
								markers.clearMarkers();
						    	markers.addMarker(m);
								map.setCenter(lonlat, <?php echo $default_zoom; ?>);
								
								// Update form values (jQuery)
								$("#latitude").attr("value", lonlat.lat);
								$("#longitude").attr("value", lonlat.lon);
							}
						}
					);
				}
			});
			
			// Action on Save Only
			$("#save_only").click(function () {
				$("#save").attr("value", "1");
			});
			
			// Action on Cancel
			$("#cancel").click(function () {
				window.location.href='<?php echo url::base() . 'admin/reports/' ?>';
				return false;
			});
			
			// Prevent Enter Button Submit
			$("#reportForm").bind("keypress", function(e) {
			  if (e.keyCode == 13) return false;
			});
			
			// Show Messages Box
		    $('a#messages_toggle').click(function() {
		    $('#show_messages').toggle(400);
		    return false;
			});
		});
		
		function formSwitch(form_id, incident_id)
		{
			var answer = confirm('Are You Sure You Want To SWITCH Forms?');
			if (answer){
				$('#form_loader').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
				$.post("<?php echo url::base() . 'admin/reports/switch_form' ?>", { form_id: form_id, incident_id: incident_id },
					function(data){
						if (data.status == 'success'){
							$('#custom_forms').html('');
							$('#custom_forms').html(unescape(data.response));
							$('#form_loader').html('');
						}
				  	}, "json");
			}
		}