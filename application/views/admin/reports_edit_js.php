<?php
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
?>
		var map;
		var thisLayer;
		var proj_4326 = new OpenLayers.Projection('EPSG:4326');
		var proj_900913 = new OpenLayers.Projection('EPSG:900913');
		var vlayer;
		var highlightCtrl;
		var selectCtrl;
		var selectedFeatures = [];
		
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

		$(document).ready(function() {
			// Now initialise the map
			var options = {
			units: "m"
			, numZoomLevels: 18
			, controls:[],
			projection: proj_900913,
			'displayProjection': proj_4326,
			eventListeners: {
					"zoomend": incidentZoom
			    },
			};
			map = new OpenLayers.Map('divMap', options);
			
			<?php echo map::layers_js(FALSE); ?>
			map.addLayers(<?php echo map::layers_array(FALSE); ?>);
			
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			// Vector/Drawing Layer Styles
			style1 = new OpenLayers.Style({
				pointRadius: "8",
				fillColor: "#ffcc66",
				fillOpacity: "0.7",
				strokeColor: "#CC0000",
				strokeWidth: 2.5,
				graphicZIndex: 1,
				externalGraphic: "<?php echo url::base().'media/img/openlayers/marker.png' ;?>",
				graphicOpacity: 1,
				graphicWidth: 21,
				graphicHeight: 25,
				graphicXOffset: -14,
				graphicYOffset: -27
			});
			style2 = new OpenLayers.Style({
				pointRadius: "8",
				fillColor: "#30E900",
				fillOpacity: "0.7",
				strokeColor: "#197700",
				strokeWidth: 2.5,
				graphicZIndex: 1,
				externalGraphic: "<?php echo url::base().'media/img/openlayers/marker-green.png' ;?>",
				graphicOpacity: 1,
				graphicWidth: 21,
				graphicHeight: 25,
				graphicXOffset: -14,
				graphicYOffset: -27
			});
			style3 = new OpenLayers.Style({
				pointRadius: "8",
				fillColor: "#30E900",
				fillOpacity: "0.7",
				strokeColor: "#197700",
				strokeWidth: 2.5,
				graphicZIndex: 1
			});
			
			var vlayerStyles = new OpenLayers.StyleMap({
				"default": style1,
				"select": style2,
				"temporary": style3
			});
			
			// Create Vector/Drawing layer
			vlayer = new OpenLayers.Layer.Vector( "Editable", {
				styleMap: vlayerStyles,
				rendererOptions: {zIndexing: true}
			});
			map.addLayer(vlayer);
			
			
			// Drag Control
			var drag = new OpenLayers.Control.DragFeature(vlayer, {
				onStart: startDrag,
				onDrag: doDrag,
				onComplete: endDrag
			});
			map.addControl(drag);
			
			// Vector Layer Events
			vlayer.events.on({
				beforefeaturesadded: function(event) {
					//for(i=0; i < vlayer.features.length; i++) {
					//	if (vlayer.features[i].geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
					//		vlayer.removeFeatures(vlayer.features);
					//	}
					//}
					
					// Disable this to add multiple points
					// vlayer.removeFeatures(vlayer.features);
				},
				featuresadded: function(event) {
					refreshFeatures(event);
				},
				featuremodified: function(event) {
					refreshFeatures(event);
				},
				featuresremoved: function(event) {
					refreshFeatures(event);
				}
			});
			
			// Vector Layer Highlight Features
			highlightCtrl = new OpenLayers.Control.SelectFeature(vlayer, {
			    hover: true,
			    highlightOnly: true,
			    renderIntent: "temporary"
			});
			selectCtrl = new OpenLayers.Control.SelectFeature(vlayer, {
				clickout: true, toggle: true,
				multiple: true, hover: false,
				renderIntent: "select",
				onSelect: addSelected,
				onUnselect: clearSelected
			});
			
			// Insert Saved Geometries
			wkt = new OpenLayers.Format.WKT();
			<?php
			if ( ! count($geometries))
			{
				?>
				// Default Point
				point = new OpenLayers.Geometry.Point(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
				OpenLayers.Projection.transform(point, proj_4326, map.getProjectionObject());
				var origFeature = new OpenLayers.Feature.Vector(point);
				vlayer.addFeatures(origFeature);
				<?php
			}
			else
			{
				foreach ($geometries as $geometry)
				{
					echo "wktFeature = wkt.read('$geometry');\n";
					echo "wktFeature.geometry.transform(proj_4326,proj_900913);\n";
					echo "vlayer.addFeatures(wktFeature);\n";
				}
			}
			?>
			
			// create a lat/lon object
			var startPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			startPoint.transform(proj_4326, map.getProjectionObject());
			
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(startPoint, <?php echo ($incident_zoom) ? $incident_zoom : $default_zoom; ?>);
			
			// Create the Editing Toolbar
			var container = document.getElementById("panel");
			var panel = new OpenLayers.Control.EditingToolbar(
				vlayer, {div: container}
			);
			map.addControl(panel);
			panel.activateControl(panel.controls[0]);
			
			// Highlight / Select Controls
			map.addControl(highlightCtrl);
			map.addControl(selectCtrl);
			drag.activate();
			highlightCtrl.activate();
			selectCtrl.activate();
			
			// Undo Action Removes Most Recent Marker
			$('.btn_del_last').live('click', function () {
				if (vlayer.features.length > 0) {
					x = vlayer.features.length - 1;
					vlayer.removeFeatures(vlayer.features[x]);
				}
			});
			
			// Delete Selected Features
			$('.btn_del_sel').live('click', function () {
				for(var y=0; y < selectedFeatures.length; y++) {
					vlayer.removeFeatures(selectedFeatures);
				}
			});
			
			// Clear Map
			$('.btn_clear').live('click', function () {
				vlayer.removeFeatures(vlayer.features);
				$('input[name="geometry[]"]').remove();
				$("#latitude").val("");
				$("#longitude").val("");
			});
			
			// GeoCode
			$('.btn_find').live('click', function () {
				geoCode();
			});
			$('#location_find').bind('keypress', function(e) {
				var code = (e.keyCode ? e.keyCode : e.which);
				if(code == 13) { //Enter keycode
					geoCode();
					return false;
				}
			});
			
			// Event on Latitude/Longitude Typing Change
			$('#latitude, #longitude').bind("change keyup", function() {
				var newlat = $("#latitude").val();
				var newlon = $("#longitude").val();
				if (!isNaN(newlat) && !isNaN(newlon))
				{
					var lonlat = new OpenLayers.LonLat(newlon, newlat);
					lonlat.transform(proj_4326,proj_900913);
					m = new OpenLayers.Marker(lonlat);
					markers.clearMarkers();
			    	markers.addMarker(m);
					map.setCenter(lonlat, <?php echo $default_zoom; ?>);
				}
				else
				{
					alert('Invalid value!')
				}
			});
			
			/* Form Actions */
			// Action on Save Only
			$('.btn_save').live('click', function () {
				$("#save").attr("value", "1");
				$(this).parents("form").submit();
				return false;
			});
			
			$('.btn_save_close').live('click', function () {
				$(this).parents("form").submit();
				return false;
			});
			
			// Delete Action
			$('.btn_delete').live('click', function () {
				var agree=confirm("<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> <?php echo Kohana::lang('ui_admin.delete_action'); ?>?");
				if (agree){
					$('#reportMain').submit();
				}
				return false;
			});
			
			// Toggle Date Editor
			$('a#date_toggle').click(function() {
		    	$('#datetime_edit').show(400);
				$('#datetime_default').hide();
		    	return false;
			});
			
			// Show Messages Box
		    $('a#messages_toggle').click(function() {
		    	$('#show_messages').toggle(400);
		    	return false;
			});
			
			// Textbox Hints
			$("#location_find").hint();
			
			/* Dynamic categories */
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
		
			// Category treeview
			$("#category-column-1,#category-column-2").treeview({
			  persist: "location",
			  collapsed: true,
			  unique: false
			});
			
			// Date Picker JS
			$("#incident_date").datepicker({ 
			    showOn: "both", 
			    buttonImage: "<?php echo url::base() ?>media/img/icon-calendar.gif", 
			    buttonImageOnly: true 
			});
			
			// Handles the functionality for changing the size of the map
			// TODO: make the CSS widths dynamic... instead of hardcoding, grab the width's
			// from the appropriate parent divs
			$('.map-toggles a').click(function() {
				var action = $(this).attr("class");
				$('ul.map-toggles li').hide();
				switch(action)
				{
					case "wider-map":
						$('.incident-location').insertBefore($('.f-col'));
						$('.map_holder_reports').css({"height":"350px", "width": "935px"});
						$('.incident-location h4').css({"margin-left":"10px"});
						$('.location-info').css({"margin-right":"14px"});
						$('a[href=#report-map]').parent().hide();
						$('a.taller-map').parent().show();
						$('a.smaller-map').parent().show();
						break;
					case "taller-map":
						$('.map_holder_reports').css("height","600px");
						$('a.shorter-map').parent().show();
						$('a.smaller-map').parent().show();
						break;
					case "shorter-map":
						$('.map_holder_reports').css("height","350px");
						$('a.taller-map').parent().show();
						$('a.smaller-map').parent().show();
						break;
					case "smaller-map":
						$('.incident-location').hide().prependTo($('.f-col-1'));
						$('.map_holder_reports').css({"height":"350px", "width": "494px"});
						$('a.wider-map').parent().show();
						$('.incident-location').show();
						$('.incident-location h4').css({"margin-left":"0"});
						$('.location-info').css({"margin-right":"0"});
						break;
				};
				
				map.setCenter(map.getCenter(), map.getZoom());
				
				return false;
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
			var answer = confirm("<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to_delete_this_item'); ?>?");
		    if (answer){
				$(id).remove();
		    }
			else{
				return false;
		    }
		}
		
		function deletePhoto (id, div)
		{
			var answer = confirm("<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to_delete_this_photo'); ?>?");
		    if (answer){
				$("#" + div).effect("highlight", {}, 800);
				$.get("<?php echo url::base() . 'admin/reports/deletePhoto/' ?>" + id);
				$("#" + div).remove();
		    }
			else{
				return false;
		    }
		}
		
		/**
		 * Google GeoCoder
		 */
		function geoCode()
		{
			$('#find_loading').html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
			address = $("#location_find").val();
			$.post("<?php echo url::site() . 'reports/geocode/' ?>", { address: address },
				function(data){
					if (data.status == 'success'){
						// Clear the map first
						vlayer.removeFeatures(vlayer.features);
						$('input[name="geometry[]"]').remove();
						
						point = new OpenLayers.Geometry.Point(data.message[1], data.message[0]);
						OpenLayers.Projection.transform(point, proj_4326,proj_900913);
						
						f = new OpenLayers.Feature.Vector(point);
						vlayer.addFeatures(f);
						
						// create a new lat/lon object
						myPoint = new OpenLayers.LonLat(data.message[1], data.message[0]);
						myPoint.transform(proj_4326, map.getProjectionObject());

						// display the map centered on a latitude and longitude
						map.setCenter(myPoint, <?php echo $default_zoom; ?>);
						
						// Update form values
						$("#latitude").attr("value", data.message[0]);
						$("#longitude").attr("value", data.message[1]);
						$("#location_name").attr("value", $("#location_find").val());
					} else {
						alert(address + " not found!\n\n***************************\nEnter more details like city, town, country\nor find a city or town close by and zoom in\nto find your precise location");
					}
					$('#find_loading').html('');
				}, "json");
			return false;
		}
		
		function formSwitch(form_id, incident_id)
		{
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to_switch_forms'); ?>?');
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
		
		/* Keep track of the selected features */
		function addSelected(feature) {
		    selectedFeatures.push(feature);
		}

		/* Clear the list of selected features */
		function clearSelected(feature) {
		    selectedFeatures = [];
		}

		/* Feature starting to move */
		function startDrag(feature, pixel) {
		    lastPixel = pixel;
		}

		/* Feature moving */
		function doDrag(feature, pixel) {
		    for (f in selectedFeatures) {
		        if (feature != selectedFeatures[f]) {
		            var res = map.getResolution();
		            selectedFeatures[f].geometry.move(res * (pixel.x - lastPixel.x), res * (lastPixel.y - pixel.y));
		            vlayer.drawFeature(selectedFeatures[f]);
		        }
		    }
		    lastPixel = pixel;
		}

		/* Featrue stopped moving */
		function endDrag(feature, pixel) {
		    for (f in selectedFeatures) {
		        f.state = OpenLayers.State.UPDATE;
		    }
			refreshFeatures();
		}
		
		function refreshFeatures(event)
		{
			var geoCollection = new OpenLayers.Geometry.Collection;
			$('input[name="geometry[]"]').remove();
			for(i=0; i < vlayer.features.length; i++) {
				newFeature = vlayer.features[i].clone();
				newFeature.geometry.transform(proj_900913,proj_4326);
				geoCollection.addComponents(newFeature.geometry);
				
				if (vlayer.features.length == 1 && vlayer.features[i].geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
					// If feature is a Single Point - save as lat/lon
				} else {
					// Otherwise, save geometry values
					// Convert to Well Known Text
					var format = new OpenLayers.Format.WKT();
					$('#reportForm').append($('<input></input>').attr('name','geometry[]').attr('type','hidden').attr('value',format.write(newFeature)));
				}
			}
			
			// Centroid of location will constitute the Location
			// if its not a point
			centroid = geoCollection.getCentroid(true);
			$("#latitude").val(centroid.y);
			$("#longitude").val(centroid.x);
		}
		
		function incidentZoom(event)
		{
			$("#incident_zoom").val(map.getZoom())
		}