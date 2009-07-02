<?php 
/**
 * Main js file.
 * 
 * Handles javascript stuff related to main function.
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
		jQuery(function() {
			var map_layer;
	
			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			- Units in Metres instead of Degrees					
			*/
			var options = {
				units: "m",
				numZoomLevels: 16,
				controls:[],
				projection: new OpenLayers.Projection("EPSG:900913")
				};
			var map = new OpenLayers.Map('map', options);
			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
			
			/*
			- Select A Mapping API
			- Live/Yahoo/OSM/Google
			- Set Bounds					
			*/
			var default_map = <?php echo $default_map; ?>;
			if (default_map == 2)
			{
				map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else if (default_map == 3)
			{
				map_layer = new OpenLayers.Layer.Yahoo("yahoo", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else if (default_map == 4)
			{
				map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else
			{
				map_layer = new OpenLayers.Layer.Google("google", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			map.addLayer(map_layer);
			
			// Add Controls
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.Attribution());
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			// Set Feature Styles
			style = new OpenLayers.Style({
				'externalGraphic': "${icon}",
				pointRadius: "${radius}",
				fillColor: "${color}",
				fillOpacity: "${opacity}",
				strokeColor: "#<?php echo $default_map_all;?>",
				strokeWidth: 1,
				strokeOpacity: 1,
				'graphicYOffset': -20
			}, 
			{
				context: 
				{
					radius: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!="") {
							return 16;
						} else {
							return 9;
						}
					},
					opacity: function(feature)
					{
						feature_icon = feature.attributes.icon;
						if (feature_icon!="") {
							return 1;
						} else {
							return 0.8;
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
					}					
				}
			});
			
			// Transform feature point coordinate to Spherical Mercator
			preFeatureInsert = function(feature) {
				var src = new OpenLayers.Projection('EPSG:4326');
				var dest = new OpenLayers.Projection('EPSG:900913');			
				var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
				OpenLayers.Projection.transform(point, src, dest);
			};
			
			// Create the markers layer
			markers = new OpenLayers.Layer.GML("reports", "<?php echo url::base() . 'json' ?>", 
			{
				preFeatureInsert:preFeatureInsert,
				format: OpenLayers.Format.GeoJSON,
				projection: new OpenLayers.Projection("EPSG:4326"),
				formatOptions: {
					extractStyles: true,
					extractAttributes: true
				},
				styleMap: new OpenLayers.StyleMap({"default":style})
			});			
			map.addLayer(markers);
			
			selectControl = new OpenLayers.Control.SelectFeature(markers,
                {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
            map.addControl(selectControl);
            selectControl.activate();
			
			
			// Create center lat/lon object, converted to metres
			var proj = new OpenLayers.Projection("EPSG:4326");
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
			myPoint.transform(proj, map.getProjectionObject());
			
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, <?php echo $default_zoom; ?>);

			
			
			function onPopupClose(evt) {
	            // selectControl.unselect(selectedFeature);
				for (var i=0; i<map.popups.length; ++i)
				{
					map.removePopup(map.popups[i]);
				}
	        }
	        function onFeatureSelect(feature) {
	            selectedFeature = feature;
	            // Since KML is user-generated, do naive protection against
	            // Javascript.
	            var content = "<div class=\"infowindow\"><h2>"+feature.attributes.name + "</h2></div>";
	            if (content.search("<script") != -1) {
	                content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/</g, "&lt;");
	            }
	            popup = new OpenLayers.Popup.FramedCloud("chicken", 
	                                     feature.geometry.getBounds().getCenterLonLat(),
	                                     new OpenLayers.Size(100,100),
	                                     content,
	                                     null, true, onPopupClose);
	            feature.popup = popup;
	            map.addPopup(popup);
	        }
	        function onFeatureUnselect(feature) {
	            map.removePopup(feature.popup);
	            feature.popup.destroy();
	            feature.popup = null;
	        }
	
			// Category Switch
			$("a[id^='cat_']").click(function() {
				var catID = this.id.substring(4);
				var catSet = 'cat_' + this.id.substring(4);
				$("a[id^='cat_']").removeClass("active");
				$("#cat_" + catID).addClass("active");
				$("#currentCat").val(catID);
				markers.setUrl("<?php echo url::base() . 'json/?c=' ?>" + catID);
			});
			
			if (!$("#startDate").val()) {
				return;
			}
			
			//Accessible Slider/Select Switch
			$("select#startDate, select#endDate").selectToUISlider({
				labels: 6,
				labelSrc: 'text',
				sliderOptions: {
					change: function(e, ui) {
						var startDate = $("#startDate").val();
						var endDate = $("#endDate").val();
						var currentCat = gCategoryId;

						// refresh graph
						if (!currentCat || currentCat == '0') {
							currentCat = 'ALL';
						}
						
						gTimeline = $.timeline({categoryId: currentCat, 
							startTime: new Date(startDate * 1000), 
						    endTime: new Date(endDate * 1000),
							graphData: allGraphData[0][currentCat], 
							mediaType: gMediaType,
							url: "<?php echo url::base() . 'json/timeline/' ?>"
						});
						gTimeline.plot();
						gTimeline.plotMarkers(style, markers);
					}
				}
			});

		
			// Graph
			allGraphData = [<?php echo $all_graphs ?>];
			var plotPeriod = $.timelinePeriod(allGraphData[0]['ALL'].data);
			var startTime = $.monthStartTime(plotPeriod[0]) / 1000;
			var endTime = $.monthEndDateTime(plotPeriod[1]) / 1000;
			$("#startDate").val(startTime);
			$("#endDate").val(endTime);
			gCategoryId = 'ALL';
			gMediaType = 0;
			$("#startDate, #endDate").change();

			var categoryIds = [0,<?php echo join(array_keys($categories), ","); ?>];
				
			for (var i=0; i<categoryIds.length; i++) {
				$('#cat_'+categoryIds[i]).click(function(){
					onPopupClose(false);
					var categories = <?php echo json_encode($categories); ?>;
					categories['0'] = ["ALL", "#990000"];
					graphData = allGraphData[0][categories[this.id.split("_")[1]][0]];
					var catId = categories[this.id.split("_")[1]][0];
					gCategoryId = catId;
					
					var startTime = new Date($("#startDate").val() * 1000);
					var endTime = new Date($("#endDate").val() * 1000);
					gTimeline = $.timeline({categoryId: catId, startTime: startTime, endTime: endTime,
			            graphData: graphData,
			            url: "<?php echo url::base() . 'json/timeline/' ?>",
			            mediaType: gMediaType
					});
					gTimeline.plot();
				});
			}
			
			// media filter
			$('.filter a').click(function(){
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
				// TODO: Fix filter markers on media type
				//addMarkers($('#currentCat').val(), startTimestamp, endTimestamp, 
				//           currZoom, currCenter, gMediaType);
				
				$('.filter a').attr('class', '');
				$(this).addClass('active');
				gTimeline = $.timeline({categoryId: gCategoryId, startTime: startTime, 
				    endTime: endTime, mediaType: gMediaType,
					url: "<?php echo url::base() . 'json/timeline/' ?>"
				});
				gTimeline.plot();
			});
			
			$('#playTimeline').click(function() {
				gTimeline.resetPlay().play();
			});
		});
