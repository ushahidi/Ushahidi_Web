/*
		* Main Javascript
		* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		* TESTING SERVER SIDE CLUSTERING
		* REPLACE main_js.php with this one to use
		* Clustering (heat mapping)
		* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		*/

		// Map JS
		jQuery(function() {
			var map_layer;
			var markers;
			var southwest;
			var northeast;
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
			function addMarkers(catID,startDate,endDate, currZoom, currCenter){
				
				// Does 'markers' already exist? If so, destroy it before creating new layer
				if (markers){
					for (var i = 0; i < markers.length; i++) {
						markers[i].destroy();
						markers[i] = null;
					}
					map.removeLayer(markers);
				}
				
				// Set Map Center
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
				
				// Get Viewport Boundaries				
				extent = map.getExtent().transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
				southwest = extent.bottom+','+extent.left;
				northeast = extent.top+','+extent.right;
				
				// Set Feature Styles
				var style = new OpenLayers.Style({
					label: "${count}",
					fontColor: "#ffffff",
					fontSize: "${fontsize}",
					fontWeight: "bold",
					labelAlign: "center",
					pointRadius: "${radius}",
					fillColor: "${color}",
					fillOpacity: 0.8,
					strokeColor: "#000000",
					strokeWidth: 1,
					strokeOpacity: 1
				}, 
				{
					context: 
					{
						count: function(feature)
						{
							if (feature.attributes.count == 1) {
								return "";
							} else {
								return feature.attributes.count;
							}							
						},
						fontsize: function(feature)
						{
							if (feature.attributes.count > 10) {
								return "18px";
							} else {
								return "13px";
							}							
						},
						radius: function(feature)
						{					
							var feature_count = feature.attributes.count;		
							return (Math.min(feature_count, 20) + 8) * 1.01;
						},
						color: function(feature) 
						{
							return "#" + feature.attributes.color;
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
				
				// Create the markers layer
				markers = new OpenLayers.Layer.GML("reports", "<?php echo url::base() . 'json_test' ?>" + '/?z='+ myZoom +'&sw='+ southwest +'&ne='+ northeast +'&' + params.join('&'), 
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
			}
			addMarkers();
			
			// Refactor Clusters On Zoom
			map.events.register('zoomend', null, function() {
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
			});
			
			
			function onMapStartLoad(event) {
				$("#loader").show();
			}
			
			function onMapEndLoad(event) {
				$("#loader").hide();
			}
			
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
	        function onFeatureUnselect(event) {
	            map.removePopup(event.feature.popup);
	            event.feature.popup.destroy();
	            event.feature.popup = null;
	        }
	
			// Category Switch
			$("a[id^='cat_']").click(function() {
				var catID = this.id.substring(4);
				var catSet = 'cat_' + this.id.substring(4);
				$("a[id^='cat_']").removeClass("active");
				$("#cat_" + catID).addClass("active");
				$("#currentCat").val(catID);
				
				// Destroy any open popups
				onPopupClose();
				
				// Get Current Start Date
				currStartDate = $("#startDate").val();
				
				// Get Current End Date
				currEndDate = $("#endDate").val();
				
				// Get Current Zoom
				currZoom = map.getZoom();
				
				// Get Current Center
				currCenter = map.getCenter();
				
				addMarkers(catID, currStartDate, currEndDate, currZoom, currCenter);
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
						
						// Get Current Category
						currCat = $("#currentCat").val();
						
						// Get Current Zoom
						currZoom = map.getZoom();
						
						// Get Current Center
						currCenter = map.getCenter();
						
						// Refresh Map
						addMarkers(currCat, startDate, endDate, currZoom, currCenter);
						
						// refresh graph
						if (!currentCat || currentCat == '0') {
							currentCat = 'ALL';
						}
						$.timeline({categoryId: currentCat, startTime: new Date(startDate * 1000), 
						    endTime: new Date(endDate * 1000),
							graphData: allGraphData[0][currentCat], 
							url: "<?php echo url::base() . 'json_test/timeline/' ?>"
						}).plot();
					}
				}
			}); //.hide();
		
			// Graph
			var allGraphData = [<?php echo $all_graphs ?>];
			var startTime = new Date($("#startDate").val() * 1000);
			var endTime = new Date($("#endDate").val() * 1000);
			$.timeline({categoryId: 'ALL', startTime: startTime, endTime: endTime,
			    graphData: allGraphData[0]['ALL'],
			    url: "<?php echo url::base() . 'json_test/timeline/' ?>"
			}).plot();
			
			var categoryIds = [0,<?php echo join(array_keys($categories), ","); ?>];
				
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
						url: "<?php echo url::base() . 'json_test/timeline/' ?>"
					}).plot();
				});
			}
		});
