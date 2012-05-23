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
Ushahidi.baseUrl = "<?php echo url::site(); ?>";
Ushahidi.markerRadius = <?php echo $marker_radius; ?>;
Ushahidi.markerOpacity = <?php echo $marker_opacity; ?>;
Ushahidi.markerStokeWidth = <?php echo $marker_stroke_width; ?>;
Ushahidi.markerStrokeOpacity = <?php echo $marker_stroke_opacity; ?>;

// Default to most active month
var startTime = <?php echo $active_startDate ?>;

// Default to most active month
var endTime = <?php echo $active_endDate ?>;


/**
 * Display info window for checkin data
 */
function showCheckinData(event) {
	selectedFeature = event.feature;
	zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
	lon = zoom_point.lon;
	lat = zoom_point.lat;
	
	var content = "<div class=\"infowindow\" style=\"color:#000000\"><div class=\"infowindow_list\">";
	
	if(event.feature.attributes.ci_media_medium !== "")
	{
		content += "<a href=\""+event.feature.attributes.ci_media_link+"\" rel=\"lightbox-group1\" title=\""+event.feature.attributes.ci_msg+"\">";
		content += "<img src=\""+event.feature.attributes.ci_media_medium+"\" /><br/>";
	}

	content += event.feature.attributes.ci_msg+"</div><div style=\"clear:both;\"></div>";
	content += "\n<div class=\"infowindow_meta\">";
	content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",1)'><?php echo Kohana::lang('ui_main.zoom_in');?></a>";
	content += "&nbsp;&nbsp;|&nbsp;&nbsp;";
	content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",-1)'><?php echo Kohana::lang('ui_main.zoom_out');?></a></div>";
	content += "</div>";

	if (content.search("<?php echo '<'; ?>script") != -1)
	{
		content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/<?php echo '<'; ?>/g, "&lt;");
	}
	
	popup = new OpenLayers.Popup.FramedCloud("chicken", 
			event.feature.geometry.getBounds().getCenterLonLat(),
			new OpenLayers.Size(100,100),
			content,
			null, true, onPopupClose);
			
	event.feature.popup = popup;
	map.addPopup(popup);
}

/**
 * Display Checkin Points
 * Note: This function totally ignores the timeline
 */
function showCheckins() {
	$(document).ready(function(){

		var ci_styles = new OpenLayers.StyleMap({
			"default": new OpenLayers.Style({
				pointRadius: "5", // sized according to type attribute
				fillColor: "${fillcolor}",
				strokeColor: "${strokecolor}",
				fillOpacity: "${fillopacity}",
				strokeOpacity: 0.75,
				strokeWidth: 1.5
			})
		});

		var checkinLayer = new OpenLayers.Layer.Vector('Checkins', {styleMap: ci_styles});
		map.addLayers([checkinLayer]);

		highlightCtrl = new OpenLayers.Control.SelectFeature(checkinLayer, {
		    hover: true,
		    highlightOnly: true,
		    renderIntent: "temporary"
		});
		map.addControl(highlightCtrl);
		highlightCtrl.activate();
		
		selectControl = new OpenLayers.Control.SelectFeature([checkinLayer,markers]);
		map.addControl(selectControl);
		selectControl.activate();
		checkinLayer.events.on({
			"featureselected": showCheckinData,
			"featureunselected": onFeatureUnselect
		});

		$.getJSON("<?php echo url::site()."api/?task=checkin&action=get_ci&mapdata=1&sqllimit=1000&orderby=checkin.checkin_date&sort=ASC"?>", function(data) {
			var user_colors = new Array();
			// Get colors
			$.each(data["payload"]["users"], function(i, payl) {
				user_colors[payl.id] = payl.color;
			});

			// Get checkins
			$.each(data["payload"]["checkins"], function(key, ci) {

				var cipoint = new OpenLayers.Geometry.Point(parseFloat(ci.lon), parseFloat(ci.lat));
				cipoint.transform(proj_4326, proj_900913);

				var media_link = '';
				var media_medium = '';
				var media_thumb = '';

				if(ci.media === undefined)
				{
					// No image
				}
				else
				{
					// Image!
					media_link = ci.media[0].link;
					media_medium = ci.media[0].medium;
					media_thumb = ci.media[0].thumb;
				}

				var checkinPoint = new OpenLayers.Feature.Vector(cipoint, {
					fillcolor: "#"+user_colors[ci.user],
					strokecolor: "#FFFFFF",
					fillopacity: ci.opacity,
					ci_id: ci.id,
					ci_msg: ci.msg,
					ci_media_link: media_link,
					ci_media_medium: media_medium,
					ci_media_thumb: media_thumb
				});

				checkinLayer.addFeatures([checkinPoint]);
			});
		});
	});			
}

