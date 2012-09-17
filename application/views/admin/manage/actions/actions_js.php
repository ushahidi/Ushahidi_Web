<?php
/**
 * Actions JS file.
 *
 * Handles javascript stuff related to editing actions
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
var selectedFeatures = [];

$(document).ready(function() {
	// Now initialize the map
	var options = {
	units: "dd"
	, numZoomLevels: 18
	, controls:[],
	projection: proj_900913,
	'displayProjection': proj_4326,
	eventListeners: {
			"zoomend": incidentZoom
	   },
				maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
				maxResolution: 156543.0339
	};
	map = new OpenLayers.Map('divMap', options);

	<?php echo map::layers_js(FALSE); ?>
	map.addLayers(<?php echo map::layers_array(FALSE); ?>);

	map.addControl(new OpenLayers.Control.Navigation());
	map.addControl(new OpenLayers.Control.Zoom());
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
			// Nothing here.
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
	map.addControl(highlightCtrl);

	// Insert Saved Geometries
	wkt = new OpenLayers.Format.WKT();
	<?php
	if (count($geometries))
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

	drag.activate();
	highlightCtrl.activate();


	// Clear Map
	$('.btn_clear').on('click', function () {
		clear_everything();
	});

	function clear_everything(){
		vlayer.removeFeatures(vlayer.features);
		$('input[name="geometry[]"]').remove();
		$("#latitude").val("");
		$("#longitude").val("");
		$('#geometry_lat').val("");
		$('#geometry_lon').val("");
		$('#geometryLabelerHolder').hide(400);
	}

	// GeoCode
	$('.btn_find').on('click', function () {
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




	// Prevent Map Effects in the Geometry Labeler
	$('#geometryLabelerHolder').click(function(evt) {
		var e = evt ? evt : window.event;
		OpenLayers.Event.stop(e);
		return false;
	});

	$('#geometry_lat').click(function() {
		$('#geometry_lat').focus();
	}).bind("change keyup blur", function(){
		for (f in selectedFeatures) {
			selectedFeatures[f].lat = this.value;
	    }
		refreshFeatures();
	});

	$('#geometry_lon').click(function() {
		$('#geometry_lon').focus();
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

	// Event on StrokeWidth Change
	$('#geometry_strokewidth').bind("change keyup", function() {
		if (parseFloat(this.value) && parseFloat(this.value) <?php echo '<='; ?> 8) {
			for (f in selectedFeatures) {
				selectedFeatures[f].strokewidth = this.value;
				updateFeature(selectedFeatures[f], '', parseFloat(this.value));
			}
			refreshFeatures();
		}
	});

	hide_map();
	
	
	// Category treeview
	$(".category-column").treeview({
		persist: "location",
		collapsed: true,
		unique: false
	});

});

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

/* Feature stopped moving */
function endDrag(feature, pixel) {
    for (f in selectedFeatures) {
        f.state = OpenLayers.State.UPDATE;
    }
	refreshFeatures();
}

function refreshFeatures(event) {
	var geoCollection = new OpenLayers.Geometry.Collection;
	$('input[name="geometry[]"]').remove();
	for(i=0; i <?php echo '<'; ?> vlayer.features.length; i++) {
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

			geometryAttributes = JSON.stringify({ geometry: geometry });
			$('#actionsMain').append($('<input></input>').attr('name','geometry[]').attr('type','hidden').attr('value',geometryAttributes));
		}
	}
}

function incidentZoom(event) {
	$("#incident_zoom").val(map.getZoom());
}

function updateFeature(feature, color, strokeWidth){
	// create a symbolizer from exiting stylemap
	var symbolizer = feature.layer.styleMap.createSymbolizer(feature);

	// color available?
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

	// stroke available?
	if (parseFloat(strokeWidth)) {
		symbolizer['strokeWidth'] = parseFloat(strokeWidth);
	} else if ( typeof(feature.strokewidth) != 'undefined' && feature.strokewidth !='' ) {
		symbolizer['strokeWidth'] = feature.strokewidth;
	} else {
		symbolizer['strokeWidth'] = "2.5";
	}

	// set the unique style to the feature
	feature.style = symbolizer;

	// redraw the feature with its new style
	feature.layer.drawFeature(feature);
}

