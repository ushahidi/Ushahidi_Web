/*
		* Main Javascript
		* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		* TESTING THE CLUSTER STRATEGY
		* REPLACE main_js.php with this one to use
		* Clustering (heat mapping)
		* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		*/

		// Map JS
		jQuery(function() {
			var map_layer;
			var markers;
			var catID = '';
			
			// Now initialise the map
			var options = {units: "dd",numZoomLevels: 16,controls:[]};
			var map = new OpenLayers.Map('map', options);
			// map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
			var default_map = <?php echo $default_map; ?>;
			if (default_map == 2)
			{
				map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth");
			}
			else if (default_map == 3)
			{
				map_layer = new OpenLayers.Layer.Yahoo("yahoo");
			}
			else if (default_map == 4)
			{
				map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap");
			}
			else
			{
				map_layer = new OpenLayers.Layer.Google("google");
			}
	
			map.addLayer(map_layer);
	
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.Attribution());
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			
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
						//document.write(print_r(feature.cluster) + " || <BR><BR>");
						return (Math.min(feature.attributes.count, 7) + 3) * 1.6;
					},
					color: function(feature)
					{
						// document.write(print_r(feature.cluster) + " ||| ");
						if (feature.cluster.length < 2)
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
			
			
			
			// Create the markers layer
			function addMarkers(catID){
				if (markers){
					for (var i = 0; i < markers.length; i++) {
						markers[i].destroy();
						markers[i] = null;
					}
					map.removeLayer(markers);
				}
				
				if (catID != ''){
					catID = '/?c=' + catID;
				}
				markers = new OpenLayers.Layer.Vector("Reports", {
					strategies: [
						new OpenLayers.Strategy.Fixed(),
					    new OpenLayers.Strategy.Cluster({
							distance: 15
						})
					],
					protocol: new OpenLayers.Protocol.HTTP({
	                    url: "<?php echo url::base() . 'json' ?>" + catID,
	                    format: new OpenLayers.Format.GeoJSON()
	                }),
					projection: new OpenLayers.Projection("EPSG:4326"),
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
					// "beforefeaturesadded": onMapStartLoad,
					// "featuresadded": onMapEndLoad,
					"featureselected": onFeatureSelect,
					"featureunselected": onFeatureUnselect
				});
				
				// create a lat/lon object
				var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);

				// display the map centered on a latitude and longitude (Google zoom levels)
				map.setCenter(myPoint, <?php echo $default_zoom; ?>);
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
				// markers.setUrl("<?php echo url::base() . 'json/?c=' ?>" + catID);
				addMarkers(catID);
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
					var currentCat = $("#currentCat").val();
					
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
					markers.refresh();
					
					// refresh graph
					plotGraph();
				}
			}).hide();
		
			// Graph
			var allGraphData = [<?php echo $all_graphs ?>];
			var graphData = allGraphData[0]['ALL'];
			var graphOptions = {
				xaxis: { mode: "time", timeformat: "%b %y" },
				yaxis: { tickDecimals: 0 },
				points: { show: true},
				lines: { show: true}
			};

			function plotGraph() {	
				var startTime = new Date($("#startDate").val() * 1000);
				var endTime = new Date($("#endDate").val() * 1000);

				plot = $.plot($("#graph"), [graphData],
				        $.extend(true, {}, graphOptions, {
				            xaxis: { min: startTime.getTime(), max: endTime.getTime() }
				        }));
			}
			
			plotGraph();
			var categoryIds = [0,<?php echo join(array_keys($categories), ","); ?>];
				
			for (var i=0; i<categoryIds.length; i++) {
				$('#cat_'+categoryIds[i]).click(function(){
					var categories = <?php echo json_encode($categories); ?>;
					categories['0'] = ["ALL", "#0099CC"];
					graphData = allGraphData[0][categories[this.id.split("_")[1]][0]];
					plotGraph();
				});
			}
		});
		
		print_r = function(a, dTab) {
		//initiate the return variable
		var ret = "";

		//the depth tabbing variable helps in indentation
		if(!dTab) dTab = "\t";

		//If the input variable is a collection object then iterate
		if(typeof(a) == 'object'){

		//foreach implementation in javascript
		for(var sub in a) {
		var val = a[sub];
		ret += "'" + sub + "' =>";

		//incase the value obtained is again a collection
		if(typeof(val) == 'object') {

		//drill it down by calling the print_r function recurrsively
		ret += "\n" + dTab + "[" + print_r(val, dTab + "\t") + "]\n" + (dTab.substring(0, (dTab.length-1)));
		} else {
		ret += " \"" + val + "\"";
		}
		}
		} else {
		//Not a collection
		ret = "'" + a + "' is of type '" + typeof(a) + "', not array/object.";
		}
		return ret;
		}
