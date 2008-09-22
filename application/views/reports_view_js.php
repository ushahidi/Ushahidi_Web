/*
		* View Reports Javascript
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
			var markers = new OpenLayers.Layer.GML("KML", "<?php echo url::base() . 'markers/single/' . $incident_id; ?>", 
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

			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
	
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, <?php echo $default_zoom; ?>);
			
			
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
		});