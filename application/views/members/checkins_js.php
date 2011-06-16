<?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>
var map;
var proj_4326 = new OpenLayers.Projection('EPSG:4326');
var proj_900913 = new OpenLayers.Projection('EPSG:900913');
var latitude;
var longitude;
var markers;
var linestring;

function showCheckin(id, lon, lat) {
	if (id) {
		if ($('#' + id).css('display') == 'none') {
			$('#' + id).show(400);
			showMap(id, lon, lat);
		}
		else
		{
			$('#' + id).hide(400);
			if (map)
			{
				map.destroy();
				$('#' + id + '_map').html();
			}
		}
	}
}

function showMap(id, lon, lat) {
	/*
	- Initialize Map
	- Uses Spherical Mercator Projection
	- Units in Metres instead of Degrees					
	*/
	var options = {
		units: "m",
		numZoomLevels: 16,
		controls:[],
		projection: proj_900913,
		'displayProjection': proj_4326
	};
		
	map = new OpenLayers.Map(id + '_map', options);

	<?php echo map::layers_js(FALSE); ?>
	map.addLayers(<?php echo map::layers_array(FALSE); ?>);
	
	map.addControl(new OpenLayers.Control.Navigation());
	map.addControl(new OpenLayers.Control.PanZoomBar());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.LayerSwitcher());
	
	
	style = new OpenLayers.Style({
		fillColor: "#<?php echo Kohana::config('settings.default_map_all'); ?>",
		fillOpacity: 0.8,
		strokeColor: "white",
		strokeOpacity: 1,
		pointRadius: "8"
	});
	
	var vectorLayer = new OpenLayers.Layer.Vector("Checkin", {
		styleMap: new OpenLayers.StyleMap({
			"default":style,
			"select": style
		})
	});
	
	var lonlat = new OpenLayers.LonLat(lon, lat);
	lonlat.transform(proj_4326, proj_900913);
	map.setCenter(lonlat, 14);
	
	point = new OpenLayers.Geometry.Point(lon, lat);
	point.transform(proj_4326, proj_900913);
	var origFeature = new OpenLayers.Feature.Vector(point);
	vectorLayer.addFeatures(origFeature);
	map.addLayer(vectorLayer);
}

function checkinAction( action, confirmAction, checkin_id )
{
	var statusMessage;
	if( !isChecked( "checkin" ) && checkin_id=='' )
	{ 
		alert('Please select at least one checkin.');
	} else {
		var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
		if (answer){

			// Set Submit Type
			$("#action").attr("value", action);

			if (checkin_id != '') 
			{
				// Submit Form For Single Item
				$("#checkin_single").attr("value", checkin_id);
				$("#checkinMain").submit();
			}
			else
			{
				// Set Hidden form item to 000 so that it doesn't return server side error for blank value
				$("#checkin_single").attr("value", "000");

				// Submit Form For Multiple Items
				$("#checkinMain").submit();
			}

		} else {
		//	return false;
		}
	}
}