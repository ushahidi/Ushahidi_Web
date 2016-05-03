var map;
var new_markers;
var radius = 20000;
var proj_4326 = new OpenLayers.Projection('EPSG:4326');
var proj_900913 = new OpenLayers.Projection('EPSG:900913');

$(document).ready(function() {
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
		
	map = new OpenLayers.Map('analysis-map', options);
	
	/*
	- Select A Mapping API
	- Live/Yahoo/OSM/Google
	- Set Bounds					
	*/
	<?php echo map::layers_js(FALSE); ?>
	map.addLayers(<?php echo map::layers_array(FALSE); ?>);
	
	map.addControl(new OpenLayers.Control.Navigation());
	map.addControl(new OpenLayers.Control.PanZoom());
	map.addControl(new OpenLayers.Control.Attribution());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.LayerSwitcher());
	
	// Create the Circle/Radius layer
	var radiusLayer = new OpenLayers.Layer.Vector("Radius Layer");
	
	
	// Create the markers layer
	var markers = new OpenLayers.Layer.Markers("Markers");
	new_markers = new OpenLayers.Layer.Markers("New Markers");
	map.addLayers([radiusLayer, markers, new_markers]);
	
	// create a lat/lon object
	var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
	myPoint.transform(proj_4326, proj_900913);
	
	// create a marker positioned at a lon/lat
	var marker = new OpenLayers.Marker(myPoint);
	markers.addMarker(marker);
	
	// draw circle around point
	drawCircle(<?php echo $longitude; ?>,<?php echo $latitude; ?>,radius);
	
	// display the map centered on a latitude and longitude (Google zoom levels)
	map.setCenter(myPoint, 9);
	
	
	// Draw circle around point
	function drawCircle(lon,lat,radius)
	{
		radiusLayer.destroyFeatures();
		var circOrigin = new OpenLayers.Geometry.Point(lon,lat);
		circOrigin.transform(proj_4326, proj_900913);
		
		var circStyle = OpenLayers.Util.extend( {},OpenLayers.Feature.Vector.style["default"] );
		var circleFeature = new OpenLayers.Feature.Vector(
			OpenLayers.Geometry.Polygon.createRegularPolygon( circOrigin, radius, 40, 0 ),
			null,
			circStyle
		);
		radiusLayer.addFeatures( [circleFeature] );
	}
	
	// Detect Map Clicks
	map.events.register("click", map, function(e){
		var lonlat = map.getLonLatFromViewPortPx(e.xy);
		var lonlat2 = map.getLonLatFromViewPortPx(e.xy);
	    m = new OpenLayers.Marker(lonlat);
		markers.clearMarkers();
    	markers.addMarker(m);

		reset();

		currRadius = $("#analysis_radius").val();
		radius = currRadius * 1000
		
		lonlat2.transform(proj_900913,proj_4326);
		drawCircle(lonlat2.lon, lonlat2.lat, radius);
					
		// Update form values (jQuery)
		$("#latitude").attr("value", lonlat2.lat);
		$("#longitude").attr("value", lonlat2.lon);
	});	
	
	// Radius Slider
	$("select#analysis_radius").selectToUISlider({
		labels: 6,
		tooltip: false,
		labelSrc: 'text',
		sliderOptions: {
			change: function(e, ui) {
				var newRadius = $("#analysis_radius").val();
				
				// Convert to Meters
				radius = newRadius * 1000;	
				
				currLon = $("#longitude").val();
				currLat = $("#latitude").val();
				drawCircle(currLon,currLat,radius);
			}
		}
	}).hide();
	
	// Resizable Map
	$("#analysis-map").resizable({maxWidth: 418, minWidth: 418});
	
	// View Report Dialog
	$("#analysis-report").dialog({
		autoOpen: false,
		height: 550,
		width: 500,
		modal: true,
		resizable: true,
		draggable: true,
		zIndex: 3999
	});
});


function generateReports(){
	$('#analysis-generated').html('<div style="text-align:center;"><img src="<?php echo url::base() . "plugins/analysis/views/images/loading_g2.gif"; ?>"></div>');
	$.post("<?php echo url::site() . 'admin/analysis/find_reports' ?>", 
		{
			latitude: $("#latitude").attr("value"),
			longitude: $("#longitude").attr("value"),
			analysis_radius: $("#analysis_radius").attr("value"),
			start_date: $("#start_date").attr("value"),
			end_date: $("#end_date").attr("value"),
			analysis_category: $("#analysis_category").attr("value")
		},
		function(data){
			if (data.status == 'success'){
				$('#analysis-generated').html(data.message);
				$('#analysis-generated').effect("highlight", {}, 2000);
				$('#analysis-assess').html('<div class="tab"><ul><li><a href="javascript:assessForm();" class="analysis-btn-assess"  id="analysis-btn-assess">PERFORM ASSESSMENT&raquo;</a></li></ul></div>');
				var markers_array = data.markers;
				// Create a new markers layer
				
				new_markers.clearMarkers();
				
				for ( var i in markers_array){
					// create a lat/lon object
					var newPoint = new OpenLayers.LonLat(markers_array[i][0], markers_array[i][1]);
					newPoint.transform(proj_4326, proj_900913);

					// create a marker positioned at a lon/lat
					var newMarker = new OpenLayers.Marker(newPoint);
					new_markers.addMarker(newMarker);
				};
			} else {
				alert(data.message);
				$('#analysis-generated').html("<h4>CLICK \"SEARCH\" ON RIGHT TO FIND RELATED REPORTS</h4>");
			}
	  	}, "json");
}

function showReport(id) {
	$('#analysis-report').html('<div style="text-align:center;"><img src="<?php echo url::base() . "plugins/analysis/views/images/loading_g2.gif"; ?>"></div>');
	$.get('<?php echo url::site()."admin/analysis/get_report/"; ?>'+id, function(data) {
		// Generate Content
		$("#analysis-report").html(data);
	});
	
	// Open Dialog
	$("#analysis-report").dialog('open');
}

function assessForm()
{
	var a_id_checked = $('#analysis-generated').find('input:checkbox:checked').length;
	if(a_id_checked == 0){
		alert('Please Make A Selection');
	}
	else
	{
		$("#analysis-form").submit();
	}
}

function reset() {
	new_markers.clearMarkers();
	$('#analysis-generated').html("<h4>CLICK \"SEARCH\" ON RIGHT TO FIND RELATED REPORTS</h4>");
}

function hideAbout(){
	$(".analysis-about").hide();
}

function checkAll(name){
	$("input[name="+name+"]").attr('checked', true);
}

function checkNone(name){
	$("input[name="+name+"]").attr('checked', false);
}