/**
 * Add KML/KMZ Layers
 */
function switchLayer(layerID, layerURL, layerColor) {
	if ( $("#layer_" + layerID).hasClass("active") )
	{
		new_layer = map.getLayersByName("Layer_"+layerID);
		if (new_layer)
		{
			for (var i = 0; i <?php echo '<'; ?> new_layer.length; i++)
			{
				map.removeLayer(new_layer[i]);
			}
			
			// Part of #2168 fix
			// Added by E.Kala <emmanuel(at)ushahidi.com>
			// Remove the layer from the list of KML overlays - kmlOverlays
			if (kmlOverlays.length == 1)
			{
				kmlOverlays.pop();
			}
			else if (kmlOverlays.length > 1)
			{
				// Temporarily store the current list of overlays
				tempKmlOverlays = kmlOverlays;
				
				// Re-initialize the list of overlays
				kmlOverlays = [];
				
				// Search for the overlay that has just been removed from display
				for (var i = 0; i < tempKmlOverlays.length; i ++)
				{
					if (tempKmlOverlays[i].name != "Layer_"+layerID)
					{
						kmlOverlays.push(tempKmlOverlays[i]);
					}
				}
				// Unset the working list
				tempKmlOverlays = null;
			}
		}
		$("#layer_" + layerID).removeClass("active");

	}
	else
	{
		$("#layer_" + layerID).addClass("active");

		// Get Current Zoom
		currZoom = map.getZoom();

		// Get Current Center
		currCenter = map.getCenter();
		
		// Add New Layer
		addMarkers('', '', '', currZoom, currCenter, '', layerID, 'layers', layerURL, layerColor);
	}
}

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
function refreshTimeline() {

	var options = (arguments.length == 0) ? {} : arguments[0];

	// Compute the start and end dates
	startTime = (options.s == undefined)
	    ? new Date(startTime * 1000)
	    : new Date(options.s * 1000);

	endTime = (options.e == undefined)
	    ? new Date(endTime * 1000)
	    : new Date(options.e * 1000);

	var url = "<?php echo url::site().'json/timeline/'; ?>";
	url += (options.c !== undefined && parseInt(options.c) > 0) ?  options.c : '';

	var parameters = {};
	var divisor = 1000 * 3600 * 24;
	if ((endTime - startTime) / divisor <= 3){
		parameters.i = "hour";
	} else if ((endTime - startTime) / divisor >= 124) {
		parameters.i = "day";
	}

	// Get the graph data
	$.ajax({
		url: url,
		data: parameters,
		async: false,
		success: function(response) {
			// Clear out the any existing plots
			$("#graph").html('');

			if (response[0].data.length == 0)
				return;

			var graphData = [];
			var raw = response[0].data;
			for (var i=0; i<raw.length; i++) {
				var date = new Date(raw[i][0]);

				var dateStr = date.getFullYear() + "-";
				dateStr += (date.getMonth() < 10) ? "0" : "";
				dateStr += (date.getMonth() +1) + "-" ;
				dateStr += (date.getDate() < 10) ? "0" : "";
				dateStr += date.getDate();

				graphData.push([dateStr, parseInt(raw[i][1])]);
			}
			var timeline = $.jqplot('graph', [graphData], {
				seriesDefaults: {
					color: response[0].color,
					lineWidth: 1.6,
					markerOptions: {
						show: false
					}
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
				cursor: {show: false}
			});
		},
		dataType: "json"
	});
}


jQuery(function() {
	// Render thee JavaScript for the base layers so that
	// they are accessible by Ushahidi.js

	<?php echo map::layers_js(FALSE); ?>
	
	// Map configuration
	var config = {

		// Zoom level at which to display the map
		zoom: <?php echo $default_zoom; ?>,

		// Redraw the layers when the zoom level changes
		redrawOnZoom: true,

		// Center of the map
		center: {
			latitude: <?php echo $latitude; ?>,
			longitude: <?php echo $longitude; ?>
		},

		// Map controls
		mapControls: [
			new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}),
			new OpenLayers.Control.Navigation(),
			new OpenLayers.Control.Attribution(),
			new OpenLayers.Control.PanZoomBar(),
			new OpenLayers.Control.MousePosition({
				div: document.getElementById('mapMousePosition'),
				numdigits: 5
			}),
			new OpenLayers.Control.Scale('mapScale'),
			new OpenLayers.Control.ScaleLine(),
			new OpenLayers.Control.LayerSwitcher()
		],

		// Base layers
		baseLayers: <?php echo map::layers_array(FALSE); ?>,

		// Display the map projection
		showProjection: true

	};

	// Initialize the map
	var map = new Ushahidi.Map('map', config);
	map.addLayer(Ushahidi.REPORTS, {
		name: "<?php echo Kohana::lang('ui_main.reports'); ?>",
		url: "<?php echo $json_url; ?>"
	}, true);


	// Register the referesh timeline function as a callback
	map.register("filterschanged", refreshTimeline);
	setTimeout(function() { refreshTimeline(); }, 1500);


	// Category Switch Action
	$("ul.category-filters li > a").click(function(e) {
		
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

		// Check if the timeline is enabled
										
		e.stopPropagation();
		return false;
	});
	
	// Sharing Layer[s] Switch Action
	$("a[id^='share_']").click(function()
	{
		var shareID = this.id.substring(6);
		
		if ($("#share_" + shareID).hasClass("active")) {
			
		} else {
			$("#share_" + shareID).addClass("active");
		}
	});
	
	// Timeslider and date change actions
	$("select#startDate, select#endDate").selectToUISlider({
		labels: 4,
		labelSrc: 'text',
		sliderOptions: {
			change: function(e, ui) {
				var startDate = $("#startDate").val();
				var endDate = $("#endDate").val();

				// The end date must always be greater
				if (endDate > startDate) {
					// Update the report filters
					map.updateReportFilters({s: startDate, e: endDate});
				}

				return false;
			}
		}
	});
	
	// Media Filter Action
	$('.filters li a').click(function() {
		var mediaType = parseFloat(this.id.replace('media_', '')) || 0;
		
		$('.filters li a').attr('class', '');
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


// START CHECKINS!
<?php if (Kohana::config('settings.checkins')): ?>

function cilisting(sqllimit,sqloffset) {
	jsonurl = "<?php echo url::site(); ?>api/?task=checkin&action=get_ci&sqllimit="+sqllimit+"&sqloffset="+sqloffset+"&orderby=checkin.checkin_date&sort=DESC";
	
	var showncount = 0;
	$.getJSON(jsonurl, function(data) {
		
		if(data.payload.checkins == undefined)
		{
			if(sqloffset != 0)
			{
				var newoffset = sqloffset - sqllimit;
				$('div#cilist').html("<div style=\"text-align:center;\"><?php echo Kohana::lang('ui_main.no_checkins'); ?><br/><br/><a href=\"javascript:cilisting("+sqllimit+","+newoffset+");\">&lt;&lt; <?php echo Kohana::lang('ui_main.previous'); ?></a></div>");
			}else{
				$('div#cilist').html("<div style=\"text-align:center;\">No checkins to display.</div>");
			}

			return;
		}
		
		$('div#cilist').html("");
		
		var user_colors = new Array();
		// Get colors
		$.each(data.payload.users, function(i, payl) {
			user_colors[payl.id] = payl.color;
		});
		
		$.each(data.payload.checkins, function(i,item){
			
			if(i == 0)
			{
				$('div#cilist').append("<div class=\"ci_checkin\" class=\"ci_id_"+item.id+"\"style=\"border:none\"><a name=\"ci_id_"+item.id+"\" />");
			}else{
				$('div#cilist').append("<div class=\"ci_checkin\" class=\"ci_id_"+item.id+"\" style=\"padding-bottom:5px;margin-bottom:5px;\"><a name=\"ci_id_"+item.id+"\" />");
			}
			
			if(item.media === undefined)
			{
				// Tint the color a bit
				$('div#cilist').append("<div class=\"ci_colorblock ci_shorterblock\" style=\"background-color:#"+user_colors[item.user]+";\"><div class=\"ci_colorfade\"></div></div>");
			}else{
				// Show image
				$('div#cilist').append("<div class=\"ci_colorblock ci_tallerblock\" style=\"background-color:#"+user_colors[item.user]+";\"><div class=\"ci_imgblock\"><a href=\""+item.media[0].link+"\" rel=\"lightbox-group1\" title=\""+item.msg+"\"><img src=\""+item.media[0].thumb+"\" height=\"59\" /></a></div></div>");
			}
			
			$('div#cilist').append("<div style=\"float:right;width:24px;height:24px;margin-right:10px;\"><a class=\"ci_moredetails\" reportid=\""+item.id+"\" href=\"javascript:externalZeroIn("+item.lon+","+item.lat+",16,"+item.id+");\"><img src=\"<?php echo url::file_loc('img'); ?>media/img/pin_trans.png\" width=\"24\" height=\"24\" /></a></div>");
			
			$.each(data.payload.users, function(j,useritem){
				if(useritem.id == item.user){
					$('div#cilist').append("<div style=\"font-size:14px;width:215px;padding-top:0px;\"><a href=\"<?php echo url::site(); ?>profile/user/"+useritem.username+"\">"+useritem.name+"</a></div>");
				}
			});
			
			var utcDate = item.date.replace(" ","T")+"Z";
			
			if(item.msg == "")
			{
				$('div#cilist').append("<div class=\"ci_cimsg\"><small><em>"+$.timeago(utcDate)+"</em></small></div>");
			}else{
				$('div#cilist').append("<div class=\"ci_cimsg\">"+item.msg+"<br/><small><em>"+$.timeago(utcDate)+"</em></small></div>");
			}
			
			if(item.comments !== undefined)
			{
				var user_link = '';
				var comment_utcDate = '';
				$.each(item.comments, function(j,comment){
					comment_utcDate = comment.date.replace(" ","T")+"Z";
					if(item.user_id != 0){
						user_link = '<a href=\"<?php echo url::site(); ?>profile/user/'+comment.username+'\">'+comment.author+'</a>';
					}else{
						user_link = ''+comment.author+'';
					}
					$('div#cilist').append("<div style=\"clear:both\"></div>"+user_link+": "+comment.description+" <small>(<em>"+$.timeago(comment_utcDate)+"</em>)</small></div>");
				});
			}
			
			$('div#cilist').append("<div style=\"clear:both\"></div></div>");

			showncount = showncount + 1;
		});
		
		// Show previous link
		if(sqloffset == 0)
		{
			$('div#cilist').append("<div style=\"float:left;\">&lt;&lt; <?php echo Kohana::lang('ui_main.previous'); ?></div>");
		}else{
			var newoffset = sqllimit - sqloffset;
			$('div#cilist').append("<div style=\"float:left;\"><a href=\"javascript:cilisting("+sqllimit+","+newoffset+");\">&lt;&lt; <?php echo Kohana::lang('ui_main.previous'); ?></a></div>");
		}
		
		// Show next link
		if(showncount != sqllimit)
		{
			$('div#cilist').append("<div style=\"float:right;\"><?php echo Kohana::lang('ui_main.next'); ?> &gt;&gt;</div>");
		}else{
			var newoffset = sqloffset + sqllimit;
			$('div#cilist').append("<div style=\"float:right;\"><a href=\"javascript:cilisting("+sqllimit+","+newoffset+");\"><?php echo Kohana::lang('ui_main.next'); ?> &gt;&gt;</a></div>");
		}
		
		$('div#cilist').append("<div style=\"clear:both\"></div>");

	});
	
}

cilisting(3,0);
showCheckins();

<?php endif; ?>
