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
		
		if (typeof(options) == 'undefined')
		{
			// Create the default options
			mapOptions = {
				units: "mi",
				numZoomLevels: 18,
				theme: false,
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
	
	function addFeatureSelectionEvents(map, layer) {
		var selectedFeature = null;
		featureSelect = function(event) {
			selectedFeature = event.feature;
			zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
			lon = zoom_point.lon;
			lat = zoom_point.lat;
			
			var thumb = "";
			if ( typeof(event.feature.attributes.thumb) != 'undefined' && 
				event.feature.attributes.thumb != '')
			{
				thumb = "<div class=\"infowindow_image\"><a href='"+event.feature.attributes.link+"'>";
				thumb += "<img src=\""+event.feature.attributes.thumb+"\" height=\"59\" width=\"89\" /></a></div>";
			}

			var content = "<div class=\"infowindow\">" + thumb;
			content += "<div class=\"infowindow_content\"><div class=\"infowindow_list\">"+event.feature.attributes.name+"</div>";
			content += "\n<div class=\"infowindow_meta\">";
			if ( typeof(event.feature.attributes.link) != 'undefined' &&
				event.feature.attributes.link != '')
			{
				content += "<a href='"+event.feature.attributes.link+"'><?php echo Kohana::lang('ui_main.more_information');?></a><br/>";
			}
			
			content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",1)'>";
			content += "<?php echo Kohana::lang('ui_main.zoom_in');?></a>";
			content += "&nbsp;&nbsp;|&nbsp;&nbsp;";
			content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",-1)'>";
			content += "<?php echo Kohana::lang('ui_main.zoom_out');?></a></div>";
			content += "</div><div style=\"clear:both;\"></div></div>";		

			if (content.search("<?php echo '<'; ?>script") != -1)
			{
				content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/<?php echo '<'; ?>/g, "&lt;");
			}
            
			// Destroy existing popups before opening a new one
			if (event.feature.popup != null)
			{
				map.removePopup(event.feature.popup);
			}
			
			popup = new OpenLayers.Popup.FramedCloud("chicken", 
				event.feature.geometry.getBounds().getCenterLonLat(),
				new OpenLayers.Size(100,100),
				content,
				null, true, onPopupClose);

			event.feature.popup = popup;
			map.addPopup(popup);
		};
		
		
		featureUnselect = function(event) {
			// Safety check
			if (event.feature.popup != null)
			{
				map.removePopup(event.feature.popup);
				event.feature.popup.destroy();
				event.feature.popup = null;
			}
		}
		
		onPopupClose = function(event){
			selectControl.unselect(selectedFeature);
			selectedFeature = null;
		};
		
		selectControl = new OpenLayers.Control.SelectFeature(layer);
		map.addControl(selectControl);
		selectControl.activate();
		layer.events.on({
			"featureselected": featureSelect,
			"featureunselected": featureUnselect
		});
		
	}