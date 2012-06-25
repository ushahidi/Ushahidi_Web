<?php require APPPATH.'views/admin/utils_js.php' ?>
var map;

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
	<?php echo map::layers_js(FALSE); ?>

	// Map configuration
	var mapConfig = {
		// Map center
		center: {
			latitude: lat,
			longitude: lon,
		},

		// Zoom level
		zoom: <?php echo Kohana::config('settings.default_zoom'); ?>,

		// Base layers
		baseLayers: <?php echo map::layers_array(FALSE); ?>
	};

	// Initialize the map
	map = new Ushahidi.Map(id + '_map', mapConfig);
	
	// Style for the checkin
	var style = new OpenLayers.Style({
		fillColor: "#<?php echo Kohana::config('settings.default_map_all'); ?>",
		fillOpacity: 0.8,
		strokeColor: "white",
		strokeOpacity: 1,
		pointRadius: "8"
	});

	// Style map for the checkins
	var styleMap = new OpenLayers.StyleMap({
		default: style,
		select: style
	});

	// Add the layer
	map.addLayer(Ushahidi.DEFAULT, {styleMap: styleMap, detectMapClicks: false});		
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