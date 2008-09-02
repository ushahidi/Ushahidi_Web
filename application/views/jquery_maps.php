jQuery(function() {
    // Now initialise the map
    var mapstraction = new Mapstraction('divMap','google');
    mapstraction.addControls({
        zoom: 'large',
        map_type: true
    });

    // Show map centred on default location
    mapstraction.setCenterAndZoom(
        new LatLonPoint(50.82423734980143, -0.14007568359375),
        15 // default zoom level 
    );

	// capture the mouse click event
	mapstraction.addEventListener( 'click', onClickMap);

	// on each mouse click this function will be called with the point 
	// object of mapstraction
	function onClickMap( point) {
		// the yahoo map returns 0 for lat and 180 for lon when user
		// clicks on a control on the map (for example on the pan-left arrow)
		if (point.lat != 0) {
			// we will remove existing markers on the map (resetting the map)
			mapstraction.removeAllMarkers();
			// we add a marker at the clicked location
			mapstraction.addMarker( new Marker( new LatLonPoint(point.lat,point.lon)));
			$("#latitude").attr("value", point.lat);
			$("#longitude").attr("value", point.lon); 
		}
	}

});