/*
   Copyright (c) 2007, Andrew Turner
   All rights reserved.

   Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

 * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * Neither the name of the Mapstraction nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


// Use http://jsdoc.sourceforge.net/ to generate documentation

//////////////////////////// 
//
// utility to functions, TODO namespace or remove before release
//
///////////////////////////

/**
 * MapstractionRouter instantiates a router with some API choice 
 * @param {Function} callback The function to call when a route request returns (function(waypoints, route))
 * @param {String} api The API to use, currently only 'mapquest' is supported
 * @param {Function} error_callback The optional function to call when a route request fails
 * @constructor
 */
function MapstractionRouter(callback, api, error_callback) {
  this.api = api;
	this.callback = callback;
	this.routers = new Object();
	this.geocoders = new Object();
	if(error_callback == null) {
		this.error_callback = this.route_error
	} else {
		this.error_callback = error_callback;
	}

  // This is so that it is easy to tell which revision of this file 
  // has been copied into other projects.
  this.svn_revision_string = '$Revision: 107 $';

  this.addAPI(api);

}

/**
 * Internal function to actually set the router specific parameters
 */
 MapstractionRouter.prototype.addAPI = function(api, options) { 
     me = this;
     switch (api) {
         case 'mapquest':

         //set up the connection to the route server
         var proxyServerName = "";
         var proxyServerPort = "";
         var proxyServerPath = "mapquest_proxy/JSReqHandler.php";

         var geocoderServerName = "geocode.access.mapquest.com";
         var routerServerName = "route.access.mapquest.com";
         var serverPort = "80";
         var serverPath = "mq";
         for(var sOptKey in options) {
             switch(sOptKey) {
                 case 'var proxyServerName ':
                 proxyServerName = options.proxyServerName;
                 break;
                 case 'proxyServerPort':
                 proxyServerPort = options.proxyServerPort;
                 break;
                 case 'proxyServerPath':
                 proxyServerPath = options.proxyServerPath;
                 break;
                 case 'geocoderServerName':
                 geocoderServerName = options.geocoderServerName;
                 break;
                 case 'routerServerName ':
                 routerServerName = options.routerServerName ;
                 break;
                 case 'serverPort ':
                 serverPort = options.serverPort ;
                 break;        
                 case 'var serverPath ':
                 serverPath = options.serverPath ;
                 break;
             }
         }
         this.geocoders[api] = new MQExec(geocoderServerName, serverPath, serverPort, proxyServerName, proxyServerPath, proxyServerPort );
         this.routers[api] = new MQExec(routerServerName, serverPath, serverPort, proxyServerName, proxyServerPath, proxyServerPort );

         break;
         default:
         alert(api + ' not supported by mapstraction-router');
     }
 }
/**
 * Change the Routing API to use
 * @param {String} api The API to swap to
 */
MapstractionRouter.prototype.swap = function(api) {
  if (this.api == api) { return; }

  this.api = api;
  if (this.routers[this.api] == undefined) {
    this.addAPI($(element),api);
  }
}

/**
 * Default Route error function
 */
MapstractionRouter.prototype.route_error = function(response) { 
	alert("Sorry, we were unable to route that address");
}
/**
 * Default handler for route request completion
 */
MapstractionRouter.prototype.route_callback = function(response, mapstraction_router) { 
	
	// TODO: what if the api is switched during a route request?
	// TODO: provide an option error callback
	switch (mapstraction_router.api) {
		case 'mapquest':
			break;
	}
}

/**
 * Performs a routing and then calls the specified callback function with the waypoints and route
 * @param {Array} addresses The array of address objects to use for the waypoints of the route
 */
MapstractionRouter.prototype.route = function(addresses) { 

	var api = this.api;
	switch (api) {
		case 'mapquest':
			var waypoints = new MQLocationCollection();
			var mapstraction_points = Array();
			var gaCollection = new MQLocationCollection("MQGeoAddress");
			var routeOptions = new MQRouteOptions();
			for (var i=0;i<addresses.length;i++) {
				var mqaddress = new MQAddress();

				//first geocode all the user entered locations
				mqaddress.setStreet(addresses[i].street);
				mqaddress.setCity(addresses[i].locality);
				mqaddress.setState(addresses[i].region);
				mqaddress.setPostalCode(addresses[i].postalcode);
				mqaddress.setCountry(addresses[i].country);
				this.geocoders[api].geocode(mqaddress, gaCollection);
				var geoAddr = gaCollection.get(0);
				waypoints.add(geoAddr);

				// Create an array of Mapstraction points to use for markers
  			var mapstraction_point = new Object();
				mapstraction_point.street = geoAddr.getStreet();
				mapstraction_point.locality = geoAddr.getCity();
				mapstraction_point.region = geoAddr.getState();
				mapstraction_point.country = geoAddr.getCountry();
				var mqpoint = geoAddr.getMQLatLng();
				mapstraction_point.point = new LatLonPoint(mqpoint.getLatitude(), mqpoint.getLongitude());
				mapstraction_points.push(mapstraction_point);
			}

			var session = new MQSession();	
			var routeResults = new MQRouteResults();
			var routeBoundingBox = new MQRectLL(new MQLatLng(),new MQLatLng());	
			var sessId = this.routers[api].createSessionEx(session);
			this.routers[api].doRoute(waypoints,routeOptions,routeResults,sessId,routeBoundingBox);
						
			var routeParameters = new Array();
			routeParameters['results'] = routeResults;
			routeParameters['bounding_box'] = routeBoundingBox;
			routeParameters['session_id'] = sessId;
			
			this.callback(mapstraction_points, routeParameters);
			break;
    default:
      alert(api + ' not supported by mapstraction-router');
			break;
  }
}

/**
* Performs a routing and then calls the specified callback function with the waypoints and route
* 
* @param {Array} addresses The array of point/location objects to use for the route
*/
MapstractionRouter.prototype.routePoints = function(points) { 

    var api = this.api;
    switch (api) {
        case 'mapquest':
        var waypoints = new MQLocationCollection();
        var routeOptions = new MQRouteOptions();

        for (var i=0;i<points.length;i++) {
            var geoAddr = new MQGeoAddress();
            geoAddr.setMQLatLng(new MQLatLng(points[i].lat, points[i].lng));
            waypoints.add(geoAddr);
        }

        var session = new MQSession();	
        var routeResults = new MQRouteResults();
        var routeBoundingBox = new MQRectLL(new MQLatLng(),new MQLatLng());	
        var sessId = this.routers[api].createSessionEx(session);

        this.routers[api].doRoute(waypoints,routeOptions,routeResults,sessId,routeBoundingBox);

        var routeParameters = new Array();
        routeParameters['results'] = routeResults;
        routeParameters['bounding_box'] = routeBoundingBox;
        routeParameters['session_id'] = sessId;

        this.callback(points, routeParameters);
        break;
        default:
        alert(api + ' not supported by mapstraction-router');
        break;
    }
}
