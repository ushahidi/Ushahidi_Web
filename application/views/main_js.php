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
			
			
			// Create the markers layer and load file
			var markers = new OpenLayers.Layer.Vector("KML", {
				strategies: [
					new OpenLayers.Strategy.Fixed(),
				    new OpenLayers.Strategy.Cluster()
				],
				protocol: new OpenLayers.Protocol.HTTP({url: "<?php echo url::base() . 'markers' ?>"}),
				format: new OpenLayers.Format.KML(),
				styleMap: new OpenLayers.StyleMap({
					"default": style,
					"select": {
						fillColor: "#8aeeef",
						strokeColor: "#32a8a9"
					}
				})
			});
			map.addLayer(markers);
					
			var select = new OpenLayers.Control.SelectFeature(
                markers, {
					hover: true
				}
            );
            map.addControl(select);
            select.activate();
			
			//
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			
			// create a lat/lon object
			var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
	
			// display the map centered on a latitude and longitude (Google zoom levels)
			map.setCenter(myPoint, <?php echo $default_zoom; ?>);

		});