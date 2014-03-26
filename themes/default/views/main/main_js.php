<?php
/**
 * Main cluster js file.
 * 
 * Server Side Map Clustering
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
		
// Initialize the Ushahidi namespace
Ushahidi.baseURL = "<?php echo url::site(); ?>";
Ushahidi.markerRadius = <?php echo $marker_radius; ?>;
Ushahidi.markerOpacity = <?php echo $marker_opacity; ?>;
Ushahidi.markerStokeWidth = <?php echo $marker_stroke_width; ?>;
Ushahidi.markerStrokeOpacity = <?php echo $marker_stroke_opacity; ?>;

// Default to most active month
var startTime = <?php echo $active_startDate ?>;

// Default to most active month
var endTime = <?php echo $active_endDate ?>;

var intervalTime = ''; // HT: manual time interval

// To hold the Ushahidi.Map reference
var map = null;


/**
 * Toggle Layer Switchers
 */
function toggleLayer(link, layer) {
	if ($("#"+link).text() == "<?php echo Kohana::lang('ui_main.show'); ?>")
	{
		$("#"+link).text("<?php echo Kohana::lang('ui_main.hide'); ?>");
	}
	else
	{
		$("#"+link).text("<?php echo Kohana::lang('ui_main.show'); ?>");
	}
	$('#'+layer).toggle(500);
}

/**
 * Create a function that calculates the smart columns
 */
function smartColumns() {
	//Reset column size to a 100% once view port has been adjusted
	$("ul.content-column").css({ 'width' : "100%"});

	//Get the width of row
	var colWrap = $("ul.content-column").width();

	// Find how many columns of 200px can fit per row / then round it down to a whole number
	var colNum = <?php echo $blocks_per_row; ?>;

	// Get the width of the row and divide it by the number of columns it 
	// can fit / then round it down to a whole number. This value will be
	// the exact width of the re-adjusted column
	var colFixed = Math.floor(colWrap / colNum);

	// Set exact width of row in pixels instead of using % - Prevents
	// cross-browser bugs that appear in certain view port resolutions.
	$("ul.content-column").css({ 'width' : colWrap});

	// Set exact width of the re-adjusted column	
	$("ul.content-column li").css({ 'width' : colFixed});
}

/**
 * Callback function for rendering the timeline
 */
function refreshTimeline(options) {

	<?php if (Kohana::config('settings.enable_timeline')) {?>

	// Use report filters if no options passed
	options = options || map.getReportFilters();
	// Copy options object to avoid accidental modifications to reportFilters
	options = jQuery.extend({}, options);

	var url = "<?php echo url::site().'json/timeline/'; ?>";

	if(options.i == undefined || options.i == '') { // HT: Added condition only to auto interval if empty interval type choosed
		var interval = (options.e - options.s) / (3600 * 24);
		if (interval <= 3) {
			options.i = "hour";
		} else if (interval <= (31 * 6)) {
			options.i = "day";
		} else {
			options.i = "month";
		}
	}
	// HT: More info link
	var urlLink = "<?php echo url::site().'reports/index/?'?>"+$.param(options);
	$('#timelineMoreLink').attr('href', urlLink);

	// Get the graph data
	$.ajax({
		url: url,
		data: options,
		success: function(response) {
			// Clear out the any existing plots
			$("#graph").html('');

			if (response != null && response[0].data.length < 2)
				return;

			var graphData = [];
			var raw = response[0].data;
			for (var i=0; i<raw.length; i++) {
				var date = new Date(raw[i][0]);

				var dateStr = date.getFullYear() + "-";
				dateStr += ('0' + (date.getMonth()+1)).slice(-2) + '-';
				dateStr += ('0' + date.getDate()).slice(-2);

				graphData.push([dateStr, parseInt(raw[i][1])]);
			}
			var timeline = $.jqplot('graph', [graphData], {
				seriesDefaults: {
					<?php if (Kohana::config('settings.timeline_graph') == 'bar') { ?>
					renderer: $.jqplot.BarRenderer, // HT: For bargraph
					rendererOptions: { // HT: For bargraph
						varyBarColor: true,
						barWidth: 1,
						shadowAlpha: 0
					},
					<?php } ?>
					color: response[0].color,
					lineWidth: 1.6,
					markerOptions: {
						<?php if (Kohana::config('settings.timeline_point_label')) { ?>
							show: true, // HT: To show the points
							//style: 'circle' // HT: Circle point
						<?php } else { ?>
							show: false,
						<?php } ?>
					},
					<?php if (Kohana::config('settings.timeline_point_label')) { ?>
						pointLabels: { // HT: To show point label
							show: true,
							edgeTolerance: -10,
							ypadding: 3
						}
					<?php } ?>
				},
				axesDefaults: {
					pad: 1.23,
				},
				axes: {
					xaxis: {
						renderer: $.jqplot.DateAxisRenderer,
						tickOptions: {
							formatString: '%#d&nbsp;%b\n%Y'
						}
					},
					yaxis: {
						min: 0,
						tickOptions: {
							formatString: '%.0f'
						}
					}
				},
				<?php if (Kohana::config('settings.timeline_point_label')) { ?>
					cursor: {show: true}, // HT: To show current point detail
				<?php } else { ?>
					cursor: {show: false},
				<?php } ?>
			});
		},
		dataType: "json"
	});
	<?php }?>
}


