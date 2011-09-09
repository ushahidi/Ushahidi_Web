<?php
/**
 * Handles javascript stuff related to report creation and editing
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Reports
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
		    // Get jQuery version of 'this'
		    var $input = jQuery(this),

		    // Capture the rest of the variable to allow for reuse
		      title = $input.attr('title'),
		      $form = jQuery(this.form),
		      $win = jQuery(window);

		    function remove() {
		      if ($input.val() === title && $input.hasClass(blurClass)) {
		        $input.val('').removeClass(blurClass);
		      }
		    }

		    // Only apply logic if the element has the attribute
		    if (title) { 
			
		      // On blur, set value to title attr if text is blank
		      $input.blur(function () {
		        if (this.value === '') {
		          $input.val(title).addClass(blurClass);
		        }
		      }).focus(remove).blur(); // now change all inputs to title

		      // Clear the pre-defined text when form is submitted
		      $form.submit(remove);
		      $win.unload(remove); // handles Firefox's autocomplete
			  $(".btn_find").click(remove);
		    }
		  });
		};

		jQuery(window).load(function() {
			// Map options
			var options = {
				units: "m",
				numZoomLevels: 18, 
				controls:[],
				theme: false,
				projection: proj_900913,
				'displayProjection': proj_4326,
				eventListeners: {
					"zoomend": incidentZoom
				}
			};
			
			// Now initialise the map
			map = new OpenLayers.Map('divMap', options);
			
			<?php echo map::layers_js(FALSE); ?>
			map.addLayers(<?php echo map::layers_array(FALSE); ?>);
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.ScaleLine());
			map.addControl(new OpenLayers.Control.Scale('mapScale'));
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			// Vector/Drawing Layer Styles
			style1 = new OpenLayers.Style({
				pointRadius: "8",
				fillColor: "#ffcc66",
				fillOpacity: "0.7",
				strokeColor: "#CC0000",
				strokeWidth: 2.5,
				graphicZIndex: 1,
				externalGraphic: "<?php echo url::file_loc('img').'media/img/openlayers/marker.png' ;?>",
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
				externalGraphic: "<?php echo url::file_loc('img').'media/img/openlayers/marker-green.png' ;?>",
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
				clickout: true, toggle: false,
				multiple: false, hover: false,
				renderIntent: "select",
				onSelect: addSelected,
				onUnselect: clearSelected
			});
			map.addControl(highlightCtrl);
			map.addControl(selectCtrl);
			
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
					$geometry = json_decode($geometry);
					echo "wktFeature = wkt.read('$geometry->geometry');\n";
					echo "wktFeature.geometry.transform(proj_4326,proj_900913);\n";
					echo "wktFeature.label = '$geometry->label';\n";
					echo "wktFeature.comment = '$geometry->comment';\n";
					echo "wktFeature.color = '$geometry->color';\n";
					echo "wktFeature.strokewidth = '$geometry->strokewidth';\n";
					echo "vlayer.addFeatures(wktFeature);\n";
					echo "var color = '$geometry->color';if (color) {updateFeature(wktFeature, color, '');};";
					echo "var strokewidth = '$geometry->strokewidth';if (strokewidth) {updateFeature(wktFeature, '', strokewidth);};";
				}
			}
			?>
			
			
			// Create a lat/lon object
			var startPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			startPoint.transform(proj_4326, map.getProjectionObject());
			
			// Display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(startPoint, <?php echo ($incident_zoom) ? $incident_zoom : $default_zoom; ?>);
			
			// Create the Editing Toolbar
			var container = document.getElementById("panel");
			var panel = new OpenLayers.Control.EditingToolbar(
				vlayer, {div: container}
			);
			map.addControl(panel);
			panel.activateControl(panel.controls[0]);
			drag.activate();
			highlightCtrl.activate();
			selectCtrl.activate();
			
			map.events.register("click", map, function(e){
				selectCtrl.deactivate();
				selectCtrl.activate();
			});
			
			// Undo Action Removes Most Recent Marker
			$('.btn_del_last').live('click', function () {
				if (vlayer.features.length > 0) {
					x = vlayer.features.length - 1;
					vlayer.removeFeatures(vlayer.features[x]);
				}
				$('#geometry_color').ColorPickerHide();
				$('#geometryLabelerHolder').hide(400);
				selectCtrl.activate();
			});
			
			// Delete Selected Features
			$('.btn_del_sel').live('click', function () {
				for(var y=0; y < selectedFeatures.length; y++) {
					vlayer.removeFeatures(selectedFeatures);
				}
				$('#geometry_color').ColorPickerHide();
				$('#geometryLabelerHolder').hide(400);
				selectCtrl.activate();
			});
			
			// Clear Map
			$('.btn_clear').live('click', function () {
				vlayer.removeFeatures(vlayer.features);
				$('input[name="geometry[]"]').remove();
				$("#latitude").val("");
				$("#longitude").val("");
				$('#geometry_label').val("");
				$('#geometry_comment').val("");
				$('#geometry_color').val("");
				$('#geometry_lat').val("");
				$('#geometry_lon').val("");
				$('#geometry_color').ColorPickerHide();
				$('#geometryLabelerHolder').hide(400);
				selectCtrl.activate();
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
					// Clear the map first
					vlayer.removeFeatures(vlayer.features);
					$('input[name="geometry[]"]').remove();
					
					point = new OpenLayers.Geometry.Point(newlon, newlat);
					OpenLayers.Projection.transform(point, proj_4326,proj_900913);
					
					f = new OpenLayers.Feature.Vector(point);
					vlayer.addFeatures(f);
					
					// create a new lat/lon object
					myPoint = new OpenLayers.LonLat(newlon, newlat);
					myPoint.transform(proj_4326, map.getProjectionObject());

					// display the map centered on a latitude and longitude
					map.setCenter(myPoint, <?php echo $default_zoom; ?>);
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
			<?php if ($edit_mode): ?>
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
		
				$.post("<?php echo url::base() . 'admin/reports/save_category/' ?>", 
					{ category_title: category_name, category_description: category_description, category_color: category_color },
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
			<?php endif; ?>
		
			// Category treeview
			$("#category-column-1,#category-column-2").treeview({
			  persist: "location",
			  collapsed: true,
			  unique: false
			});
			
			// Date Picker JS
			$("#incident_date").datepicker({ 
			    showOn: "both", 
			    buttonImage: "<?php echo url::file_loc('img') ?>media/img/icon-calendar.gif", 
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
				
				map.updateSize();
				map.pan(0,1);
				
				return false;
			});
			
			
			// Prevent Map Effects in the Geometry Labeler
			$('#geometryLabelerHolder').click(function(evt) {
				var e = evt ? evt : window.event; 
				OpenLayers.Event.stop(e);
				return false;
			});
			
			// Geometry Label Text Boxes
			$('#geometry_label').click(function() {
				$('#geometry_label').focus();
				$('#geometry_color').ColorPickerHide();
			}).bind("change keyup blur", function(){
				for (f in selectedFeatures) {
					selectedFeatures[f].label = this.value;
				}
				refreshFeatures();
			});
			
			$('#geometry_comment').click(function() {
				$('#geometry_comment').focus();
				$('#geometry_color').ColorPickerHide();
			}).bind("change keyup blur", function(){
				for (f in selectedFeatures) {
					selectedFeatures[f].comment = this.value;
			    }
				refreshFeatures();
			});
			
			$('#geometry_lat').click(function() {
				$('#geometry_lat').focus();
				$('#geometry_color').ColorPickerHide();
			}).bind("change keyup blur", function(){
				for (f in selectedFeatures) {
					selectedFeatures[f].lat = this.value;
			    }
				refreshFeatures();
			});
			
			$('#geometry_lon').click(function() {
				$('#geometry_lon').focus();
				$('#geometry_color').ColorPickerHide();
			}).bind("change keyup blur", function(){
				for (f in selectedFeatures) {
					selectedFeatures[f].lon = this.value;
			    }
				refreshFeatures();
			});
			
			// Event on Latitude/Longitude Typing Change
			$('#geometry_lat, #geometry_lon').bind("change keyup", function() {
				var newlat = $("#geometry_lat").val();
				var newlon = $("#geometry_lon").val();
				if (!isNaN(newlat) && !isNaN(newlon))
				{
					var lonlat = new OpenLayers.LonLat(newlon, newlat);
					lonlat.transform(proj_4326,proj_900913);
					for (f in selectedFeatures) {
						selectedFeatures[f].geometry.x = lonlat.lon;
						selectedFeatures[f].geometry.y = lonlat.lat;
						selectedFeatures[f].lon = newlat;
						selectedFeatures[f].lat = newlon;
						vlayer.drawFeature(selectedFeatures[f]);
				    }
				}
				else
				{
					alert('Invalid value!')
				}
			});
				
			// Event on Color Change
			$('#geometry_color').ColorPicker({
				onSubmit: function(hsb, hex, rgb) {
					$('#geometry_color').val(hex);
					for (f in selectedFeatures) {
						selectedFeatures[f].color = hex;
						updateFeature(selectedFeatures[f], hex, '');
				    }
					refreshFeatures();
				},
				onChange: function(hsb, hex, rgb) {
					$('#geometry_color').val(hex);
					for (f in selectedFeatures) {
						selectedFeatures[f].color = hex;
						updateFeature(selectedFeatures[f], hex, '');
				    }
					refreshFeatures();
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
					for (f in selectedFeatures) {
						selectedFeatures[f].color = this.value;
						updateFeature(selectedFeatures[f], this.value, '');
				    }
					refreshFeatures();
				}
			}).bind('keyup', function(){
				$(this).ColorPickerSetColor(this.value);
				for (f in selectedFeatures) {
					selectedFeatures[f].color = this.value;
					updateFeature(selectedFeatures[f], this.value, '');
			    }
				refreshFeatures();
			});
			
			// Event on StrokeWidth Change
			$('#geometry_strokewidth').bind("change keyup", function() {
				if (parseFloat(this.value) && parseFloat(this.value) <= 8) {
					for (f in selectedFeatures) {
						selectedFeatures[f].strokewidth = this.value;
						updateFeature(selectedFeatures[f], '', parseFloat(this.value));
					}
					refreshFeatures();
				}
			});
			
			// Close Labeler
			$('#geometryLabelerClose').click(function() {
				$('#geometryLabelerHolder').hide(400);
				for (f in selectedFeatures) {
					selectCtrl.unselect(selectedFeatures[f]);
				}
				selectCtrl.activate();
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
			$('#find_loading').html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
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
				$('#form_loader').html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
				$.post("<?php echo url::site().'reports/switch_form'; ?>", { form_id: form_id, incident_id: incident_id },
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
			selectCtrl.activate();
			if (vlayer.features.length == 1 && feature.geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
				// This is a single point, no need for geometry metadata
			} else {
				$('#geometryLabelerHolder').show(400);
				if (feature.geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
					$('#geometryLat').show();
					$('#geometryLon').show();
					$('#geometryColor').hide();
					$('#geometryStrokewidth').hide();
					thisPoint = feature.clone();
					thisPoint.geometry.transform(proj_900913,proj_4326);
					$('#geometry_lat').val(thisPoint.geometry.y);
					$('#geometry_lon').val(thisPoint.geometry.x);
				} else {
					$('#geometryLat').hide();
					$('#geometryLon').hide();
					$('#geometryColor').show();
					$('#geometryStrokewidth').show();
				}
				if ( typeof(feature.label) != 'undefined') {
					$('#geometry_label').val(feature.label);
				}
				if ( typeof(feature.comment) != 'undefined') {
					$('#geometry_comment').val(feature.comment);
				}
				if ( typeof(feature.lon) != 'undefined') {
					$('#geometry_lon').val(feature.lon);
				}
				if ( typeof(feature.lat) != 'undefined') {
					$('#geometry_lat').val(feature.lat);
				}
				if ( typeof(feature.color) != 'undefined') {
					$('#geometry_color').val(feature.color);
				}
				if ( typeof(feature.strokewidth) != 'undefined' && feature.strokewidth != '') {
					$('#geometry_strokewidth').val(feature.strokewidth);
				} else {
					$('#geometry_strokewidth').val("2.5");
				}
			}
		}

		/* Clear the list of selected features */
		function clearSelected(feature) {
		    selectedFeatures = [];
			$('#geometryLabelerHolder').hide(400);
			$('#geometry_label').val("");
			$('#geometry_comment').val("");
			$('#geometry_color').val("");
			$('#geometry_lat').val("");
			$('#geometry_lon').val("");
			selectCtrl.deactivate();
			selectCtrl.activate();
			$('#geometry_color').ColorPickerHide();
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
			// Fetching Lat Lon Values
		  	var latitude = parseFloat($("#latitude").val());
			var longitude = parseFloat($("#longitude").val());
			// Looking up country name using reverse geocoding
			var latlng = new google.maps.LatLng(latitude, longitude);
			reverseGeocode(latlng);
		}
		
		function refreshFeatures(event) {
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
					var geometry = format.write(newFeature);
					var label = '';
					var comment = '';
					var lon = '';
					var lat = '';
					var color = '';
					var strokewidth = '';
					if ( typeof(vlayer.features[i].label) != 'undefined') {
						label = vlayer.features[i].label;
					}
					if ( typeof(vlayer.features[i].comment) != 'undefined') {
						comment = vlayer.features[i].comment;
					}
					if ( typeof(vlayer.features[i].lon) != 'undefined') {
						lon = vlayer.features[i].lon;
					}
					if ( typeof(vlayer.features[i].lat) != 'undefined') {
						lat = vlayer.features[i].lat;
					}
					if ( typeof(vlayer.features[i].color) != 'undefined') {
						color = vlayer.features[i].color;
					}
					if ( typeof(vlayer.features[i].strokewidth) != 'undefined') {
						strokewidth = vlayer.features[i].strokewidth;
					}
					geometryAttributes = JSON.stringify({ geometry: geometry, label: label, comment: comment,lat: lat, lon: lon, color: color, strokewidth: strokewidth});
					$('#reportForm').append($('<input></input>').attr('name','geometry[]').attr('type','hidden').attr('value',geometryAttributes));
				}
			}
			
			// Centroid of location will constitute the Location
			// if its not a point
			centroid = geoCollection.getCentroid(true);
			$("#latitude").val(centroid.y);
			$("#longitude").val(centroid.x);
		}
		
		function incidentZoom(event) {
			$("#incident_zoom").val(map.getZoom());
		}
		
		function updateFeature(feature, color, strokeWidth){
		
			// Create a symbolizer from exiting stylemap
			var symbolizer = feature.layer.styleMap.createSymbolizer(feature);
			
			// Color available?
			if (color) {
				symbolizer['fillColor'] = "#"+color;
				symbolizer['strokeColor'] = "#"+color;
				symbolizer['fillOpacity'] = "0.7";
			} else {
				if ( typeof(feature.color) != 'undefined' && feature.color != '' ) {
					symbolizer['fillColor'] = "#"+feature.color;
					symbolizer['strokeColor'] = "#"+feature.color;
					symbolizer['fillOpacity'] = "0.7";
				}
			}
			
			// Stroke available?
			if (parseFloat(strokeWidth)) {
				symbolizer['strokeWidth'] = parseFloat(strokeWidth);
			} else if ( typeof(feature.strokewidth) != 'undefined' && feature.strokewidth !='' ) {
				symbolizer['strokeWidth'] = feature.strokewidth;
			} else {
				symbolizer['strokeWidth'] = "2.5";
			}
			
			// Set the unique style to the feature
			feature.style = symbolizer;

			// Redraw the feature with its new style
			feature.layer.drawFeature(feature);
		}
		
		// Reverse GeoCoder
		function reverseGeocode(latlng)
		{
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode({'latLng': latlng}, function(results, status){
				if (status == google.maps.GeocoderStatus.OK) {
					var country = results[0].address_components[4].long_name;
					$("#country_name").val(country);
      			} else {
        			console.log("Geocoder failed due to: " + status);
      			}
			});
		}