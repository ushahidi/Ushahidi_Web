/*
		* Main Javascript
		*/

		// Map JS
		jQuery(function() {
			var moved=false;
	
			// Now initialise the map
			var options = {
			units: "dd"
			, numZoomLevels: 16
			, controls:[]};
			var map = new OpenLayers.Map('map', options);
			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
			var default_map = <?php echo $default_map; ?>;
			if (default_map == 2)
			{
				var map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth");
			}
			else if (default_map == 3)
			{
				var map_layer = new OpenLayers.Layer.Yahoo("yahoo");
			}
			else if (default_map == 4)
			{
				var map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap");
			}
			else
			{
				var map_layer = new OpenLayers.Layer.Google("google");
			}
	
			map.addLayer(map_layer);
	
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.Attribution());
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
						
			
			// Create the markers layer
			var markers = new OpenLayers.Layer.GML("KML", "<?php echo url::base() . 'markers' ?>", 
			{
				format: OpenLayers.Format.KML, 
				formatOptions: 
				{
					extractStyles: true, 
					extractAttributes: true
				}
			});
			map.addLayer(markers);
			selectControl = new OpenLayers.Control.SelectFeature(markers,
                {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});

            map.addControl(selectControl);
            selectControl.activate();
			
			
			// Create the heatmap layer
			
			// Polygon Styles
			var style = new OpenLayers.Style({
				pointRadius: "${radius}",
				fillColor: "#990000",
				fillOpacity: 0.8,
				strokeColor: "#990000",
				strokeWidth: 2,
				strokeOpacity: 0.8
			}, 
			{
				context: 
				{
					radius: function(feature) 
					{
						return Math.min(feature.attributes.count, 7) + 4;
					}
				}
			});
			
			// Heatmap
			var heatmap = new OpenLayers.Layer.Vector("HeatMap", {
				strategies: [
					new OpenLayers.Strategy.Fixed(),
				    new OpenLayers.Strategy.Cluster()
				],
				protocol: new OpenLayers.Protocol.HTTP({url: "<?php echo url::base() . 'markers' ?>"}),
				format: new OpenLayers.Format.KML(),
				formatOptions: {
					extractStyles: true,
					extractAttributes: true
				},
				styleMap: new OpenLayers.StyleMap({
					"default": style,
					"select": {
						fillColor: "#8aeeef",
						strokeColor: "#32a8a9"
					}
				})
			});
			// Heatmaps Disabled
			// map.addLayer(heatmap);
		
			
			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
	
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, <?php echo $default_zoom; ?>);
	
			// Category Switch
			$("a[@id^='cat_']").click(function() {
				var catID = this.id.substring(4);
				var catSet = 'cat_' + this.id.substring(4);
				$("a[@id^='cat_']").removeClass("active");
				$("#cat_" + catID).addClass("active");
				$("#currentCat").val(catID);
				markers.setUrl("<?php echo url::base() . 'markers/index/' ?>" + catID);
			});
			
			function onPopupClose(evt) {
	            selectControl.unselect(selectedFeature);
	        }
	        function onFeatureSelect(feature) {
	            selectedFeature = feature;
	            // Since KML is user-generated, do naive protection against
	            // Javascript.
	            var content = "<div class=\"infowindow\"><h2>"+feature.attributes.name + "</h2>" + feature.attributes.description + "</div>";
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
	
	
			//Accessible Slider/Select
			$('select#startDate, select#endDate').accessibleUISlider({
				labels: 6,
				stop: function(e, ui) {
					var startDate = $("#startDate").val();
					var endDate = $("#endDate").val();
					var currentCat = $("#currentCat").val();
					markers.setUrl("<?php echo url::base() . 'markers/index/' ?>" + currentCat + "/" + startDate + "/" + endDate);
					
					// refresh graph
					plotGraph();
				}
			});
			
			// Graph
			var graphData = [<?php echo join($graph_data, ",");?>];
			var graphOptions = {
				xaxis: { mode: "time", timeformat: "%b %y" },
				yaxis: { tickDecimals: 0 }
			};

			function plotGraph() {	
				// TODO: Filter incident count by seleted category
				var startTime = new Date($("#startDate").val().replace("-","/","g"));
				var endTime = new Date($("#endDate").val().replace("-","/","g"));

				plot = $.plot($("#graph"), [graphData],
				        $.extend(true, {}, graphOptions, {
				            xaxis: { min: startTime.getTime(), max: endTime.getTime() }
				        }));
			}
			
			plotGraph();
						
		});