function hide_map() {

	$('#divMap').slideUp(function(){
		$('#divMap').css({"height":"0px", "width": "0px"});
		map.updateSize();
		map.pan(0,1);
	});
}

function show_map() {
	$('#divMap').css({"height":"350px", "width": "900px"});
	$('#divMap').slideDown(function(){
		map.updateSize();
		map.pan(0,1);

	});
}

// Ajax Submission
function actionsAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		$("#action_id").attr("value", id);
		$("#action_switch_to").attr("value", action);
		$("#actionListing").submit();
	}
}

var update_field = function(k, v) {
	var select = $("select[name='action_"+k+"']");
	var input = $("input[name='action_"+k+"']");
	// Is the value an array?
	if( Object.prototype.toString.call( v ) === '[object Array]' ) {
		input = $("input[name='action_"+k+"[]']");
		var select = $("select[name='action_"+k+"[]']");
	}
	
	// If theres a matching select
	if (select.length > 0)
	{
		if( Object.prototype.toString.call( v ) === '[object Array]' ) {
			options = $('option', select);
			$.each(options, function(i, el) {
				if (jQuery.inArray($(el).val(), v) != -1) $(el).attr('selected', true);
			});
		}
		else
		{
			$('option[value='+v+']', select).attr('selected', true);
		}
	}
	// If its a text input
	else if (input.attr('type') == 'text')
	{
		input.val(v);
	}
	// For radios and checkboxes
	else if (input.attr('type') == 'radio' ||
		input.attr('type') == 'checkbox')
	{
		if( Object.prototype.toString.call( v ) === '[object Array]' ) {
			$.each(input, function(i, el) {
				if (jQuery.inArray($(el).val(), v) != -1) $(el).attr('checked', true);
			});
		}
		else
		{
			$.each(input, function(i, el) {
				if ($(el).val() == v) $(el).attr('checked', true);
			});
		}
	}
};

function actionEdit(id, trigger, qualifiers, response, responseVars)
{
	$('form#actionsMain').trigger('reset');
	// Reset date picker
	$.each($('#action_specific_days_calendar').dpGetSelected(), function(i, date) {
		$('#action_specific_days_calendar').dpSetSelected(new Date(date).asString(), false);
	});
	hide_advanced_options();
	hide_response_advanced_options();
	
	$("form#actionsMain input[name=id]").val(id);
	$("#action_trigger option[value='"+trigger+"']").attr('selected',true).trigger('change');
	$("#action_response option[value='"+response+"']").attr('selected',true).trigger('change');
	
	jQuery.each(qualifiers, update_field);
	jQuery.each(responseVars, update_field);
	
	// Special cases
	if (qualifiers.between_times == 1)
	{
		qualifiers.between_times_1 = qualifiers.between_times_1 / 60;
		var min_1 = qualifiers.between_times_1 % 60;
		var hr_1 = (qualifiers.between_times_1 - min_1) / 60;
		update_field("between_times_hour_1", hr_1);
		update_field("between_times_minute_1", min_1);

		qualifiers.between_times_2 = qualifiers.between_times_2 / 60;
		var min_2 = qualifiers.between_times_2 % 60;
		var hr_2 = (qualifiers.between_times_2 - min_2) / 60;
		update_field("between_times_hour_2", hr_2);
		update_field("between_times_minute_2", min_2);
	}
	
	// Set selected days in date picker
	if (qualifiers.specific_days != undefined)
	{
		$.each(qualifiers.specific_days, function(i, date) {
			$('#action_specific_days_calendar').dpSetSelected(new Date(date).asString());
		});
	}
	
	// Load geometries
	vlayer.removeAllFeatures();
	if (qualifiers.geometry != undefined)
	{
		wkt = new OpenLayers.Format.WKT();
		$.each(qualifiers.geometry, function (i, geom) {
			wktFeature = wkt.read(geom.geometry);
			wktFeature.geometry.transform(proj_4326,proj_900913);
			vlayer.addFeatures(wktFeature);
		});
	}
	// Trigger change event
	$('.action_location').change();
}
