/**
 * Copyright (c) 2008-2012 by Ushahidi Dev Team
 * Published under the LGPL license. See License.txt for the
 * full text of the license
 *
 * @requires media/js/OpenLayers.js
 */
(function(){
	
	/**
	 * Namespace: Ushahidi
	 * The Ushahidi object provides a namespace for all things Ushahidi
	 */
	 window.Ushahidi = {

	 	/**
	 	 * APIProperty: proj_4326
	 	 * Projection for representing latitude and longitude coordinates
	 	 */
	 	proj_4326: new OpenLayers.Projection('EPSG:4326'),

	 	/**
	 	 * APIProperty: proj_900913
	 	 * An unofficial code but maintained nonetheless - for describing
	 	 * coordinates in meters in x/y
	 	 */
	 	proj_900913: new OpenLayers.Projection('EPSG:900913'),

	 	/**
	 	 * APIProperty: markerOpacity
	 	 * Default opacity for the markers
	 	 */
	 	markerOpacity: 0.9,

	 	/**
	 	 * APIProperty: markerRadius
	 	 * Default radius for the markers
	 	 */
	 	markerRadius: 5,

	 	/**
	 	 * APIProperty: markerStrokeWidth
	 	 * Stroke width for the markers
	 	 */
	 	markerStrokeWidth: 2,

	 	/**
	 	 * APIProperty: markerStrokeOpacity
	 	 */
	 	markerStrokeOpacity: 0.9,

		// Layer types
		GEOJSON: "GeoJSON",

		KML: "KML",

		DEFAULT: "default",

		/**
		 * APIProperty: baseURL
		 * Base URL for the application
		 */
		baseURL: '',

	 	/**
	 	 * APIProperty: geoJSONStyle
	 	 * Default styling for GeoJSON data
	 	 * the map
	 	 */
	 	 GeoJSONStyle: function() {
	 	 	var style = new OpenLayers.Style({
	 	 		'externalGraphic': "${icon}",
	 	 		'graphicTitle': "${cluster_count}",
				pointRadius: "${radius}",
				fillColor: "${color}",
				fillOpacity: "${opacity}",
				strokeColor: "${strokeColor}",
				strokeWidth: "${strokeWidth}",
				strokeOpacity: "${strokeOpacity}",
				label:"${clusterCount}",
				fontWeight: "${fontweight}",
				fontColor: "#ffffff",
				fontSize: "${fontsize}"
			},
			{
				context: {
					count: function(feature) {
						if (feature.attributes.count < 2) {
							return 2 * Ushahidi.markerRadius;
						}  else if (feature.attributes.count == 2) {
							return (Math.min(feature.attributes.count, 7) + 1) *
							(Ushahidi.markerRadius * 0.8);
						} else {
							return (Math.min(feature.attributes.count, 7) + 1) *
							(Ushahidi.markerRadius * 0.6);
						}
					},
					fontsize: function(feature) {
						feature_icon = feature.attributes.icon;
						if (feature_icon !== "") {
							return "9px";
						}
						else {
							feature_count = feature.attributes.count;
							if (feature_count > 1000) {
								return "20px";
							} else if (feature_count > 500) {
								return "18px";
							} else if (feature_count > 100) {
								return "14px";
							} else if (feature_count > 10) {
								return "12px";
							} else if (feature_count >= 2) {
								return "10px";
							} else {
								return "";
							}
						}
					},
					fontweight: function(feature) {
						feature_icon = feature.attributes.icon;
						if (feature_icon!=="") {
							return "normal";
						} else {
							return "bold";
						}
					},
					radius: function(feature) {
						if (typeof(feature.attributes.radius) != 'undefined' && 
							feature.attributes.radius != '') {
							return feature.attributes.radius;
						} else {
							feature_count = feature.attributes.count;
							if (feature_count > 10000)
							{
								return Ushahidi.markerRadius * 17;
							}
							else if (feature_count > 5000)
							{
								return Ushahidi.markerRadius * 10;
							}
							else if (feature_count > 1000)
							{
								return Ushahidi.markerRadius * 8;
							}
							else if (feature_count > 500)
							{
								return Ushahidi.markerRadius * 7;
							}
							else if (feature_count > 100)
							{
								return Ushahidi.markerRadius * 6;
							}
							else if (feature_count > 10)
							{
								return Ushahidi.markerRadius * 5;
							}
							else if (feature_count >= 2)
							{
								return Ushahidi.markerRadius * 3;
							}
							else
							{
								return Ushahidi.markerRadius * 2;
							}
						}
					},
					strokeWidth: function(feature) {
						if ( typeof(feature.attributes.strokewidth) != 'undefined' && 
							feature.attributes.strokewidth != '')
						{
							return feature.attributes.strokewidth;
						}
						else
						{
							feature_count = feature.attributes.count;
							if (feature_count > 10000)
							{
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
						}
					},
					color: function(feature) {
						return "#" + feature.attributes.color;
					},
					strokeColor: function(feature) {
						if ( typeof(feature.attributes.strokecolor) != 'undefined' && 
							feature.attributes.strokecolor != '')
						{
							return "#"+feature.attributes.strokecolor;
						}
						else
						{
							return "#"+feature.attributes.color;
						}
					},
					icon: function(feature) {
						feature_icon = feature.attributes.icon;
						
						return (feature_icon !== "") ? feature_icon : "";
					},
					clusterCount: function(feature) {
						if (feature.attributes.count > 1)
						{
							if ($.browser.msie && $.browser.version=="6.0")
							{ // IE6 Bug with Labels
								return "";
							}

							return feature.attributes.count;
						}
						else
						{
							return "";
						}
					},
					opacity: function(feature) {
						feature_icon = feature.attributes.icon;
						if (typeof(feature.attributes.opacity) != 'undefined' && 
							feature.attributes.opacity != '')
						{
							return feature.attributes.opacity
						}
						else if (feature_icon!=="")
						{
							return "1";
						}
						else
						{
							return Ushahidi.markerOpacity;
						}
					},
					strokeOpacity: function(feature) {
						if(typeof(feature.attributes.strokeopacity) != 'undefined' && 
							feature.attributes.strokeopacity != '')
						{
							return feature.attributes.strokeopacity;
						}
						else
						{
							return Ushahidi.markerStrokeOpacity;
						}
					},
					labelalign: function(feature) {
						return "c";
					}
				}
			});

			return style;
		}

	 };

	 /**
	  * Constructor: Ushahidi.Map
	  * Base class used to construct all OpenLayers.Map objects
	  *
	  * Parameters:
	  * div - {DOMElement|String} The element or id of an element in the page
	  *       that will contain the map
	  * config = {Object} Optional object with the configuration to be used for
	  *          generating the map
	  *
	  * Valid config options are:
	  * zoom - Initial zoom level of the map
	  * center - {Object} The default initial center of the map
	  * redrawOnZoom - {Boolean} Whether to redraw the layers when the map zoom changes.
	  *                Default is false
	  * detectMapZoom - {Boolean} Whether to detect change in the zoom level. If the
	  *                 redrawOnZoom property is true, this option is ignored
	  * mapControls - {Array(OpenLayers.Control)} The list of controls to add to the map
	  */
	 Ushahidi.Map = function(div, config) {
	 	// Internal registry for the marker layers
	 	this._registry = [];
	 	
	 	// Internal list of layers to keep at the top
	 	this._onTop = [];

	 	// Markers are not yet loaded on the map
	 	this._isLoaded = 0;

	 	// Timeouts for layers about to be loaded, keyed by layer name
	 	this._addLayerTimeouts = {};

	 	// List of supported events
	 	this._EVENTS = [
	 		// When the report filters change
	 	    "filterschanged",

	 	    // Map's zoom level changes
	 	    "zoomchanged",

	 	    // When the size of the map's viewport changes
	 	    "resize",

	 	    // When a layer is deleted
	 	    "deletelayer",

	 	    // Maker position changed
	 	    "markerpositionchanged",

	 	    // Base layer is changed
	 	    "baselayerchanged",

	 	    // Map center changed
	 	    "mapcenterchanged"
	 	];

	 	// The set of filters/parameters to pass to the URL that fetches
	 	// overlay data - not applicable to KMLs
	 	this._reportFilters = {};

	 	// Register for the callbacks to be invoked when the report filters
	 	// are updated. The updated paramters will passed to the callback
	 	// as a parameter
	 	this._callbacks = {};

	 	// Tracks the current marker position
	 	this._currentMarkerPosition = {};

	 	// Check for the mapDiv
	 	if (div == undefined) {
	 		throw "The element or id of an element that will contain the map must be specified";
	 	}

	 	// Sanitize input parameters
	 	var config = config || {};
	 	if (config.zoom == undefined) config.zoom = 8;

	 	// Internally track the current zoom
	 	this.currentZoom = config.zoom;

	 	// Update the report filters with the zoom level
	 	this._reportFilters.z = config.zoom;

	 	// Latitude and longitude
	 	if (config.center == undefined) {
	 		// Set the default latitude and longitude to Nairobi - no place like ground Ø
	 		config.center = {latitude: -1.286449478924, longitude: 36.822838050049};
		}

		// Map options
		var mapOptions = {
			units: "dd",
			numZoomLevels: 18,
			theme: false,
			controls: [],
			projection: Ushahidi.proj_900913,
			'displayProjection': Ushahidi.proj_4326,
			maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 
			                                 20037508.34, 20037508.34),
			maxResolution: 156543.0339,
			// Shrink the popup padding so popups don't land under zoom control
			paddingForPopups: new OpenLayers.Bounds(40,15,15,15),
			eventListeners: { 
				// Trigger keepOnTop fn whenever new layers are added
				addlayer: this.keepOnTop,
				scope: this
			}
		};

		// Are the layers to be redrawn on zoom change
		if (config.redrawOnZoom !== undefined && config.redrawOnZoom) {
			mapOptions.eventListeners.zoomend = this.refresh;
		}

		// Zoom detection is enabled and redrawOnZoom has not been specified or is false
		if ((config.redrawOnZoom == undefined || !config.redrawOnZoom) && config.detectMapZoom) {
			this.register("zoomchanged", this.updateZoom, this);
			mapOptions.eventListeners.zoomend = this.triggerZoomChanged;
		}

		// Check for the controls to add to the map
		if (config.mapControls == undefined) {
			// Default map controls
			mapOptions.controls = [
				new OpenLayers.Control.Navigation({ dragPanOptions: { enableKinetic: true } }),
				new OpenLayers.Control.Zoom(),
				new OpenLayers.Control.Attribution(),
				new OpenLayers.Control.MousePosition(),
				new OpenLayers.Control.LayerSwitcher()
			];
		} else {
			mapOptions.controls = config.mapControls;
		}

		// Create the map
		this._olMap = new OpenLayers.Map(div, mapOptions);
		if (config.baseLayers != undefined && config.baseLayers.length > 0) {
			this._olMap.addLayers(config.baseLayers);
		}

		var point = new OpenLayers.LonLat(config.center.longitude, config.center.latitude);
		point.transform(Ushahidi.proj_4326, Ushahidi.proj_900913);

		this._currentCenter = point;

		// Set the map center
		this._olMap.setCenter(point, config.zoom);

		// Display the map projection
		if (config.displayProjection != undefined && config.displayProjection) {
			document.getElementById('mapProjection').innerHTML = this._olMap.projection;
		}

		// Register this instance in the namespace
		// NOTE: Only one map is allowed in the namespace
		Ushahidi._currentMap = this;

		// Register events
		this.register("resize", this.resize, this);
		this.register("deletelayer", this.deleteLayer, this);
		this.register("baselayerchanged", this.updateBaseLayer, this);
		this.register("mapcenterchanged", this.updateMapCenter, this);
		
		// Pre-load the background image for the popup so that it is 
		// fetched from the cache when when popup is displayed
		var popupBgImage = new Image();
		popupBgImage.src = OpenLayers.Util.getImagesLocation() + 'cloud-popup-relative.png';

		return this;
	};

	/**
	 * APIMethod addLayers
	 *
	 * Parameters:
	 * layerType - {String} Type of marker to be added and could be one of the following 
	 *             (Ushahidi.REPORTS, Ushahidi.KML, Ushahidi.SHARES. Ushahidi.DEFAULT)
	 * options   - {Object} Optional object key/value pairs of the markers to be added to the map
	 *
	 * Valid options are:
	 * name - {String} Name of the Layer being added
	 * url  - {String} Fetch URL for the layer data
	 * styleMap - {OpenLayers.StyleMap} Styling for the layer
	 * detectMapClicks - {Boolean} - When true, registers a callback function to detect
	 *               click events on the map. This option is only used with the default
	 *               layer (Ushahidi.DEFAULT). The default value is true
	 * transform - {Boolean} When true, transforms the featur geometry to spherical mercator
	 *             The default value is false
	 * features - {Array(OpenLayers.Feature.Vector)} Features to add to the layer 
	 *            When the features ar specified, the protocol property is omitted from the
	 *            options passed to the layer constructor. The features property is used instead
	 *
	 * save -      {bool} Whether to save the layer in the internal registry of Ushahidi.Map This
	 *             parameter should be set to true, if the layer being added is new so as to ensure
	 *             that it is redrawn when the map is zoomed in/out or the report filters are updated   
	 * keepOnTop - {bool} Whether to keep this layer above others.
	 */
	Ushahidi.Map.prototype.addLayer = function(layerType, options, save, keepOnTop) {
		// Default markers layer
		if (layerType == Ushahidi.DEFAULT) {
			this.deleteLayer("default");
			
			var markers = null;
			
			if (options == undefined) {
				options = {};
			}
			
			// Check for the style map
			if (options.styleMap != undefined) {

				// Create vector layer
				markers = new OpenLayers.Layer.Vector("default", {
					styleMap: styleMap
				});

				// Add features to the vector layer
				makers.addFeatures(new OpenLayers.Feature.Vector(this._olMap.getCenter()));

			} else {
				markers = new OpenLayers.Layer.Markers("default");
				markers.addMarker(new OpenLayers.Marker(this._olMap.getCenter()));
			}

			// Add the layer to the map
			this._olMap.addLayer(markers);

			// Is map-click detection enabled?
			if (options.detectMapClicks == undefined || options.detectMapClicks) {
				var context = this;
				context._olMap.events.register("click", context._olMap, function(e){
					var point = context._olMap.getLonLatFromViewPortPx(e.xy);
					markers.clearMarkers();
					markers.addMarker(new OpenLayers.Marker(point));

					point.transform(Ushahidi.proj_900913, Ushahidi.proj_4326);
					
					var coords = {latitude: point.lat, longitude: point.lon};
					context.trigger("markerpositionchanged", coords);

					// Update the current map center
					var newCenter = new OpenLayers.LonLat(coords.longitude, coords.latitude);
					newCenter.transform(Ushahidi.proj_4326, Ushahidi.proj_900913);
					context._currentCenter = newCenter;
				});
			}

			this._isLoaded = 1;

			return this;
		}
		
		// Setup default protocol format
		var protocolFormat = new OpenLayers.Format.GeoJSON();
		// Switch protocol format if layer is KML
		if (layerType == Ushahidi.KML) {
			protocolFormat = new OpenLayers.Format.KML({
				extractStyles: true,
				extractAttributes: true,
				maxDepth: 5
			});
		}

		// No options defined - where layerType !== Ushahidi.DEFAULT
		if (options == undefined) {
			throw "Invalid layer options";
		}

		// Save the layer data in the internal registry
		if (save !== undefined && save) {
			this._registry.push({layerType: layerType, options: options});
		}

		// Save the layer name to keep on top
		if (keepOnTop !== undefined && keepOnTop) {
			this._onTop.push(options.name);
		}

		// Transform feature geometry to Spherical Mercator
		preFeatureInsert = function(feature) {
			if (feature.geometry) {
				var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
				OpenLayers.Projection.transform(point, Ushahidi.proj_4326, Ushahidi.proj_900913);
				feature.geometry.x = point.x;
				feature.geometry.y = point.y;
			}
		}

		// Layer options
		var layerOptions = {
			projection: Ushahidi.proj_4326,
			formatOptions: {
				extractStyles: true,
				extractAttributes: true
			}
		};

		if (options.transform) {
			// Transform the feature geometry to spherical mercator
			layerOptions.preFeatureInsert = preFeatureInsert;
		}

		// Build out the fetch url
		var fetchURL = Ushahidi.baseURL + options.url;

		// Apply the report filters to the layer fetch URL except where
		// the layer type is KML
		if (layerType !== Ushahidi.KML) {
			var params = [];
			for (var _key in this._reportFilters) {
				params.push(_key + '=' + this._reportFilters[_key]);
			}

			// Update the fetch URL with parameters
			fetchURL += (params.length > 0) ? '?' + params.join('&') : '';
			// Get the styling to use
			var styleMap = null;
			if (options.styleMap !== undefined) {
				styleMap = options.styleMap;
			} else {
				var style = Ushahidi.GeoJSONStyle();
				styleMap = new OpenLayers.StyleMap({
					"default": style,
					"select": style
				});
			}

			// Update the layer options with the style map
			layerOptions.styleMap = styleMap;
		}

		// If no features were passed in layer options,
		// set up protocal and strategy to grab GeoJSON
		if (options.features === undefined) {
			layerOptions.strategies = [new OpenLayers.Strategy.Fixed({preload: true})];

			// Set the protocol
			layerOptions.protocol = new OpenLayers.Protocol.HTTP({
				url: fetchURL,
				format: protocolFormat
			});
		}

		// Create the layer
		var layer = new OpenLayers.Layer.Vector(options.name, layerOptions);
		
		// Store context for callbacks
		var context = this;
		
		// Hide the layer until its loaded
		// only delete the old layer on loadend
		layer.display(false);
		// Hack to start with opacity 0 then fade in
		layer.div.style['opacity'] = 0;
		var oldLayer = this._olMap.getLayersByName(options.name);
		var displayLayer = function () {
			// Delete the old layers
			this.deleteLayer(oldLayer);
			// Display the new layer, fading in if we've got CSS3
			layer.display(true);
			layer.div.className += " olVectorLayerDiv";
			layer.div.style['opacity'] = 1;

			// Update / Create SelectFeature Control
			if (typeof this._selectControl == "object")
			{
				// Update SelectFeature control with all vector layers
				this._selectControl.setLayer(this._olMap.getLayersByClass("OpenLayers.Layer.Vector"));
			}
			else
			{
				// Select Feature control
				this._selectControl = new OpenLayers.Control.SelectFeature(
					this._olMap.getLayersByClass("OpenLayers.Layer.Vector"),
					{ clickout: true, toggle: false, multiple: false, hover: false }
				);
				this._olMap.addControl(this._selectControl);
				this._selectControl.activate();
			}
			
			// Bind popup events for select/unselect
			layer.events.on({
				"featureselected": this.onFeatureSelect,
				"featureunselected": this.onFeatureUnselect,
				scope: this
			});
		}
		// Register display layer fn to run on load end
		layer.events.register('loadend', this, displayLayer);
		
		// If features were passed in layer options
		// Add features to layer and register display layer on layer added
		if (options.features !== undefined && options.features.length > 0) {
			layer.addFeatures(options.features);
			layer.events.register('added', this, displayLayer);
		}

		// Add the layer to the map
		// We do this after a delay in case someone zooms multiple times
		clearTimeout(this._addLayerTimeouts[options.name]);
		this._addLayerTimeouts[options.name] = setTimeout(function(){ context._olMap.addLayer(layer); }, 100);

		this._isLoaded = 1;

		return this;
	}

	/**
	 * APIMethod: updateReportFilters
	 * This method updates the set of parameters used to filter out
	 * the map content and then redraws the map
	 *
	 * Parameters:
	 * filters - {Object} Set of filter to apply to the map.
	 * Allowable filters are:
	 *     z - {Number} The zoom level
	 *     c - {Number} The category id
	 *     m - {Number} The media type (0 - All, 1 - Pictures, 2 - Video 4 - News)
	 *     s - {Number} Start date - Earliest date by which to filter the reports
	 *     e - {Number} End date - Latest date by which to filter the reports
	 */
	Ushahidi.Map.prototype.updateReportFilters = function(filters) {
		if (filters == undefined) {
			throw "Missing filters";
		}

		var hasChanged = false;
		var context = this;

		// Overwrite the current set of filters with the new values
		$.each(filters, function(filter, value) {
			var currentValue = context._reportFilters[filter];
			if ((currentValue == undefined && currentValue == null) ||
				currentValue !== value) {
				hasChanged = true;
				context._reportFilters[filter] = value;
			}
		});

		// Have the filters changed
		if (hasChanged) {
			context.redraw();

			setTimeout(function() {
				context.trigger("filterschanged", context._reportFilters);
			}, 800);
		}
	}

	/**
	 * APIMethod: getReportFilters
	 * Gets the set of filters used to filter the reports on the map
	 */
	Ushahidi.Map.prototype.getReportFilters = function() {
		return this._reportFilters;
	}

	/**
	 * APIMethod: refresh
	 *
	 * Deletes the markers and layers from the map and adds them afresh. This
	 * method should be called when zoom level of the map changes
	 */
	Ushahidi.Map.prototype.refresh = function(e) {
		if (this._isLoaded) {
			// Update the zoom and redraw
			this._reportFilters.z = this._olMap.getZoom();
			this.redraw();
		}
		return this;
	}

	/**
	 * APIMethod: redraw
	 *
	 * Redraws the layers on the map using the updated report filters
	 * This method is automatically invoked each time the map is zoomed
	 * or when the report filters are updated
	 */
	Ushahidi.Map.prototype.redraw = function() {
		// Close any open popups
		this.closePopups();

		for (var i=0; i<this._registry.length; i++) {
			var layer = this._registry[i];
			this.addLayer(layer.layerType, layer.options);
		}
		return this;
	}

	/**
	 * APIMethod: onFeatureSelect
	 *
	 * Callback that is executed when a feature that is on the map is
	 * selected. When executed, it displays a popup with the content/information
	 * about the selected feature
	 */
	Ushahidi.Map.prototype.onFeatureSelect = function(event) {
		this._selectedFeature = event.feature;

		zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
		lon = zoom_point.lon;
		lat = zoom_point.lat;

		// Image to display within the popup
		var image = "";
		if (event.feature.attributes.thumb !== undefined && event.feature.attributes.thumb != '') {
			image = "<div class=\"infowindow_image\"><a href='"+event.feature.attributes.link+"'>";
			image += "<img src=\""+event.feature.attributes.thumb+"\" height=\"59\" width=\"89\" /></a></div>";
		} else if (event.feature.attributes.image !== undefined && event.feature.attributes.image != '') {
			image = "<div class=\"infowindow_image\">";
			image += "<a href=\""+event.feature.attributes.link+"\" title=\""+event.feature.attributes.name+"\">";
			image += "<img src=\""+event.feature.attributes.image+"\" />";
			image += "</a>";
			image += "</div>";
		}

		var content = "<div class=\"infowindow\">" + image +
		    "<div class=\"infowindow_content\">"+
		    "<div class=\"infowindow_list\">"+event.feature.attributes.name+"</div>\n" +
		    "<div class=\"infowindow_meta\">";

		if (typeof(event.feature.attributes.link) != 'undefined' &&
		    event.feature.attributes.link != '') {

		    content += "<a href='"+event.feature.attributes.link+"'>" +
			    "More Information</a><br/>";
		}

		content += "<a id=\"zoomIn\">";
		content += "Zoom In</a>";
		content += "&nbsp;&nbsp;|&nbsp;&nbsp;";
		content += "<a id=\"zoomOut\">";
		content += "Zoom Out</a></div>";
		content += "</div><div style=\"clear:both;\"></div></div>";		

		if (content.search("<script") != -1) {
			content = "Content contained Javascript! Escaped content " +
			    "below.<br />" + content.replace(/</g, "&lt;");
		}
		  
		// Destroy existing popups before opening a new one
		if (event.feature.popup != null) {
			map.removePopup(event.feature.popup);
		}

		// Create the popup
		var popup = new OpenLayers.Popup.FramedCloud("chicken", 
			event.feature.geometry.getBounds().getCenterLonLat(),
			new OpenLayers.Size(100,100),
			content,
			null, true, this.onPopupClose);

		event.feature.popup = popup;
		this._olMap.addPopup(popup);
		popup.show();

		// Register zoom in/out events
		$("#zoomIn", popup.contentDiv).click(
			{context: this, latitude: lat, longitude: lon, zoomFactor: 1}, 
			this.zoomToSelectedFeature);

		$("#zoomOut", popup.contentDiv).click(
			{context: this, latitude: lat, longitude: lon, zoomFactor: -1}, 
			this.zoomToSelectedFeature);
	}

	/**
	 * APIMethod: onFeatureUnselect
	 * Callback to be executed when a feature is unselected - when a click
	 * is registered outside the feature's viewport
	 */
	Ushahidi.Map.prototype.onFeatureUnselect = function(e) {
		if (e.feature.popup != null) {
			this._olMap.removePopup(e.feature.popup);
			e.feature.popup.destroy();
			e.feature.popup = null;
		}
	}

	/**
	 * APIMethod: onPopupClose
	 * Callback to be executed when the "close" button in the 
	 * popup is clicked
	 */
	Ushahidi.Map.prototype.onPopupClose = function(e) {
		Ushahidi._currentMap.closePopups();
	}

	/**
	 * APIMethod: closePopups
	 *
	 * Closes all all open popups
	 */
	Ushahidi.Map.prototype.closePopups = function() {
		if (this._selectedFeature !== undefined && this._selectedFeature != null) {
			this._selectControl.unselect(this._selectedFeature);
			this._selectedFeature = null;
		}
	}

	/**
	 * APIMethod: zoomToSelectedFeature
	 * Callback to be executed when zooming in/out of a report
	 */
	Ushahidi.Map.prototype.zoomToSelectedFeature = function(e) {
		// Get the event data
		var data = e.data;
		
		var point = new OpenLayers.LonLat(data.longitude, data.latitude);
		var zoomLevel = data.zoomFactor + data.context._olMap.getZoom();

		// Center and zoom
		data.context._olMap.zoomTo(zoomLevel);
		data.context._olMap.panTo(point);

		// Close any open popups
		data.context.closePopups();

		// Halt further event processing
		return false;
	}

	/**
	 * APIMethod: register
	 * Registers a callback to be invoked when a specified event is triggered - the
	 * report filters are passed to each of the registered callbacks as a parameter.
	 *
	 * Parameters:
	 * eventName - {String} Name of the event for which to register the callback
	 * callback  - {Function} Function to be executed when the event in 'eventName' is triggered
	 * context   - {Object} Execution context of the callback function. This is necessary where
	 *             the callback function is an object method
	 */
	Ushahidi.Map.prototype.register = function(eventName, callback, context) {

		// Is the event known? i.e. in the internal event registry
		if ($.inArray(eventName, this._EVENTS) === -1)
			return;

		// Has the event already been registered
		if (this._callbacks[eventName] == undefined) {
			this._callbacks[eventName] = [];
		}

		// Subscribe to the event
		this._callbacks[eventName].push({
			callback: callback,
			context: context
		});
	}

	/**
	 * APIMethod: trigger
	 * Triggers the event specified specified in the eventName parameter
	 *
	 * Parameters:
	 * eventName - {String} Name of the event to be triggered. No error will be returned
	 *              if the event is not found
	 * data - {Object} Data to be passed to the subscribers of the event specified in eventName
	 */
	Ushahidi.Map.prototype.trigger = function(eventName, data) {
		// If the event is unknown, exit without throwing an error
		if (this._callbacks.length == 0 || this._callbacks[eventName] == undefined) {
			return;
		}

		// Get the subscribers for the event
		var subscribers = this._callbacks[eventName];

		for (var i=0; i < subscribers.length; i++) {
			var subscriber = subscribers[i];
			if (subscriber.context == undefined || subscriber.context == null) {
				subscriber.callback(data);
			} else {
				subscriber.callback.call(subscriber.context, data);
			}
		}
	}

	/**
	 * APIMethod: resize
	 * Resizes the map when the size of its container changes
	 */
	Ushahidi.Map.prototype.resize = function() {
		this._olMap.updateSize();
		this._olMap.pan(0, 1);
	}

	/**
	 * APIMethod: deleteLayer
	 * Deletes a layer from the map
	 *
	 * Parameters:
	 * name - {String} Name of the layer to be deleted
	 */
	Ushahidi.Map.prototype.deleteLayer = function(name) {
		var layers = name;
		if (typeof name === 'string')
			layers = this._olMap.getLayersByName(name);
		
		for (var i=0; i < layers.length; i++) {
			// Set opacity to 0 then hide, using CSS3 transitions to fade the layer out
			layers[i].div.style['opacity'] = 0.2;
			layers[i].display(false);
			// Skip layer if its not on the map
			if (layers[i].map == null || this._olMap.getLayerIndex(layers[i]) == -1) continue;
			
			this._olMap.removeLayer(layers[i]);
			if (layers[i].destroyFeatures !== undefined)
				layers[i].destroyFeatures();
		}
	}

	/**
	 * APIMethod: createRadiusLayer
	 * Creates a radius layer
	 * Parameters:
	 * config - {Object} The properties to be used to create the radius layer
	 */
	Ushahidi.Map.prototype.addRadiusLayer = function(config) {

		// Check if the Layer Switcher control has been added to the map
		// and delete all instances of such
		var layerSwitchers = this._olMap.getControlsByClass("OpenLayers.Control.LayerSwitcher");
		for (var i = 0; i < layerSwitchers.length; i++) {
			this._olMap.removeControl(layerSwitchers[i]);
		}

		this.updateRadius(config);
		var context = this;

		// Detect click events on the map
		this._olMap.events.register("click", context._olMap, function(e) {
			var lonlat = context._olMap.getLonLatFromViewPortPx(e.xy);
			lonlat.transform(Ushahidi.proj_900913, Ushahidi.proj_4326);

			var coords = {latitude: lonlat.lat, longitude: lonlat.lon};
			context.updateRadius(coords);
			context.trigger("markerpositionchanged", coords);
		});
		
	}

	/**
	 * APIMethod: updateRadius
	 * Updates the location and size of the radius layer
	 *
	 * Parameters:
	 * options - {Object} Properties for updating the radius layer
	 *
	 * Valid options are:
	 * latitude - Latitude for the alert center
	 * longitude - Longitude for the alert center
	 * radius - {Number} Alert radius
	 */
	Ushahidi.Map.prototype.updateRadius = function(options) {
		// Get the radius
		this._radius = (options.radius == undefined) ? 20000 : options.radius;

		if (options.latitude !== undefined && options.longitude !== undefined) {
			this._currentMarkerPosition = {latitude: options.latitude, longitude: options.longitude};
		} 

		if (this._currentMarkerPosition.latitude == undefined ||
			this._currentMarkerPosition.longitude == undefined) {
			throw "Missing latitude and longitude in the options";
		}

		// Update the options with latitude and longitude
		var latitude = this._currentMarkerPosition.latitude;
		var longitude = this._currentMarkerPosition.longitude;

		// Delete the layers and re-add them
		this.deleteLayer("radius");
		this.deleteLayer("radiusmarker");

		// Get the current map center
		var point  = new OpenLayers.LonLat(longitude, latitude);
		point.transform(Ushahidi.proj_4326, Ushahidi.proj_900913);

		// Move the map to the new center
		this._olMap.moveTo(point);

		var circleOrigin = new OpenLayers.Geometry.Point(longitude, latitude);
		circleOrigin.transform(Ushahidi.proj_4326, Ushahidi.proj_900913);

		// Radius feature
		var radiusFeature = new OpenLayers.Feature.Vector(
			OpenLayers.Geometry.Polygon.createRegularPolygon(circleOrigin, this._radius, 40, 0),
			null,
			OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style["default"])
		);

		var radiusLayer = new OpenLayers.Layer.Vector("radius");
		radiusLayer.addFeatures([radiusFeature]);

		// Create marker to be used for adjusting the center
		// point for the radius
		var markers = new OpenLayers.Layer.Markers("radiusmarker");
		markers.addMarker(new OpenLayers.Marker(point));

		this._olMap.addLayers([radiusLayer, markers]);

	}

	/**
	 * APIMethod: updateBaseLayer
	 * Changes the basealyer of the map
	 * Parameters:
	 * layerName - {String} Name of the new base layer
	 */
	Ushahidi.Map.prototype.updateBaseLayer = function(layerName) {
		var layers =  this._olMap.getLayersByName(layerName);
		if (layers.length < 1)
			throw "Layer " + layerName + " not found";

		layers[0].setVisibility(true);
		this._olMap.setBaseLayer(layers[0]);
	}

	/**
	 * APIMethod: updateZoom
	 * Updates the zoom level of the map
	 * Parameters:
	 * zoom - {Number} The zoom level to adjust to
	 */
	Ushahidi.Map.prototype.updateZoom = function(zoom) {
		if (this.currentZoom !== zoom) {
			this.currentZoom = zoom;
			this._olMap.setCenter(this._currentCenter, zoom);
		}
	}

	/**
	 * APIMethod: updateMapCenter
	 * Updates the center of the map
	 */
	Ushahidi.Map.prototype.updateMapCenter = function(center) {
		var point = new OpenLayers.LonLat(center.longitude, center.latitude);
		point.transform(Ushahidi.proj_4326, Ushahidi.proj_900913);
		this._olMap.panTo(point);

		// Re-add the default layer
		this.addLayer(Ushahidi.DEFAULT);

		// Map center has changed, trigger 
		this.trigger("markerpositionchanged", center);
	}

	/**
	 * APIMethod: triggerZoomChanged
	 * Callback that triggers the "zoomchanged" event. This method
	 * is called by OpenLayers when the map zoom changes and the
	 * Ushahidi.Map object was initialized with the detectMapZoom
	 * option set to TRUE
	 */
	Ushahidi.Map.prototype.triggerZoomChanged = function(e) {
		if (this._isLoaded) {
			this.trigger("zoomchanged", this._olMap.getZoom());

			// EK <emmanuel(at)ushahidi.com>
			// Dirty hack to add back the default layer
			// The assumption here is that this method is only called
			// when we're using the default layer
			this.addLayer(Ushahidi.DEFAULT);
		}
	}
	
	/**
	 * APIMethod: keepOnTop
	 * Forces specified layer(s) to the top of the stack
	 */
	Ushahidi.Map.prototype.keepOnTop = function(layer) {
		for (var i=0; i<this._onTop.length; i++) {
			var layerName = this._onTop[i];
			var layers = this._olMap.getLayersByName(layerName);
			
			for (var j=0; j<layers.length; j++) {
				this._olMap.raiseLayer(layers[j], this._olMap.getNumLayers());
			}
		}
	}

})();
