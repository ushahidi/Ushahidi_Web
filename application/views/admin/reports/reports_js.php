/**
 * Main reports js file.
 * 
 * Handles javascript stuff related to reports function.
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

<?php require SYSPATH.'../application/views/admin/utils_js.php' ?>

		// Ajax Submission
		function reportAction ( action, confirmAction, incident_id )
		{
			var statusMessage;
			if( !isChecked( "incident" ) && incident_id=='' )
			{ 
				alert('Please select at least one report.');
			} else {
				var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
				if (answer){
					
					// Set Submit Type
					$("#action").attr("value", action);

					var $incident_single = $(document.getElementById("incident_single"))
					var $mainform = $(document.getElementById("reportMain"));
					
					if (incident_id != '') 
					{
						// Submit Form For Single Item
						$incident_single.attr("value", incident_id);

						//prevent checked reports from receiving actoin if action is triggered from a single report
						$mainform.find("input:checkbox").attr("checked", false);
					}
					else
					{
						// Set Hidden form item to 000 so that it doesn't return server side error for blank value
						$incident_single.attr("value", "000");
					}

					$mainform.submit();
				}
			}

			return false;						
		}
		
		function showLog(id)
		{
			$('#' + id).toggle(400);
		}

$(function () {
	// Handle sort/order fields
	$("select#order").change(function() { $('.sort-form').submit(); });
	$(".sort-ASC").click(function() {
		$('.sort-field').val('DESC');
		$('.sort-form').submit();
		return false;
	});
	$(".sort-DESC").click(function() {
		$('.sort-field').val('ASC');
		$('.sort-form').submit();
		return false;
	});

	// Handle search tab
	$(".tabset .search").click(function() {
		if ($('.search-tab').hasClass('active'))
		{
			$(".search-tab").removeClass('active').slideUp(300, function() { $(".action-tab").slideDown().addClass('active'); });
			$(".tabset .search").removeClass('active');
		}
		else
		{
			$(".action-tab").removeClass('active').slideUp(300, function() { 
				$(".search-tab").slideDown().addClass('active');
			
				// Check if the map has already been created
				if (mapLoaded == false)
				{
					initMap();
				}
			});
			$(".tabset .search").addClass('active');
		}
		
		return false;
	});
	
	// Category treeview
	$(".category-column").treeview({
	  persist: "location",
	  collapsed: true,
	  unique: false
	});
});
	
	
// Map reference
var map = null;
var latitude = <?php echo isset($_GET['start_loc'][0]) ? floatval($_GET['start_loc'][0]) : Kohana::config('settings.default_lat') ?>;
var longitude = <?php echo isset($_GET['start_loc'][1]) ? floatval($_GET['start_loc'][1]) : Kohana::config('settings.default_lon'); ?>;
var zoom = 8;

var mapLoaded = false;

var initMap = function(){
		// OpenLayers uses IE's VML for vector graphics
		// We need to wait for IE's engine to finish loading all namespaces (document.namespaces) for VML.
		// jQuery.ready is executing too soon for IE to complete it's loading process.
		
		<?php echo map::layers_js(FALSE); ?>
		var mapConfig = {

			// Map center
			center: {
				latitude: latitude,
				longitude: longitude
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
			$(".search_lat").val(coords.latitude);
			$(".search_lon").val(coords.longitude);
		});
		
		// Alerts Slider
		$("select#alert_radius").change(
			function(e, ui) {
				var newRadius = $("#alert_radius").val();
				
				// Convert to Meters
				radius = newRadius * 1000;	
				
				// Redraw Circle
				map.updateRadius({radius: radius});
			}
		);
		
		mapLoaded = true;
};

$(function () {
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

