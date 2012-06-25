<?php require APPPATH.'views/admin/utils_js.php' ?>
var map;

function showAlert(id, lon, lat, radius) {
	if (id) {
		if ($('#' + id).css('display') == 'none') {
			$('#' + id).show(400);
			showMap(id, lon, lat, radius);
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

function showMap(id, lon, lat, radius) {
	<?php echo map::layers_js(FALSE); ?>
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

	// Add the radius layer
	map.addRadiusLayer({latitude: lat, longitude: lon});
}

function alertsAction (action, confirmAction, alert_id) {
	var statusMessage;
	if( !isChecked( "alert" ) && alert_id=='' )
	{ 
		alert('Please select at least one alert.');
	} else {
		var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
		if (answer){

			// Set Submit Type
			$("#action").attr("value", action);

			if (alert_id != '') 
			{
				// Submit Form For Single Item
				$("#alert_single").attr("value", alert_id);
				$("#alertsMain").submit();
			}
			else
			{
				// Set Hidden form item to 000 so that it doesn't return server side error for blank value
				$("#alert_single").attr("value", "000");

				// Submit Form For Multiple Items
				$("#alertsMain").submit();
			}

		} else {
		//	return false;
		}
	}
}