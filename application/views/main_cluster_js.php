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
		// Map JS
		var map;
		var currentCat;
		var thisLayer;
		var proj_4326 = new OpenLayers.Projection('EPSG:4326');
		var proj_900913 = new OpenLayers.Projection('EPSG:900913');
		var mapLoad = 0;
		var json_url = "json_cluster";
		
		jQuery(function() {
			var map_layer;
			markers = null;
			var catID = '';
			OpenLayers.Strategy.Fixed.prototype.preload=true;
			
			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			- Units in Metres instead of Degrees					
			*/
			
			var options = {
				units: "mi",
				numZoomLevels: 16,
				controls:[],
				projection: proj_900913,
				'displayProjection': proj_4326,
				eventListeners: {
						"zoomend": mapMove
				    }
				};
			map = new OpenLayers.Map('map', options);
			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
			
			/*
			- Select A Mapping API
			- Live/Yahoo/OSM/Google
			- Set Bounds					
			*/
			google_st = new OpenLayers.Layer.Google("Google Streets", {
				sphericalMercator: true,
				maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
				});
				
			google_sat = new OpenLayers.Layer.Google("Google Satellite", {
				type: G_SATELLITE_MAP,
				sphericalMercator: true,
				maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
				});
				
			osm_sat = new OpenLayers.Layer.OSM.Mapnik("Open Street Maps Satellite", {
				sphericalMercator: true,
				maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
				});
				
			map.addLayers([osm_sat, google_st, google_sat]);
			
			
			// Add Controls
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.MousePosition(
					{ div: 	document.getElementById('mapMousePosition'), numdigits: 5 
				}));    
			map.addControl(new OpenLayers.Control.Scale('mapScale'));
            map.addControl(new OpenLayers.Control.ScaleLine());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			// display the map projection
			document.getElementById('mapProjection').innerHTML = map.projection;
				
			gMap = map;
			
			// Category Switch
			$("a[id^='cat_']").click(function() {
				var catID = this.id.substring(4);
				var catSet = 'cat_' + this.id.substring(4);
				$("a[id^='cat_']").removeClass("active"); // Remove All active
				$("[id^='child_']").hide(); // Hide All Children DIV
				$("#cat_" + catID).addClass("active"); // Add Highlight
				$("#child_" + catID).show(); // Show children DIV
				$(this).parents("div").show();
				
				currentCat = catID;
				$("#currentCat").val(catID);
				// setUrl not supported with Cluster Strategy
				//markers.setUrl("<?php echo url::base(); ?>" json_url + '/?c=' + catID);
				
				// Destroy any open popups
				onPopupClose();
				
				// Get Current Zoom
				currZoom = map.getZoom();
				
				// Get Current Center
				currCenter = map.getCenter();
					
				addMarkers(catID, '', '', currZoom, currCenter, gMediaType);
				
				graphData = dailyGraphData[0][catID];
				gCategoryId = catID;
				var startTime = new Date($("#startDate").val() * 1000);
				var endTime = new Date($("#endDate").val() * 1000);
				gTimeline = $.timeline({categoryId: catID, startTime: startTime, endTime: endTime,
					graphData: graphData,
					//url: "<?php echo url::base(); ?>json_url + '/timeline/'",
					mediaType: gMediaType
				});
				gTimeline.plot();
				
				return false;
			});
			
			// Sharing Layer[s] Switch
			$("a[id^='share_']").click(function() {
				var shareID = this.id.substring(6);
				
				if ( $("#share_" + shareID).hasClass("active") ) {
					share_layer = map.getLayersByName("Share_"+shareID);
					if (share_layer){
						for (var i = 0; i < share_layer.length; i++) {
							map.removeLayer(share_layer[i]);
						}
					}
					$("#share_" + shareID).removeClass("active");
					
				} else {
					$("#share_" + shareID).addClass("active");
					
					// Get Current Zoom
					currZoom = map.getZoom();

					// Get Current Center
					currCenter = map.getCenter();
					
					// Add New Layer
					addMarkers('', '', '', currZoom, currCenter, '', shareID, 'shares');
				}
			});

			// Exit if we don't have any incidents
			if (!$("#startDate").val()) {
				map.setCenter(new OpenLayers.LonLat(<?php echo $longitude ?>, <?php echo $latitude ?>), 5);
				return;
			}
			
			//Accessible Slider/Select Switch
			$("select#startDate, select#endDate").selectToUISlider({
				labels: 4,
				labelSrc: 'text',
				sliderOptions: {
					change: function(e, ui) {
						var startDate = $("#startDate").val();
						var endDate = $("#endDate").val();
						var currentCat = gCategoryId;
						
						// Get Current Category
						currCat = currentCat;
						
						// Get Current Zoom
						currZoom = map.getZoom();
						
						// Get Current Center
						currCenter = map.getCenter();
						
						// If we're in a two day date range, switch to 
						// non-clustered mode
						var startTime = new Date(startDate * 1000);
						var endTime = new Date(endDate * 1000);
						if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 3){
							json_url = "json"
						} else {
							json_url = "json_cluster"
						}
						
						// Refresh Map
						addMarkers(currCat, startDate, endDate, '', '', gMediaType);
						
						refreshGraph(startDate, endDate);
					}
				}
			});
		
			// Graph
			allGraphData = [<?php echo $all_graphs ?>];
			dailyGraphData = [<?php echo $daily_graphs ?>];
			weeklyGraphData = [<?php echo $weekly_graphs ?>];
			hourlyGraphData = [<?php echo $hourly_graphs ?>];
			var startTime = <?php echo $active_startDate ?>;	// Default to most active month
			var endTime = <?php echo $active_endDate ?>;		// Default to most active month
					
			// get the closest existing dates in the selection options
			var options = $('#startDate > optgroup > option').map(function() { 
				return $(this).val(); 
			});
			startTime = $.grep(options, function(n,i) {
			  return n >= ('' + startTime) ;
			})[0];
			
			options = $('#endDate > optgroup > option').map(function() { 
				return $(this).val(); 
			});
			endTime = $.grep(options, function(n,i) {
			  return n >= ('' + endTime) ;
			})[0];
			
			gCategoryId = '0';
			gMediaType = 0;
			//$("#startDate").val(startTime);
			//$("#endDate").val(endTime);
			
			// Initialize Map
			addMarkers(gCategoryId, startTime, endTime, '', '', gMediaType);
			refreshGraph(startTime, endTime);
			
			// media filter
			$('.filters li a').click(function(){
				var startTimestamp = $("#startDate").val();
				var endTimestamp = $("#endDate").val();
				var startTime = new Date(startTimestamp * 1000);
				var endTime = new Date(endTimestamp * 1000);
				gMediaType = parseFloat(this.id.replace('media_', '')) || 0;
				
				// Get Current Zoom
				currZoom = map.getZoom();
					
				// Get Current Center
				currCenter = map.getCenter();
				
				// Refresh Map
				addMarkers(currentCat, startTimestamp, endTimestamp, 
				           currZoom, currCenter, gMediaType);
				
				$('.filters li a').attr('class', '');
				$(this).addClass('active');
				gTimeline = $.timeline({categoryId: gCategoryId, startTime: startTime, 
				    endTime: endTime, mediaType: gMediaType,
					url: "<?php echo url::base(); ?>json_url+'/timeline/'"
				});
				gTimeline.plot();
			});
			
			$('#playTimeline').click(function() {
				gTimeline.playOrPause();
			});
		});
		
		
		
		/*
		Create the Markers Layer
		*/
		function addMarkers(catID,startDate,endDate, currZoom, currCenter,
			mediaType, thisLayerID, thisLayerType, thisLayerUrl, thisLayerColor){
			
			var	protocolUrl = "<?php echo url::base(); ?>" + json_url + "/"; // Default Json
			var thisLayer = "Reports"; // Default Layer Name
			var protocolFormat = OpenLayers.Format.GeoJSON;
			newlayer = false;
			
			if (thisLayer && thisLayerType == 'shares')
			{				
				protocolUrl = "<?php echo url::base(); ?>" + json_url + "/share/"+thisLayer+"/";
				thisLayer = "Share_"+thisLayerID;
				newlayer = true;
			} else if (thisLayer && thisLayerType == 'layers') {
				protocolUrl = "<?php echo url::base(); ?>" + json_url + "/layer/"+thisLayerID+"/";
				thisLayer = "Layer_"+thisLayerID;
				
				var protocolFormat = OpenLayers.Format.KML;
				//var protocolFormat = OpenLayers.Format.GeoJSON;
				
				newlayer = true;
			}
			
			var myPoint;
			if ( currZoom && currCenter && 
				typeof(currZoom) != 'undefined' && typeof(currCenter) != 'undefined')
			{
				myPoint = currCenter;
				myZoom = currZoom;				
			}else{
				// create a lat/lon object
				myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
				myPoint.transform(proj_4326, map.getProjectionObject());
				
				// display the map centered on a latitude and longitude (Google zoom levels)
				myZoom = <?php echo $default_zoom; ?>;
			}
			
			if (mapLoad == 0) {
				map.setCenter(myPoint, myZoom, false, false);
			}
			
			mapLoad = mapLoad+1;
			
			// Get Viewport Boundaries				
			extent = map.getExtent().transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
			southwest = extent.bottom+','+extent.left;
			northeast = extent.top+','+extent.right;
			
			
			// Set Feature Styles
			style = new OpenLayers.Style({
				'externalGraphic': "${icon}",
				'graphicTitle': "${cluster_count}",
				pointRadius: "${radius}",
				fillColor: "${color}",
				fillOpacity: "${opacity}",
				strokeColor: "${color}",
				strokeWidth: "${strokeWidth}",
				strokeOpacity: "0.3",
				label:"${cluster_count}",
				labelAlign: "${labelalign}",
				fontWeight: "${fontweight}",
				fontColor: "#ffffff",
				fontSize: "${fontsize}"
			}, 
			{
				context: 
				{
					count: function(feature)
					{
						if (feature.attributes.count < 2) {
							return 2 * <?php echo $marker_radius; ?>
						} else if (feature.attributes.count == 2) {
							return (Math.min(feature.attributes.count, 7) + 1) * 
								(<?php echo $marker_radius; ?> * 0.8);
						} else {
							return (Math.min(feature.attributes.count, 7) + 1) * 
								(<?php echo $marker_radius; ?> * 0.6);
						}							
					},
					fontsize: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!="") {
							return "9px";
						} else {
							feature_count = feature.attributes.count;
							if (feature_count > 1000)
							{
								return "20px";
							}
							else if (feature_count > 500)
							{
								return "18px";
							}
							else if (feature_count > 100)
							{
								return "14px";
							}
							else if (feature_count > 10)
							{
								return "12px";
							}
							else if (feature_count >= 2)
							{
								return "10px";
							}
							else
							{
								return "";
							}
						}					
					},
					fontweight: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!="") {
							return "normal";
						} else {
							return "bold";
						}
					},
					radius: function(feature)
					{
						feature_count = feature.attributes.count;
						if (feature_count > 10000) {
							return <?php echo $marker_radius; ?> * 17;
						}
						else if (feature_count > 5000)
						{
							return <?php echo $marker_radius; ?> * 10;
						}
						else if (feature_count > 1000)
						{
							return <?php echo $marker_radius; ?> * 8;
						}
						else if (feature_count > 500)
						{
							return <?php echo $marker_radius; ?> * 7;
						}
						else if (feature_count > 100)
						{
							return <?php echo $marker_radius; ?> * 6;
						}
						else if (feature_count > 10)
						{
							return <?php echo $marker_radius; ?> * 5;
						}
						else if (feature_count >= 2)
						{
							return <?php echo $marker_radius; ?> * 3;
						}
						else
						{
							return <?php echo $marker_radius; ?> * 2;
						}
					},
					strokeWidth: function(feature)
					{
						feature_count = feature.attributes.count;
						if (feature_count > 10000) {
							return 45;
						}
						else if (feature_count > 5000)
						{
							return 30;
						}
						else if (feature_count > 1000)
						{
							return 22;
						}
						else if (feature_count > 100)
						{
							return 15;
						}
						else if (feature_count > 10)
						{
							return 10;
						}
						else if (feature_count >= 2)
						{
							return 5;
						}
						else
						{
							return 1;
						}
					},					
					color: function(feature) 
					{
						return "#" + feature.attributes.color;
					},
					icon: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!="") {
							return "<?php echo url::base() . 'media/uploads/' ?>" + feature_icon;
						} else {
							return "";
						}
					},
					cluster_count: function(feature)
					{
						if (feature.attributes.count > 1)
						{
							feature_icon = feature.attributes.icon;
							if (feature_icon!="") {
								return "> " + feature.attributes.count;
							} else {
								return feature.attributes.count;
							}
						}
						else
						{
							return "";
						}
					},
					opacity: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!="") {
							return "1";
						} else {
							return "<?php echo $marker_opacity; ?>";
						}
					},
					labelalign: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!="") {
							return "lb";
						} else {
							return "c";
						}
					},					
				}
			});
			
			// Transform feature point coordinate to Spherical Mercator
			preFeatureInsert = function(feature) {			
				var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
				OpenLayers.Projection.transform(point, proj_4326, proj_900913);
			};
			
			// Does 'markers' already exist? If so, destroy it before creating new layer
			markers = map.getLayersByName(thisLayer);
			if (markers && markers.length > 0){
				for (var i = 0; i < markers.length; i++) {
					//markers[i].destroy();
					//markers[i] = null;
					map.removeLayer(markers[i]);
				}
			}
			
			// Add parameters
			params = [];
			if (typeof(catID) != 'undefined' && catID.length > 0){
				params.push('c=' + catID);
			}
			if (typeof(startDate) != 'undefined'){
				params.push('s=' + startDate);
			}
			if (typeof(endDate) != 'undefined'){
				params.push('e=' + endDate);
			}
			if (typeof(mediaType) != 'undefined'){
				//params.push('m=' + mediaType);
			}
			
			//markers = new OpenLayers.Layer.GML(thisLayer, protocolUrl + '?z='+ myZoom +'&sw='+ southwest +'&ne='+ northeast +'&' + params.join('&'), 
			markers = new OpenLayers.Layer.GML(thisLayer, protocolUrl + '?z='+ myZoom + '&' + params.join('&'), 
			{
				preFeatureInsert:preFeatureInsert,
				format: protocolFormat,					
				projection: proj_4326,
				formatOptions: {
					extractStyles: true,
					extractAttributes: true
				},
				styleMap: new OpenLayers.StyleMap({
					"default":style,
					"select": style
				})
			});
			
			map.addLayer(markers);
			
			if (!newlayer || thisLayerID==8) {
				selectControl = new OpenLayers.Control.SelectFeature(
					markers
				);

	            map.addControl(selectControl);
	            selectControl.activate();

				markers.events.on({
					"featureselected": onFeatureSelect,
					"featureunselected": onFeatureUnselect
				});
			}
		}
		
		gAddMarkers = addMarkers;
		//addMarkers();
		
		
		/*
		Display loader as Map Loads
		*/
		function onMapStartLoad(event) {
			$("#loader").show();
		}
		
		/*
		Hide Loader
		*/
		function onMapEndLoad(event) {
			$("#loader").hide();
		}
		
		/*
		Close Popup
		*/
		function onPopupClose(evt) {
            // selectControl.unselect(selectedFeature);
			for (var i=0; i<map.popups.length; ++i)
			{
				map.removePopup(map.popups[i]);
			}
        }

		/*
		Display popup when feature selected
		*/
        function onFeatureSelect(event) {
            selectedFeature = event;
            // Since KML is user-generated, do naive protection against
            // Javascript.

			zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
			lon = zoom_point.lon;
			lat = zoom_point.lat;
			
			var content = "<div class=\"infowindow\"><div class=\"infowindow_list\"><ul><li>"+event.feature.attributes.name + "</li></ul><div style=\"clear:both;\"></div></div>";
			content = content + "\n<div class=\"infowindow_meta\"><a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +", 1)'>Zoom&nbsp;In</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +", -1)'>Zoom&nbsp;Out</a></div>";
			content = content + "</div>";			
			
			if (content.search("<script") != -1) {
                content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/</g, "&lt;");
            }
            popup = new OpenLayers.Popup.FramedCloud("chicken", 
				event.feature.geometry.getBounds().getCenterLonLat(),
				new OpenLayers.Size(100,100),
				content,
				null, true, onPopupClose);
            event.feature.popup = popup;
            map.addPopup(popup);
        }
		
		/*
		Destroy Popup Layer
		*/
        function onFeatureUnselect(event) {
            map.removePopup(event.feature.popup);
            event.feature.popup.destroy();
            event.feature.popup = null;
        }

		// Refactor Clusters On Zoom
		// *** Causes the map to load json twice on the first go
		// *** Need to fix this!
		function mapZoom(event) {
			// Prevent this event from running on the first load
			if (mapLoad > 0) {
				// Get Current Category
				currCat = $("#currentCat").val();

				// Get Current Start Date
				currStartDate = $("#startDate").val();

				// Get Current End Date
				currEndDate = $("#endDate").val();

				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();

				// Refresh Map
				addMarkers(currCat, currStartDate, currEndDate, currZoom, currCenter);
			}
		}
		
		function mapMove(event) {
			// Prevent this event from running on the first load
			if (mapLoad > 0) {
				// Get Current Category
				currCat = $("#currentCat").val();

				// Get Current Start Date
				currStartDate = $("#startDate").val();

				// Get Current End Date
				currEndDate = $("#endDate").val();

				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();

				// Refresh Map
				addMarkers(currCat, currStartDate, currEndDate, currZoom, currCenter);
			}
		}
		
		
		/*
		Refresh Graph on Slider Change
		*/
		function refreshGraph(startDate, endDate){
			var currentCat = gCategoryId;
			
			// refresh graph
			if (!currentCat || currentCat == '0') {
				currentCat = '0';
			}
			
			var startTime = new Date(startDate * 1000);
			var endTime = new Date(endDate * 1000);
			// daily
			var graphData = dailyGraphData[0][currentCat];

			// plot hourly incidents when period is within 2 days
			if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 3) {
			    graphData = hourlyGraphData[0][currentCat];
			} else if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 124) { 
			    // weekly if period > 2 months
			    graphData = dailyGraphData[0][currentCat];
			} else if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 124) {
				// monthly if period > 4 months
			    graphData = allGraphData[0][currentCat];
			}
			
			gTimeline = $.timeline({categoryId: currentCat, startTime: new Date(startDate * 1000), 
			    endTime: new Date(endDate * 1000), mediaType: gMediaType,
				graphData: graphData //allGraphData[0][currentCat], 
				//url: "<?php echo url::base(); ?>json_url+'/timeline/'"
			});
			gTimeline.plot();
		}
		
		/*
		Zoom to Selected Feature from within Popup
		*/
		function zoomToSelectedFeature(lon, lat){
			var lonlat = new OpenLayers.LonLat(lon,lat);
			
			// Get Current Zoom
			currZoom = map.getZoom();
			// New Zoom
			newZoom = currZoom + 1;
			// Center and Zoom
			map.setCenter(lonlat, newZoom);
			// Remove Popups
			for (var i=0; i<map.popups.length; ++i)
			{
				map.removePopup(map.popups[i]);
			}
		}
		
		/*
		Add KML/KMZ Layers
		*/
		function switchLayer(layerID, layerURL, layerColor){
			if ( $("#layer_" + layerID).hasClass("active") ) {
				new_layer = map.getLayersByName("Layer_"+layerID);
				if (new_layer){
					for (var i = 0; i < new_layer.length; i++) {
						map.removeLayer(new_layer[i]);
					}
				}
				$("#layer_" + layerID).removeClass("active");
				
			} else {
				$("#layer_" + layerID).addClass("active");
				
				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();
				
				// Add New Layer
				addMarkers('', '', '', currZoom, currCenter, '', layerID, 'layers', layerURL, layerColor);
			}
		}
		
		
		/*		
		d = $('#startDate > optgroup > option').map(function() { return $(this).val(); });

$.grep(d, function(n,i) {
  return n > '1183240800';
})[0];
*/
