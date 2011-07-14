<?php
/**
 * Common mapping functions
 */
?>

	// Projections
	var proj_4326 = new OpenLayers.Projection('EPSG:4326');
	var proj_900913 = new OpenLayers.Projection('EPSG:900913');
	
	/**
	 * Creates an returns a map object
	 * @param targetElement ID of the element to be used for creating the map
	 * @param options Options to be used for creating the map
	 */
	function createMap(targetElement, lat, lon, zoomLevel, options)
	{
		if (typeof targetElement == 'undefined' || $("#"+targetElement) == null)
		{
			return;
		}
				
		// To hold the map options
		var mapOptions;
		
		if (typeof targetElement == 'undefined')
		{
			// Create the default options
			mapOptions = {
				units: "mi",
				numZoomLevels: 18,
				controls:[],
				projection: proj_900913,
				'displayProjection': proj_4326
			};
		}
		else
		{
			mapOptions = options;
		}
		
		// Create the map object
		var map = new OpenLayers.Map(targetElement, mapOptions);
		
		<?php echo map::layers_js(FALSE); ?>
		
		// Add the default layers
		map.addLayers(<?php echo map::layers_array(FALSE); ?>);
		
		// Add controls
		map.addControl(new OpenLayers.Control.Navigation());
		// map.addControl(new OpenLayers.Control.PanZoomBar());
		map.addControl(new OpenLayers.Control.Attribution());
		map.addControl(new OpenLayers.Control.MousePosition());
		map.addControl(new OpenLayers.Control.LayerSwitcher());
		
		// Check for the zoom level
		var zoom = (typeof zoomLevel == 'undefined' || zoomLevel < 1)? 9 : zoomLevel;
		
		// Create a lat/lon object and center the map
		var myPoint = new OpenLayers.LonLat(lon, lat);
		myPoint.transform(proj_4326, proj_900913);
		
		// Display the map centered on a latitude and longitude
		map.setCenter(myPoint, zoom);
		
		// Return
		return map;
	}
		
	/**
	 * Creates a radius layer and adds it on the map object
	 */
	function addRadiusLayer(map, lat, lon, radius)
	{
		if (typeof map == 'undefined' || typeof lat == 'undefined' || typeof lon == 'undefined')
		{
			return;
		}
		
		if (typeof radius == 'undefined' || radius > 0)
		{
			// Set the radius to a default value
			radius = 20000;
		}
		
		// Create the Circle/Radius layer
		var radiusLayer = new OpenLayers.Layer.Vector("Radius Layer");
				
		// Create the markers layer
		markers = new OpenLayers.Layer.Markers("Markers");
		map.addLayers([radiusLayer, markers]);
		
		// Create a marker positioned at the map center
		var myPoint = new OpenLayers.LonLat(lon, lat);
		
		myPoint.transform(proj_4326, proj_900913);
		var marker = new OpenLayers.Marker(myPoint);
		
		markers.addMarker(marker);
		
		return radiusLayer;
	}
	
	/**
	 * Draw circle around point
	 */
	function drawCircle(map, lat, lon, radius)
	{
		if (typeof map == 'undefined' || typeof map != 'object') return;
		if (typeof radius == 'undefined')
		{
			radius = 20000;
		}
		
		var radiusLayer;
		radiusLayers = map.getLayersByName("Radius Layer");
		for (var i=0; i<radiusLayers.length; i++)
		{
			if (radiusLayers[i].name == "Radius Layer")
			{
				radiusLayer = radiusLayers[i];
				break;
			}
		}
		
		radiusLayer.destroyFeatures();
		
		var circOrigin = new OpenLayers.Geometry.Point(lon, lat);
		circOrigin.transform(proj_4326, proj_900913);
		
		var circStyle = OpenLayers.Util.extend( {},OpenLayers.Feature.Vector.style["default"] );
		var circleFeature = new OpenLayers.Feature.Vector(
			OpenLayers.Geometry.Polygon.createRegularPolygon( circOrigin, radius, 40, 0 ),
			null,
			circStyle
		);
		
		radiusLayer.addFeatures( [circleFeature] );
	}