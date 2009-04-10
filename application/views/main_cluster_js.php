<?php
/**
 * Main cluster js file.
 * 
 * Handles javascript stuff related to main cluster function.
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
			var markers;
			var catID = '';
			
			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			- Units in Metres instead of Degrees					
			*/
			var options = {
				units: "m",
				numZoomLevels: 16,
				controls:[],
				projection: new OpenLayers.Projection("EPSG:900913"),
				'displayProjection': new OpenLayers.Projection("EPSG:4326")
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
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.LayerSwitcher());		
			
			
			// Create the markers layer
			function addMarkers(catID,startDate,endDate, currZoom, currCenter, mediaType){
				
				// Set Feature Styles
				var style = new OpenLayers.Style({
					pointRadius: "${radius}",
					fillColor: "${color}",
					fillOpacity: 0.9,
					strokeColor: "#990000",
					strokeWidth: 2,
					strokeOpacity: 0.8
				}, 
				{
					context: 
					{
						radius: function(feature)
						{
							return (Math.min(feature.attributes.count, 7) + 3) * 1.6;
						},
						color: function(feature)
						{
							if ( feature.cluster.length < 2 || (typeof(catID) != 'undefined' && catID.length > 0 && catID != 0))
							{
								return "#" + feature.cluster[0].data.color;
							}
							else
							{
								return "#990000";
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
				
				// Does 'markers' already exist? If so, destroy it before creating new layer
				if (markers){
					for (var i = 0; i < markers.length; i++) {
						markers[i].destroy();
						markers[i] = null;
					}
					map.removeLayer(markers);
				}
				
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
					params.push('m=' + mediaType);
				}
				
				markers = new OpenLayers.Layer.Vector("Reports", {
					preFeatureInsert:preFeatureInsert,
					strategies: [
						new OpenLayers.Strategy.Fixed(),
					    new OpenLayers.Strategy.Cluster({
							distance: 15
						})
					],
					protocol: new OpenLayers.Protocol.HTTP({
	                    url: "<?php echo url::base() . 'json' ?>" + '/?' + params.join('&'),
	                    format: new OpenLayers.Format.GeoJSON(
							{
								internalProjection: map.getProjectionObject(),
								externalProjection: map.displayProjection 
							})
	                }),
					projection: new OpenLayers.Projection("EPSG:4326"),
					formatOptions: {
						extractStyles: true,
						extractAttributes: true
					},
					styleMap: new OpenLayers.StyleMap({
						"default": style
					})
				});
				
				map.addLayer(markers);
				selectControl = new OpenLayers.Control.SelectFeature(
					markers
				);

	            map.addControl(selectControl);
	            selectControl.activate();
				
				markers.events.on({
					"featureselected": onFeatureSelect,
					"featureunselected": onFeatureUnselect
				});
				
				var myPoint;
				if (typeof(currZoom) != 'undefined' && typeof(currCenter) != 'undefined')
				{
					myPoint = currCenter;
					myZoom = currZoom;
					
				}else{
					// create a lat/lon object
					var proj = new OpenLayers.Projection("EPSG:4326");
					myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
					myPoint.transform(proj, map.getProjectionObject());
					
					// display the map centered on a latitude and longitude (Google zoom levels)
					myZoom = <?php echo $default_zoom; ?>;
				};
				map.setCenter(myPoint, myZoom);
			}
			addMarkers();
			
			function onMapStartLoad(event) {
				$("#loader").show();
				// alert('started');
			}
			
			function onMapEndLoad(event) {
				$("#loader").hide();
				// document.write(print_r(event.features));
				// alert('loaded');
			}
			
			function onPopupClose(evt) {
	            // selectControl.unselect(selectedFeature);
				for (var i=0; i<map.popups.length; ++i)
				{
					map.removePopup(map.popups[i]);
				}
	        }
	        function onFeatureSelect(event) {
	            selectedFeature = event;
	            // Since KML is user-generated, do naive protection against
	            // Javascript.
				var content = "<div class=\"infowindow\">";
				content = content + "<h2>" + event.feature.cluster.length + " Event[s]...</h2>\n";
				for(var i=0; i<Math.min(event.feature.cluster.length, 5); ++i) {
					content = content + "\n<h3>" + event.feature.cluster[i].data.name + "</h3>";
				}
				if (event.feature.cluster.length > 1)
				{
					content = content + "\n<BR><a href=\"<?php echo url::base() . 'reports/' ?>\">More...</a> "
				}
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
	        function onFeatureUnselect(event) {
	            map.removePopup(event.feature.popup);
	            event.feature.popup.destroy();
	            event.feature.popup = null;
	        }
	
			// Category Switch
			$("a[@id^='cat_']").click(function() {
				var catID = this.id.substring(4);
				var catSet = 'cat_' + this.id.substring(4);
				$("a[@id^='cat_']").removeClass("active");
				$("#cat_" + catID).addClass("active");
				$("#currentCat").val(catID);
				// setUrl not supported with Cluster Strategy
				//markers.setUrl("<?php echo url::base() . 'json/?c=' ?>" + catID);
				
				// Destroy any open popups
				onPopupClose();
				
				// Get Current Zoom
				currZoom = map.getZoom();
				
				// Get Current Center
				currCenter = map.getCenter();
					
				addMarkers(catID, '', '', currZoom, currCenter, gMediaType);
			});
			
			if (!$("#startDate").val()) {
				return;
			}
			
			//Accessible Slider/Select Switch
			$("select#startDate, select#endDate").accessibleUISlider({
				labels: 6,
				stop: function(e, ui) {
					var startDate = $("#startDate").val();
					var endDate = $("#endDate").val();
					var currentCat = gCategoryId;
/*					
					var sliderfilter = new OpenLayers.Rule({
						filter: new OpenLayers.Filter.Comparison(
						{
							type: OpenLayers.Filter.Comparison.BETWEEN,
							property: "timestamp",
							lowerBoundary: startDate,
							upperBoundary: endDate
						})
					});
									    
					style.rules = [];
					style.addRules(sliderfilter);					
					markers.styleMap.styles["default"] = style; 
*/					//markers.refresh();
					//markers.redraw();
					
					// Get Current Category
					currCat = $("#currentCat").val();
					
					// Get Current Zoom
					currZoom = map.getZoom();
					
					// Get Current Center
					currCenter = map.getCenter();
					
					// Refresh Map
					addMarkers(currCat, startDate, endDate, currZoom, currCenter, gMediaType);
					
					// refresh graph
					if (!currentCat || currentCat == '0') {
						currentCat = 'ALL';
					}
					$.timeline({categoryId: currentCat, startTime: new Date(startDate * 1000), 
					    endTime: new Date(endDate * 1000), mediaType: gMediaType,
						graphData: allGraphData[0][currentCat], 
						url: "<?php echo url::base() . 'json/timeline/' ?>"
					}).plot();
				}
			}); //.hide();
		
			// Graph
			var allGraphData = [<?php echo $all_graphs ?>];
			var startTime = new Date($("#startDate").val() * 1000);
			var endTime = new Date($("#endDate").val() * 1000);
			$.timeline({categoryId: 'ALL', startTime: startTime, endTime: endTime,
			    graphData: allGraphData[0]['ALL'],
			    url: "<?php echo url::base() . 'json/timeline/' ?>"
			}).plot();
			
			var categoryIds = [0,<?php echo join(array_keys($categories), ","); ?>];
			gMediaType = 0;
				
			for (var i=0; i<categoryIds.length; i++) {
				$('#cat_'+categoryIds[i]).click(function(){
					var categories = <?php echo json_encode($categories); ?>;
					categories['0'] = ["ALL", "#990000"];
					graphData = allGraphData[0][categories[this.id.split("_")[1]][0]];
					var catId = categories[this.id.split("_")[1]][0];
					gCategoryId = catId;
					
					var startTime = new Date($("#startDate").val() * 1000);
					var endTime = new Date($("#endDate").val() * 1000);
					$.timeline({categoryId: catId, startTime: startTime, endTime: endTime,
						graphData: graphData,
						url: "<?php echo url::base() . 'json/timeline/' ?>",
						mediaType: gMediaType
					}).plot();
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
				addMarkers($('#currentCat').val(), startTimestamp, endTimestamp, 
				           currZoom, currCenter, gMediaType);
				
				$('.filter a').attr('class', '');
				$(this).addClass('active');
				$.timeline({categoryId: gCategoryId, startTime: startTime, 
				    endTime: endTime, mediaType: gMediaType,
					url: "<?php echo url::base() . 'json/timeline/' ?>"
				}).plot();
			});
		});
