<?php
/**
 * Reporter js file.
 *
 * Handles javascript stuff related to reporter function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Reporters Javascript
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
// Reporter JS
<?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>
$().ready(function() {
	<?php
	if ($form_error)
	{
		?>
		$('#add_edit_form').show();
		showMap();
		<?php
	}?>
});

function fillFields(id, level_id, service_name, service_account, location_id, location_name, latitude, longitude)
{
	show_addedit();
	$('#add_edit_form').show();
	$("#reporter_id").attr("value", unescape(id));
	$("#level_id").attr("value", unescape(level_id));
	$("#service_name").attr("value", unescape(service_name));
	$("#reporter_service").text(unescape(service_name));
	$("#service_account").attr("value", unescape(service_account));
	$("#reporter_account").text(unescape(service_account));
	$("#location_id").attr("value", unescape(location_id));
	$("#location_name").attr("value", unescape(location_name));
	$("#latitude").attr("value", unescape(latitude));
	$("#longitude").attr("value", unescape(longitude));
	showMap();
}

// Ajax Submission
function reporterAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Reporter ID
		$("#rptr_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#rptrListing").submit();
	}
}

function submitSearch()
{
	$("#searchReporters").submit();
}

function reportersAction ( action, confirmAction, reporter_id, level_id )
{
	var statusMessage;
	if( !isChecked( "reporter" ) && reporter_id=='' )
	{ 
		alert('Please select at least one reporter.');
	} else {
		var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
		if (answer){
			
			// Set Submit Type
			$("#reporter_action").attr("value", action);
			
			// Set Level ID
			$("#level_id_main").attr("value", level_id);
			
			if (reporter_id != '') 
			{
				// Submit Form For Single Item
				$("#reporter_single").attr("value", reporter_id);
				$("#reporterMain").submit();
			}
			else
			{
				// Set Hidden form item to 000 so that it doesn't return server side error for blank value
				$("#reporter_single").attr("value", "000");
				
				// Submit Form For Multiple Items
				$("#reporterMain").submit();
			}
		
		} else {
			return false;
		}
	}
}

var map;
var thisLayer;
var proj_4326 = new OpenLayers.Projection('EPSG:4326');
var proj_900913 = new OpenLayers.Projection('EPSG:900913');
var markers;

function showMap()
{
	$("#ReporterMap").html('');
	
	if (markers) {
		markers.destroy();
		markers = null;
	}
	
	// Now initialise the map
	var options = {
	units: "m"
	, numZoomLevels: 18
	, controls:[],
	projection: proj_900913,
	'displayProjection': proj_4326
	};
	
	map = new OpenLayers.Map('ReporterMap', options);
	
	<?php echo map::layers_js(FALSE); ?>
	map.addLayers(<?php echo map::layers_array(FALSE); ?>);
	
	map.addControl(new OpenLayers.Control.Navigation());
	map.addControl(new OpenLayers.Control.PanZoom());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.LayerSwitcher());
	
	// Create the markers layer
	markers = new OpenLayers.Layer.Markers("Markers");
	map.addLayer(markers);
	
	// create a lat/lon object
	var latitude, longitude;
	if ($("#latitude").val() != "" && $("#longitude").val() != "") {
		latitude = $("#latitude").val();
		longitude = $("#longitude").val();
	} else {
		latitude = "<?php echo $latitude; ?>";
		longitude = "<?php echo $longitude; ?>";
	}
	var myPoint = new OpenLayers.LonLat(longitude, latitude);
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
}