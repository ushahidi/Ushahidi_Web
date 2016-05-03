/*
MAPSTRACTION   vtimemap   http://www.mapstraction.com

Copyright (c) 2011 Tom Carden, Steve Coast, Mikel Maron, Andrew Turner, Henri Bergius, Rob Moran, Derek Fowler, Gary Gale
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

 * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * Neither the name of the Mapstraction nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
mxn.register('microsoft', {	

Mapstraction: {
	
	init: function(element, api) {
		var me = this;
		if (!VEMap) {
			throw api + ' map script not imported';
		}
		
		this.maps[api] = new VEMap(element.id);
		this.maps[api].AttachEvent('onclick', function(event){
			me.clickHandler();
			var map = me.maps[me.api];
			var shape = map.GetShapeByID(event.elementID);
			if (shape) {
				if (shape.mapstraction_marker) {
					shape.mapstraction_marker.click.fire();
				}
				if (shape.mapstraction_polyline) {
					shape.mapstraction_polyline.click.fire();
				}
			} 
			else {
				var x = event.mapX;
				var y = event.mapY;
				var pixel = new VEPixel(x, y);
				var ll = map.PixelToLatLong(pixel);
				me.click.fire({'location': new mxn.LatLonPoint(ll.Latitude, ll.Longitude)});
			}
		});
		this.maps[api].AttachEvent('onendzoom', function(event){
			me.moveendHandler(me);
			me.changeZoom.fire();
		});
		this.maps[api].AttachEvent('onendpan', function(event){
			me.moveendHandler(me);
			me.endPan.fire();
		});
		this.maps[api].AttachEvent('onchangeview', function(event){
			me.endPan.fire();
		});
		this.maps[api].LoadMap();
		document.getElementById("MSVE_obliqueNotification").style.visibility = "hidden"; 
	
		//removes the bird's eye pop-up
		this.loaded[api] = true;
		me.load.fire();	
	},
	
	applyOptions: function(){
		var map = this.maps[this.api];
		if(this.options.enableScrollWheelZoom){
			map.enableContinuousZoom();
			map.enableScrollWheelZoom();
		}		
	},

	resizeTo: function(width, height){	
		this.maps[this.api].Resize(width, height);
	},

	addControls: function( args ) {
		var map = this.maps[this.api];
		
		if (args.pan) {
			map.SetDashboardSize(VEDashboardSize.Normal);
		}
		else {
			map.SetDashboardSize(VEDashboardSize.Tiny);
		}

	  	if (args.zoom == 'large') {
			map.SetDashboardSize(VEDashboardSize.Small);
		}
		else if ( args.zoom == 'small' ) {
			map.SetDashboardSize(VEDashboardSize.Tiny);
		}
		else {
			map.HideDashboard();
			map.HideScalebar();
		}
	},

	addSmallControls: function() {
		var map = this.maps[this.api];
		map.SetDashboardSize(VEDashboardSize.Tiny);
	},

	addLargeControls: function() {
		var map = this.maps[this.api];
		map.SetDashboardSize(VEDashboardSize.Normal);
		this.addControlsArgs.pan = true;
		this.addControlsArgs.zoom = 'large';
	},

	addMapTypeControls: function() {
		var map = this.maps[this.api];
		map.addTypeControl();
	
	},

	dragging: function(on) {
		var map = this.maps[this.api];
		if (on) {
			map.enableDragMap();
		}
		else {
			map.disableDragMap();
		}
	},

	setCenterAndZoom: function(point, zoom) { 
		var map = this.maps[this.api];
		var pt = point.toProprietary(this.api);
		var vzoom =  zoom;
		map.SetCenterAndZoom(new VELatLong(point.lat,point.lon), vzoom);
	},
	
	addMarker: function(marker, old) {
		var map = this.maps[this.api];
		marker.pinID = "mspin-"+new Date().getTime()+'-'+(Math.floor(Math.random()*Math.pow(2,16)));
		var pin = marker.toProprietary(this.api);
		
		map.AddShape(pin);
		//give onclick event
		//give on double click event
		//give on close window
		//return the marker
				
		return pin;
	},

	removeMarker: function(marker) {
		var map = this.maps[this.api];
		var id = marker.proprietary_marker.GetID();
		var microsoftShape = map.GetShapeByID(id);
		map.DeleteShape(microsoftShape);
	},
	
	declutterMarkers: function(opts) {
		var map = this.maps[this.api];
		
		// TODO: Add provider code
	},

	addPolyline: function(polyline, old) {
		var map = this.maps[this.api];
		var pl = polyline.toProprietary(this.api);
		pl.HideIcon();//hide the icon VE automatically displays
		map.AddShape(pl);
		// TODO: Add click event
		return pl;
	},

	removePolyline: function(polyline) {
		var map = this.maps[this.api];
		var id = polyline.proprietary_polyline.GetID();
		var microsoftShape = map.GetShapeByID(id);
		map.DeleteShape(microsoftShape);
	},
	
	getCenter: function() {
		var map = this.maps[this.api];
		var LL = map.GetCenter();
		var point = new mxn.LatLonPoint(LL.Latitude, LL.Longitude);
		return point;
	},
 
	setCenter: function(point, options) {
		var map = this.maps[this.api];
		var pt = point.toProprietary(this.api);
		map.SetCenter(new VELatLong(point.lat, point.lon));
	},

	setZoom: function(zoom) {
		var map = this.maps[this.api];
		map.SetZoomLevel(zoom);
	},
	
	getZoom: function() {
		var map = this.maps[this.api];
		var zoom = map.GetZoomLevel();
		return zoom;
	},

	getZoomLevelForBoundingBox: function( bbox ) {
		var map = this.maps[this.api];
		// NE and SW points from the bounding box.
		var ne = bbox.getNorthEast();
		var sw = bbox.getSouthWest();
		var zoom;
		
		// TODO: Add provider code
		
		return zoom;
	},

	setMapType: function(type) {
		var map = this.maps[this.api];
		switch(type) {
			case mxn.Mapstraction.ROAD:
				map.SetMapStyle(VEMapStyle.Road);
				break;
			case mxn.Mapstraction.SATELLITE:
				map.SetMapStyle(VEMapStyle.Aerial);
				break;
			case mxn.Mapstraction.HYBRID:
				map.SetMapStyle(VEMapStyle.Hybrid);
				break;
			default:
				map.SetMapStyle(VEMapStyle.Road);
		}	 
	},

	getMapType: function() {
		var map = this.maps[this.api];
		var mode = map.GetMapStyle();
		switch(mode){
			case VEMapStyle.Aerial:
				return mxn.Mapstraction.SATELLITE;
			case VEMapStyle.Road:
				return mxn.Mapstraction.ROAD;
			case VEMapStyle.Hybrid:
				return mxn.Mapstraction.HYBRID;
			default:
				return null;
		}
	},

	getBounds: function () {
		var map = this.maps[this.api];
		view = map.GetMapView();
		var topleft = view.TopLeftLatLong;
		var bottomright = view.BottomRightLatLong;
		
		return new mxn.BoundingBox(bottomright.Latitude,topleft.Longitude,topleft.Latitude, bottomright.Longitude );
	},

	setBounds: function(bounds){
		var map = this.maps[this.api];
		var sw = bounds.getSouthWest();
		var ne = bounds.getNorthEast();
		
		var rec = new VELatLongRectangle(new VELatLong(ne.lat, ne.lon), new VELatLong(sw.lat, sw.lon));
		map.SetMapView(rec);
	},

	addImageOverlay: function(id, src, opacity, west, south, east, north, oContext) {
		var map = this.maps[this.api];
		
		// TODO: Add provider code
	},

	setImagePosition: function(id, oContext) {
		var map = this.maps[this.api];
		var topLeftPoint; var bottomRightPoint;

		// TODO: Add provider code

		//	oContext.pixels.top = ...;
		//	oContext.pixels.left = ...;
		//	oContext.pixels.bottom = ...;
		//	oContext.pixels.right = ...;
	},
	
	addOverlay: function(url, autoCenterAndZoom) {
		var map = this.maps[this.api];
		var layer = new VEShapeLayer(); 
		var mlayerspec = new VEShapeSourceSpecification(VEDataType.GeoRSS, url, layer);
	 	map.ImportShapeLayerData(mlayerspec);
	},

	addTileLayer: function(tile_url, opacity, copyright_text, min_zoom, max_zoom) {
		throw 'Not implemented';
	},

	toggleTileLayer: function(tile_url) {
		throw 'Not implemented';
	},

	getPixelRatio: function() {
		throw 'Not implemented';
	},
	
	mousePosition: function(element) {
		var locDisp = document.getElementById(element);
		if (locDisp !== null) {
			var map = this.maps[this.api];
			map.AttachEvent("onmousemove", function(veEvent){
				var latlon = map.PixelToLatLong(new VEPixel(veEvent.mapX, veEvent.mapY));
				var loc = latlon.Latitude.toFixed(4) + " / " + latlon.Longitude.toFixed(4);
				locDisp.innerHTML = loc;
			});
			locDisp.innerHTML = "0.0000 / 0.0000";
		}
	},

	openBubble: function(point, content) {
		var map = this.maps[this.api],
			// Bing requires a shape reference
			shape = new VEShape(VEShapeType.Pushpin, point.toProprietary(this.api));
		shape.SetDescription(content);
		map.AddShape(shape);
		shape.Hide();
		map.HideInfoBox(); // otherwise the position won't update
		map.ShowInfoBox(shape);
		this.bubble_shape = shape;
	},

	closeBubble: function() {
		var map = this.maps[this.api];
		map.HideInfoBox();
		map.DeleteShape(this.bubble_shape);
	}
},

LatLonPoint: {
	
	toProprietary: function() {
		return  new VELatLong(this.lat, this.lon);
	},

	fromProprietary: function(mpoint) {
		this.lat = mpoint.Latitude;
		this.lon = mpoint.Longitude;
	}
	
},

Marker: {
	
	toProprietary: function() {
		var mmarker = new VEShape(VEShapeType.Pushpin, this.location.toProprietary('microsoft'));
		mmarker.SetTitle(this.labelText);
		
		if (this.iconUrl) {
			var customIcon = new VECustomIconSpecification();
			customIcon.Image = this.iconUrl;
			// See this article on how to patch 6.2 to correctly render offsets.
			// http://social.msdn.microsoft.com/Forums/en-US/vemapcontroldev/thread/5ee2f15d-09bf-4158-955e-e3fa92f33cda?prof=required&ppud=4
			if (this.iconAnchor) {
			   customIcon.ImageOffset = new VEPixel(-this.iconAnchor[0], -this.iconAnchor[1]);
			} 
			else if (this.iconSize) {
			   customIcon.ImageOffset = new VEPixel(-this.iconSize[0]/2, -this.iconSize[1]/2);
			}
			mmarker.SetCustomIcon(customIcon);	
		}
		if (this.draggable){
			mmarker.Draggable = true;
		}
		
		return mmarker;
	},

	openBubble: function() {
		if (!this.map) {
			throw 'Marker must be added to map in order to display infobox';
		}
		this.proprietary_marker.SetDescription(this.infoBubble);
		this.map.HideInfoBox(); // otherwise the position won't update
		this.map.ShowInfoBox(this.proprietary_marker);
	},
	
	closeBubble: function() {
		if (!this.map) {
			throw 'Marker must be added to map in order to display infobox';
		}
		this.map.HideInfoBox();
	},

	hide: function() {
		this.proprietary_marker.Hide();
		this.hidden = true;
	},

	show: function() {
		this.proprietary_marker.Show();
		this.hidden = false;
	},
	
	isHidden: function() {
		return this.hidden || false;
	},

	update: function() {
		throw 'Not implemented';
	}
	
},

Polyline: {

	toProprietary: function() {
		var mpoints = [];
		for (var i =0, length = this.points.length; i < length; i++) {
			mpoints.push(this.points[i].toProprietary('microsoft'));
		}
		var shapeType = this.closed ? VEShapeType.Polygon : VEShapeType.Polyline,
			mpolyline = new VEShape(shapeType, mpoints);
		// set options
        var color, opacity, vecolor;
		if (this.color) {
			color = new mxn.util.Color(this.color);
            opacity = (this.opacity === 'undefined' || this.opacity === null) ? 1.0 : this.opacity;
            vecolor = new VEColor(color.red, color.green, color.blue, opacity);
			mpolyline.SetLineColor(vecolor);
		}
		if (this.fillColor) {
			color = new mxn.util.Color(this.fillColor);
            opacity = (this.fillOpacity === 'undefined' || this.fillOpacity === null) ? 0.3 : this.fillOpacity;
            vecolor = new VEColor(color.red, color.green, color.blue, opacity);
			mpolyline.SetFillColor(vecolor);
		}
		if (this.width) {
			mpolyline.SetLineWidth(this.width);
		}
		return mpolyline;
	},
		
	show: function() {
		this.proprietary_polyline.Show();
	},

	hide: function() {
		this.proprietary_polyline.Hide();
	},
	
	isHidden: function() {
		return this.proprietary_polyline.Visibility;
	}
	
}

});