jQuery(function() {
	var reportsURL = "<?php echo Kohana::config('settings.allow_clustering') == 1 ? "json/cluster" : "json"; ?>";

	// Render thee JavaScript for the base layers so that
	// they are accessible by Ushahidi.js
	<?php echo map::layers_js(FALSE); ?>
	
	// Map configuration
	var config = {

		// Zoom level at which to display the map
		zoom: <?php echo Kohana::config('settings.default_zoom'); ?>,

		// Redraw the layers when the zoom level changes
		redrawOnZoom: <?php echo Kohana::config('settings.allow_clustering') == 1 ? "true" : "false"; ?>,

		// Center of the map
		center: {
			latitude: <?php echo Kohana::config('settings.default_lat'); ?>,
			longitude: <?php echo Kohana::config('settings.default_lon'); ?>
		},

		// Map controls
		mapControls: [
			new OpenLayers.Control.Navigation({ dragPanOptions: { enableKinetic: true } }),
			new OpenLayers.Control.Attribution(),
			new OpenLayers.Control.Zoom(),
			new OpenLayers.Control.MousePosition({
				div: document.getElementById('mapMousePosition'),
				formatOutput: Ushahidi.convertLongLat
			}),
			new OpenLayers.Control.Scale('mapScale'),
			new OpenLayers.Control.ScaleLine(),
			new OpenLayers.Control.LayerSwitcher()
		],

		// Base layers
		baseLayers: <?php echo map::layers_array(FALSE); ?>,

		// Display the map projection
		showProjection: true,
		
		reportFilters: {
			s: startTime,
			e: endTime
		}

	};

	// Initialize the map
	map = new Ushahidi.Map('map', config);
	map.addLayer(Ushahidi.GEOJSON, {
		name: "<?php echo Kohana::lang('ui_main.reports'); ?>",
		url: reportsURL,
		transform: false
	}, true, true);


	// Register the referesh timeline function as a callback
	map.register("filterschanged", refreshTimeline);
	setTimeout(function() { refreshTimeline({
		s: startTime,
		e: endTime
	}); }, 800);


	// Category Switch Action
	$("ul#category_switch li > a").click(function(e) {
		
		var categoryId = this.id.substring(4);
		var catSet = 'cat_' + this.id.substring(4);

		// Remove All active
		$("a[id^='cat_']").removeClass("active");
		
		// Hide All Children DIV
		$("[id^='child_']").hide();

		// Add Highlight
		$("#cat_" + categoryId).addClass("active"); 

		// Show children DIV
		$("#child_" + categoryId).show();
		$(this).parents("div").show();
		
		// Update report filters
		map.updateReportFilters({c: categoryId});

		e.stopPropagation();
		return false;
	});

	// Layer selection
	$("ul#kml_switch li > a").click(function(e) {
		// Get the layer id
		var layerId = this.id.substring(6);

		var isCurrentLayer = false;
		var context = this;

		// Remove all actively selected layers
		$("#kml_switch a").each(function(i) {
			if ($(this).hasClass("active")) {
				if (this.id == context.id) {
					isCurrentLayer = true;
				}
				map.trigger("deletelayer", $(".layer-name", this).html());
				$(this).removeClass("active");
			}
		});

		// Was a different layer selected?
		if (!isCurrentLayer) {
			// Set the currently selected layer as the active one
			$(this).addClass("active");
			map.addLayer(Ushahidi.KML, {
				name: $(".layer-name", this).html(),
				url: "json/layer/" + layerId
			});
		}

		return false;
	});
		
	// Timeslider and date change actions
	$("select#startDate, select#endDate").selectToUISlider({
		labels: 4,
		labelSrc: 'text',
		sliderOptions: {
			change: function(e, ui) {
				var from = $("#startDate").val();
				var to = $("#endDate").val();
				var intrvl = $("#intervalDate").val(); // HT: manual time interval

				if (to > from && (from != startTime || to != endTime || intrvl != intervalTime)) { // HT: manual time interval
				//if (to > from && (from != startTime || to != endTime)) {
					// Update the report filters
					startTime = from;
					endTime = to;
					intervalTime = intrvl; // HT: manual time interval
					map.updateReportFilters({s: from, e: to, i: intrvl}); // HT: manual time interval
					// map.updateReportFilters({s: from, e: to});
				}

				e.stopPropagation();
			}
		}
	});
	
	// HT: manual time interval trigger timeslider change on interval change
	$("select#intervalDate").change(function() {
		$("select#startDate").trigger('change');
	});
	
	// Media Filter Action
	$('.filters a').click(function() {
		var mediaType = parseFloat(this.id.replace('media_', '')) || 0;
		
		$('.filters a.active').removeClass('active');
		$(this).addClass('active');

		// Update the report filters
		map.updateReportFilters({m: mediaType});
		
		return false;
	});
	
	//Execute the function when page loads
	smartColumns();

});

$(window).resize(function () { 
	//Each time the viewport is adjusted/resized, execute the function
	smartColumns();
});