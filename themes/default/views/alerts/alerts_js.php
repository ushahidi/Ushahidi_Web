<?php
/**
 * Alerts js file.
 *
 * Handles javascript stuff related  to alerts function
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

// Map reference
var map = null;
var latitude = <?php echo Kohana::config('settings.default_lat') ?>;
var longitude = <?php echo Kohana::config('settings.default_lon'); ?>;
var zoom = <?php echo Kohana::config('settings.default_zoom'); ?>;

jQuery(function($) {
	$(window).load(function(){
		
		// OpenLayers uses IE's VML for vector graphics
		// We need to wait for IE's engine to finish loading all namespaces (document.namespaces) for VML.
		// jQuery.ready is executing too soon for IE to complete it's loading process.
		
		<?php echo map::layers_js(FALSE); ?>
		var mapConfig = {

			// Map center
			center: {
				latitude: latitude,
				longitude: longitude,
			},

			// Zoom level
			zoom: zoom,

			// Base layers
			baseLayers: <?php echo map::layers_array(FALSE); ?>
		};

		map = new Ushahidi.Map('divMap', mapConfig);
		map.addRadiusLayer({
			latitude: latitude,
			longitude: longitude
		});

		// Subscribe to makerpositionchanged event
		map.register("markerpositionchanged", function(coords){
			$("#alert_lat").val(coords.latitude);
			$("#alert_lon").val(coords.longitude);
		});

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

		// Alerts Slider
		$("select#alert_radius").selectToUISlider({
			labels: 6,
			labelSrc: 'text',
			sliderOptions: {
				change: function(e, ui) {
					var newRadius = $("#alert_radius").val();
					
					// Convert to Meters
					radius = newRadius * 1000;	
					
					// Redraw Circle
					map.updateRadius({radius: radius});
				}
			}
		}).hide();
	
	
	// Some Default Values		
	$("#alert_mobile").focus(function() {
		$("#alert_mobile_yes").attr("checked",true);
	}).blur(function() {
		if(!this.value.length) {
			$("#alert_mobile_yes").attr("checked",false);
		}
	});
	
	$("#alert_email").focus(function() {
		$("#alert_email_yes").attr("checked",true);
	}).blur(function() {
		if( !this.value.length ) {
			$("#alert_email_yes").attr("checked",false);
		}
	});

	// Category treeview
    $(".category-column").treeview({
      persist: "location",
	  collapsed: true,
	  unique: false
	  });
	});
});

/**
 * Google GeoCoder
 */
function geoCode() {
	$('#find_loading').html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
	address = $("#location_find").val();
	$.post("<?php echo url::site(); ?>reports/geocode/", { address: address },
		function(data){
			if (data.status == 'success') {

				map.updateRadius({
					longitude: data.longitude,
					latitude: data.latitude
				});
			
				// Update form values
				$("#alert_lat").val(data.latitude);
				$("#alert_lon").val(data.longitude);
			} else {
				// Alert message to be displayed
				var alertMessage = address + " not found!\n\n***************************\n" + 
				    "Enter more details like city, town, country\nor find a city or town " +
				    "close by and zoom in\nto find your precise location";

				alert(alertMessage)
			}
			$('#find_loading').html('');
		}, "json");
	return false;
}
