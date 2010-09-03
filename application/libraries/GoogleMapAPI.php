<?php
/*
 * Project:     GoogleMapAPI V3: a PHP library inteface to the Google Map API v3
 * File:        GoogleMapV3.php
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ORIGINAL INFO
 * link http://www.phpinsider.com/php/code/GoogleMapAPI/
 * copyright 2005 New Digital Group, Inc.
 * author Monte Ohrt <monte at ohrt dot com>
 * package GoogleMapAPI
 * version 2.5
 * 
 * REVISION NOTIFICATION
 * NOTE: This is a modified version of the original listed above.  This version
 * maintains all original GNU software licenses.
 */
/**
 * @link http://code.google.com/p/phpgooglemapapiv3/
 * @copyright 2010 Brad wedell
 * @author Brad Wedell
 * @package GoogleMapAPI (version 3)
 * @version 3.0beta
*/
/*
 * To view the full CHANGELOG, visit 
 * http://code.google.com/p/php-google-map-api/wiki/ChangeLog3
/*
For database caching, you will want to use this schema:

CREATE TABLE GEOCODES (
  address varchar(255) NOT NULL default '',
  lon float default NULL,
  lat float default NULL,
  PRIMARY KEY  (address)
);

*/

/**
 * PHP Google Maps API class
 * @package GoogleMapAPI
 * @version 3.0beta
 */
class GoogleMapAPI_Core {

    /**
     * PEAR::DB DSN for geocode caching. example:
     * $dsn = 'mysql://user:pass@localhost/dbname';
     *
     * @var string
     */
    var $dsn = null;
    
    /**
     * current map id, set when you instantiate
     * the GoogleMapAPI object.
     *
     * @var string
     */
    var $map_id = null;
	
	/**
	 * determines whether or not to display the map and associated JS on the page
	 * this is used if you just want to display a streetview with no map
	 */
	var $display_map = true;

    /**
     * sidebar <div> used along with this map.
     *
     * @var string
     */
    var $sidebar_id = null;    
    
    /**
     * Whether to use new V3 mobile functionality
     *
     * @var bool
     */
    var $mobile=false;
    
    /**
     * DEPRECATED: Google now has geocoding service.  
     * NOTE: Note even sure if this still works
     * GoogleMapAPI used to use the Yahoo geocode lookup API.
     * This is the application ID for YOUR application.
     * This is set upon instantiating the GoogleMapAPI object.
     * (http://developer.yahoo.net/faq/index.html#appid)
     *
     * @var string
     */
    var $app_id = null;

    /**
     * use onLoad() to load the map javascript.
     * if enabled, be sure to include on your webpage:
     * <?=$mapobj->printOnLoad?> or manually create an onload function 
     * that calls the map's onload function using $this->printOnLoadFunction
     *
     * @var bool
     * @default true
     */
    var $onload = true;
    
    /**
     * map center latitude (horizontal)
     * calculated automatically as markers
     * are added to the map.
     *
     * @var float
     */
    var $center_lat = null;

    /**
     * map center longitude (vertical)
     * calculated automatically as markers
     * are added to the map.
     *
     * @var float
     */
    var $center_lon = null;
    
    /**
     * enables map controls (zoom/move/center)
     *
     * @var bool
     */
    var $map_controls = true;

    /**
     * determines the map control type
     * small -> show move/center controls
     * large -> show move/center/zoom controls
     *
     * @var string
     */
    var $control_size = 'large';
    
    /**
     * enables map type controls (map/satellite/hybrid/terrain)
     *
     * @var bool
     */
    var $type_controls = true;
	
	/**
	 * sets default option for type controls(DEFAULT, HORIZONTAL_BAR, DROPDOWN_MENU)
	 *
	 * @var string
	 */
	 var $type_controls_style = "DEFAULT";

    /**
     * default map type google.maps.MapTypeId.(ROADMAP, SATELLITE, HYBRID, TERRAIN)
     *
     * @var string
     */
    var $map_type = 'HYBRID';
    
    /**
     * enables scale map control
     *
     * @var bool
     */
    var $scale_control = true;
    
    /**
     * enables overview map control
     *
     * @var bool
     */
    var $overview_control = false;
    
    /**
     * enables Google Adsense Adsmanager on page, not currently supported in beta
     *
     * @var bool
     */
    var $ads_manager = false;
    
    /**
     * Google Adsense Publisher ID
     *
     * @var string
     */
    var $ads_pub_id = "";  
    
    /**
     * Google Adsense Channel ID
     *
     * @var string
     */
    var $ads_channel = "";
    
    /**
     * The Max number of Adsmanager ads to show on a map
     *
     * @var int
     */
    var $ads_max = 10;
    
    /**
     * enables/disables local search on page
     *
     * @var bool
     */
    var $local_search = false;
    
    /**
     * enables local search ads on page NOTE: will only display ads if local_search == true, otherwise just use ad_manager and settings.
     *
     * @var bool
     */
    var $local_search_ads = false;
    
    /**
     * enables/disables walking directions option
     *
     * @var bool
     */
    var $walking_directions = false;
    
    /**
     * enables/disables biking directions on directions
     *
     * @var bool
     */
    var $biking_directions = false;
    
    /**
     * enables/disables avoid highways on directions
     *
     * @var bool
     */
    var $avoid_highways = false;
    
    /**
     * determines if avoid tollways is used in directions
     *
     * @var bool
     */
    var $avoid_tollways = false;    
     
    /**
     * determines the default zoom level
     *
     * @var int
     */
    var $zoom = 16;

    /**
     * determines the map width
     *
     * @var string
     */
    var $width = '500px';
    
    /**
     * determines the map height
     *
     * @var string
     */
    var $height = '500px';

    /**
     * message that pops up when the browser is incompatible with Google Maps.
     * set to empty string to disable.
     *
     * @var string
     */
    var $browser_alert = 'Sorry, the Google Maps API is not compatible with this browser.';
    
    /**
     * message that appears when javascript is disabled.
     * set to empty string to disable.
     *
     * @var string
     */
    var $js_alert = '<b>Javascript must be enabled in order to use Google Maps.</b>';

    /**
     * determines if sidebar is enabled
     *
     * @var bool
     */
    var $sidebar = true;    

    /**
     * determines if to/from directions are included inside info window
     * 
     * @var bool
     * @deprecated
     */
    var $directions = true;

    /**
     * determines if map markers bring up an info window
     *
     * @var bool
     */
    var $info_window = true;    
    
    /**
     * determines if info window appears with a click or mouseover
     *
     * @var string click/mouseover
     */
    var $window_trigger = 'click';    
	
	/**
	 * determines whether or not to use the MarkerClusterer plugin
	 */
	 var $marker_clusterer = false;
	 
	 /**
	 * set default marker clusterer *webserver* file location
	 */
	 var $marker_clusterer_location = "/MarkerClusterer-1.0/markerclusterer_compiled.js";
	 
	 /**
	 * set default marker clusterer options
	 */
	 var $marker_clusterer_options = array(
	 	"maxZoom"=>"null",
		"gridSize"=>"null",
		"styles"=>"null"
	 );
    
    /**
     * determines if traffic overlay is displayed on map
     *
     * @var bool
     */
    var $traffic_overlay = false;
	
	/**
     * determines if biking overlay is displayed on map
     *
     * @var bool
     */
    var $biking_overlay = false;   
	
	/**
	 * determines whether or not to display street view controls
	 */
	var $street_view_controls = false;
	
	/**
	 * ID of the container that will hold a street view if streetview controls = true.
	 */
	var $street_view_dom_id = "";
    
    /**
     * what server geocode lookups come from
     *
     * available: YAHOO  Yahoo! API. US geocode lookups only.
     *            GOOGLE Google Maps. This can do international lookups,
     *                   but not an official API service so no guarantees.
     *            Note: GOOGLE is the default lookup service, please read
     *                  the Yahoo! terms of service before using their API.
     *
     * @var string service name
     */
    var $lookup_service = 'GOOGLE';
    var $lookup_server = array('GOOGLE' => 'maps.google.com', 'YAHOO' => 'api.local.yahoo.com');
    
    /**
     * 
     * @var array
     * @deprecated
     */
    var $driving_dir_text = array(
            'dir_to' => 'Start address: (include addr, city st/region)',
            'to_button_value' => 'Get Directions',
            'to_button_type' => 'submit',
            'dir_from' => 'End address: (include addr, city st/region)',
            'from_button_value' => 'Get Directions',
            'from_button_type' => 'submit',
            'dir_text' => 'Directions: ',
            'dir_tohere' => 'To here',
            'dir_fromhere' => 'From here'
            );             
               
    
    /**
     * version number
     *
     * @var string
     */
    var $_version = '3.0beta';

    /**
     * list of added markers
     *
     * @var array
     */
    var $_markers = array();
    
    /**
     * maximum longitude of all markers
     * 
     * @var float
     */
    var $_max_lon = -1000000;
    
    /**
     * minimum longitude of all markers
     *
     * @var float
     */
    var $_min_lon = 1000000;
    
    /**
     * max latitude
     *
     * @var float
     */
    var $_max_lat = -1000000;
    
    /**
     * min latitude
     *
     * @var float
     */
    var $_min_lat = 1000000;
    
    /**
     * determines if we should zoom to minimum level (above this->zoom value) that will encompass all markers
     *
     * @var bool
     */
    var $zoom_encompass = true;

    /**
     * factor by which to fudge the boundaries so that when we zoom encompass, the markers aren't too close to the edge
     *
     * @var float
     */
    var $bounds_fudge = 0.01;

    /**
     * use the first suggestion by a google lookup if exact match not found
     *
     * @var float
     */
    var $use_suggest = false;

    
    /**
     * list of added polylines
     *
     * @var array
     */
    var $_polylines = array();
	
	/**
	 * list of polylines that should have an elevation profile rendered.
	 */
	var $_elevation_polylines = array();
	
	/**
	 * determines whether or not to display a marker on the "line" when 
	 * mousing over the elevation chart
	 */
	var $elevation_markers = true;
	
	/**
	 * determines whether or not to display an elevation chart 
	 * for directions that are added to the map.  
	 */
	var $elevation_directions = false;
        
    /**
     * icon info array
     *
     * @var array
     * @deprecated
     * @version 2.5  
     */
    var $_icons = array();  
       
    /**
     * marker icon info array
     *
     * @var array
     * @version 3.0
     */
    var $_marker_icons = array();
    
    /**
     * Default icon image location.
     *
     * @var string
     */
    var $default_icon = "";
    
    /**
     * Default icon shadow image location.
     *
     * @var string
     */
    var $default_icon_shadow = "";
	
	/**
     * list of added overlays
     *
     * @var array
     */
    var $_overlays = array();
    
    /**
     * list of added kml overlays
     */
    var $_kml_overlays = array();
        

    /**
     * database cache table name
     *
     * @var string
     */
    var $_db_cache_table = 'GEOCODES';
    
    /**
     * Class variable that will store generated header code for JS to display directions
     *
     * @var string
     */
    var $_directions_header = '';
	
	/**
	 * Class variable that will store information to render directions
	 */
	var $_directions = array();
    
    /**
     * Class variable to store whether or not to display JS functions in the header
     */
    var $_display_js_functions = true;
    
    /**
     * Class variable that will store flag to minify js - this can be overwritten after object is instantiated.  Include JSMin.php if 
	 * you want to use JS Minification.
     *
     * @var bool
     */
	 var $_minify_js = true;
        
    /**
     * class constructor
     *
     * @param string $map_id the DOM element ID for the map
     * @param string $app_id YOUR Yahoo App ID
     */
    function GoogleMapAPI($map_id = 'map', $app_id = 'MyMapApp') {
        $this->map_id = $map_id;
        $this->sidebar_id = 'sidebar_' . $map_id;
        $this->app_id = $app_id;
    }
	
	/**
	 * function to enable map display
	 */
	function enableMapDisplay(){
		$this->display_map = true;
	}
	
	/**
	 * function to disable map display (used to display street view only)
	 */
	function disableMapDisplay(){
		$this->display_map = true;
	}
    
    /**
     * sets the PEAR::DB dsn
     *
     * @param string $dsn Takes the form of "mysql://user:pass@localhost/db_name"
     */
    function setDSN($dsn) {
        $this->dsn = $dsn;   
    }
    
    /**
     * sets the width of the map
     *
     * @param string $width
     * @return string|false Width or false if not a valid value
     */
    function setWidth($width) {
        if(!preg_match('!^(\d+)(.*)$!',$width,$_match))
            return false;

        $_width = $_match[1];
        $_type = $_match[2];
        if($_type == '%')
            $this->width = $_width . '%';
        else
            $this->width = $_width . 'px';
        
        return true;
    }

    /**
     * sets the height of the map
     *
     * @param string $height
     * @return string|false Height or false if not a valid value
     */
    function setHeight($height) {
        if(!preg_match('!^(\d+)(.*)$!',$height,$_match))
            return false;

        $_height = $_match[1];
        $_type = $_match[2];
        if($_type == '%')
            $this->height = $_height . '%';
        else
            $this->height = $_height . 'px';
        
        return true;
    }  
          

    /**
     * sets the default map zoom level
     *
     * @param string $level Initial zoom level value
     */
    function setZoomLevel($level) {
        $this->zoom = (int) $level;
    }    
            
    /**
     * enables the map controls (zoom/move)
     *
     */
    function enableMapControls() {
        $this->map_controls = true;
    }

    /**
     * disables the map controls (zoom/move)
     *
     */
    function disableMapControls() {
        $this->map_controls = false;
    } 
    
    /**
     * sets the map control size (large/small)
     *
     * @param string $size Large/Small
     */
    function setControlSize($size) {
        if(in_array($size,array('large','small')))
            $this->control_size = $size;
    }            

     /**
     * enables the type controls (map/satellite/hybrid)
     *
     */
    function enableLocalSearch() {
        $this->local_search = true;
    }

    /**
     * disables the type controls (map/satellite/hybrid)
     *
     */
    function disableLocalSearch() {
        $this->local_search = false;
    }
    
    /**
     * enables the type controls (map/satellite/hybrid)
     *
     */
    function enableLocalSearchAds() {
        $this->local_search_ads = true;
    }

    /**
     * disables the type controls (map/satellite/hybrid)
     *
     */
    function disableLocalSearchAds() {
        $this->local_search_ads = false;
    }
    
    /**
     * enables walking directions
     * 
     */
    function enableWalkingDirections() {
        $this->walking_directions = true;
    }

    /**
     * disables walking directions
     * 
     */
    function disableWalkingDirections() {
        $this->walking_directions = false;
    }
    
    /**
     * enables biking directions
     * 
     */
    function enableBikingDirections() {
        $this->biking_directions = true;
    }

    /**
     * disables biking directions
     * 
     */
    function disableBikingDirections() {
        $this->biking_directions = false;
    }
    
    /**
     * enables avoid highways in directions
     * 
     */
    function enableAvoidHighways() {
        $this->avoid_highways = true;
    }

    /**
     * disables avoid highways in directions
     * 
     */
    function disableAvoidHighways() {
        $this->avoid_highways = false;
    }
    
    /**
     * enables avoid tolls in directions
     * 
     */
    function enableAvoidTolls() {
        $this->avoid_tolls = true;
    }

    /**
     * disables avoid tolls in directions
     * 
     */
    function disableAvoidTolls() {
        $this->avoid_tolls = false;
    }
    
    /**
     * Add directions route to the map and adds text directions container with id=$dom_id
     *
     * @param string $start_address
     * @param string $dest_address
     * @param string $dom_id DOM Element ID for directions container.
     * @param bool $add_markers Add a marker at start and dest locations.
     */
    function addDirections($start_address='',$dest_address='',$dom_id='', $add_markers=true, $elevation_samples=256, $elevation_width="", $elevation_height="", $elevation_dom_id=''){
		if($elevation_dom_id=="")
			$elevation_dom_id = "elevation".$dom_id;
		
        if($start_address != '' && $dest_address != '' && $dom_id != ''){
			$this->_directions[$dom_id] = array(
				"dom_id"=>$dom_id,
				"start"=>$start_address,
				"dest"=>$dest_address,
				"markers"=>true,
				"elevation_samples"=>$elevation_samples,
				"width"=>($elevation_width!=""?$elevation_width:str_replace("px","",$this->width)),
				"height"=>($elevation_height!=""?$elevation_height:str_replace("px","",$this->height)/2),
				"elevation_dom_id"=>$elevation_dom_id
			);			
			if($add_markers==true){
				$this->addMarkerByAddress($start_address,$start_address, $start_address);
				$this->addMarkerByAddress($dest_address,$dest_address, $dest_address);
			} 
        }
    }
        
    /**
     * enables the type controls (map/satellite/hybrid)
     *
     */
    function enableTypeControls() {
        $this->type_controls = true;
    }

    /**
     * disables the type controls (map/satellite/hybrid)
     *
     */
    function disableTypeControls() {
        $this->type_controls = false;
    }
	
	/**
	 * sets map control style
	 */
	function setTypeControlsStyle($type){
		switch($type){
			case "dropdown":
				$this->type_controls_style = "DROPDOWN_MENU";
				break;
			case "horizontal":
				$this->type_controls_style = "HORIZONTAL_BAR";
				break;
			default:
				$this->type_controls_style = "DEFAULT";
				break;
		}
	}
	
    /**
     * set default map type (map/satellite/hybrid)
     *
     * @param string $type New V3 Map Types, only include ending word (HYBRID,SATELLITE,TERRAIN,ROADMAP)
     */
    function setMapType($type) {
        switch($type) {
            case 'hybrid':
                $this->map_type = 'HYBRID';
                break;
            case 'satellite':
                $this->map_type = 'SATELLITE';
                break;
            case 'terrain':
                $this->map_type = 'TERRAIN';
                break;
            case 'map':
            default:
                $this->map_type = 'ROADMAP';
                break;
        }       
    }	   
	    
    /**
     * enables onload
     *
     */
    function enableOnLoad() {
        $this->onload = true;
    }

    /**
     * disables onload
     *
     */
    function disableOnLoad() {
        $this->onload = false;
    }
    
    /**
     * enables sidebar
     *
     */
    function enableSidebar() {
        $this->sidebar = true;
    }

    /**
     * disables sidebar
     *
     */
    function disableSidebar() {
        $this->sidebar = false;
    }    

    /**
     * enables map directions inside info window
     *
     */
    function enableDirections() {
        $this->directions = true;
    }

    /**
     * disables map directions inside info window
     *
     */
    function disableDirections() {
        $this->directions = false;
    }    
        
    /**
     * set browser alert message for incompatible browsers
     *
     * @param string $message
     */
    function setBrowserAlert($message) {
        $this->browser_alert = $message;
    }

    /**
     * set <noscript> message when javascript is disabled
     *
     * @param string $message
     */
    function setJSAlert($message) {
        $this->js_alert = $message;
    }
    
    /**
     * enable traffic overlay 
     */
    function enableTrafficOverlay() {
        $this->traffic_overlay= true;
    }
    
    /**
     * disable traffic overlay (default)
     */
    function disableTrafficOverlay() {
        $this->traffic_overlay= false;
    }
	
	/**
     * enable biking overlay 
     */
    function enableBikingOverlay() {
        $this->biking_overlay= true;
    }
    
    /**
     * disable biking overlay (default)
     */
    function disableBikingOverlay() {
        $this->biking_overlay= false;
    }
	
	/**
     * enable biking overlay 
     */
    function enableStreetViewControls() {
        $this->street_view_controls= true;
    }
    
    /**
     * disable biking overlay (default)
     */
    function disableStreetViewControls() {
        $this->street_view_controls= false;
    }
	
	/**
	 * attach a dom id object as a streetview container to the map
	 * NOTE: Only one container can be attached to a map.
	 **/
	function attachStreetViewContainer($dom_id){
		$this->street_view_dom_id = $dom_id;
	}
    
    /**
     * enable Google Adsense admanager on Map (not supported in V3 API)
     */
    function enableAds(){
        $this->ads_manager = true; 
    }
    
    /**
     * disable Google Adsense admanager on Map (not supported in V3 API)
     */
    function disableAds(){
        $this->ads_manager = false; 
    }
    

    /**
     * enable map marker info windows
     */
    function enableInfoWindow() {
        $this->info_window = true;
    }
    
    /**
     * disable map marker info windows
     */
    function disableInfoWindow() {
        $this->info_window = false;
    }
	
	/**
	 * enable elevation marker to be displayed
	 */
	 function enableElevationMarker(){
		 $this->elevation_markers = true;
	 }
	 
	 /**
	 * disable elevation marker
	 */
	 function disableElevationMarker(){
		 $this->elevation_markers = false;
	 }
	 
	 /**
	 * enable elevation to be displayed for directions
	 */
	 function enableElevationDirections(){
		 $this->elevation_directions = true;
	 }
	 
	 /**
	 * disable elevation to be displayed for directions
	 */
	 function disableElevationDirections(){
		 $this->elevation_directions = false;
	 }
    
    /**
     * enable map marker clustering
     */
    function enableClustering() {
        $this->marker_clusterer = true;
    }
    
    /**
     * disable map marker clustering
     */
    function disableClustering() {
        $this->marker_clusterer = false;
    }
    
    /**
     * set clustering options
     */
    function setClusterOptions($zoom="null", $gridsize="null", $styles="null"){
    	$this->marker_clusterer_options["maxZoom"]=$zoom;
    	$this->marker_clusterer_options["gridSize"]=$gridsize;
    	$this->marker_clusterer_options["styles"]=$styles;
    }   

    /**
     * Set clustering library file location
     */
    function setClusterLocation($file){
    	$this->marker_clusterer_location=$file;
    }
    
    /**
     * set the info window trigger action
     *
     * @param string $message click/mouseover
     */
    function setInfoWindowTrigger($type) {
        switch($type) {
            case 'mouseover':
                $this->window_trigger = 'mouseover';
                break;
            default:
                $this->window_trigger = 'click';
                break;
            }
    }

    /**
     * enable zoom to encompass makers
     */
    function enableZoomEncompass() {
        $this->zoom_encompass = true;
    }
    
    /**
     * disable zoom to encompass makers
     */
    function disableZoomEncompass() {
        $this->zoom_encompass = false;
    }

    /**
     * set the boundary fudge factor
     * @param float
     */
    function setBoundsFudge($val) {
        $this->bounds_fudge = $val;
    }
    
    /**
     * enables the scale map control
     *
     */
    function enableScaleControl() {
        $this->scale_control = true;
    }

    /**
     * disables the scale map control
     *
     */
    function disableScaleControl() {
        $this->scale_control = false;
    }    
            
    /**
     * enables the overview map control
     *
     */
    function enableOverviewControl() {
        $this->overview_control = true;
    }

    /**
     * disables the overview map control
     *
     */
    function disableOverviewControl() {
        $this->overview_control = false;
     }    
    
    
    /**
     * set the lookup service to use for geocode lookups
     * default is YAHOO, you can also use GOOGLE.
     * NOTE: GOOGLE can to intl lookups, but is not an
     * official API, so use at your own risk.
     *
     * @param string $service
     * @deprecated
     */
    function setLookupService($service) {
        switch($service) {
            case 'GOOGLE':
                $this->lookup_service = 'GOOGLE';
                break;
            case 'YAHOO':
            default:
                $this->lookup_service = 'YAHOO';
                break;
        }       
    }
    
        
    /**
     * adds a map marker by address - DEPRECATION WARNING: Tabs are no longer supported in V3, if this changes this can be easily updated.
     * 
     * @param string $address the map address to mark (street/city/state/zip)
     * @param string $title the title display in the sidebar
     * @param string $html the HTML block to display in the info bubble (if empty, title is used)
     * @param string $tooltip Tooltip to display (deprecated?)
     * @param string $icon_filename Web file location (eg http://somesite/someicon.gif) to use for icon
     * @param string $icon_shadow_filename Web file location (eg http://somesite/someicon.gif) to use for icon shadow
     * @return int|bool
     */
    function addMarkerByAddress($address,$title = '',$html = '',$tooltip = '', $icon_filename = '', $icon_shadow_filename='') {
        if(($_geocode = $this->getGeocode($address)) === false)
            return false;
        return $this->addMarkerByCoords($_geocode['lon'],$_geocode['lat'],$title,$html,$tooltip, $icon_filename, $icon_shadow_filename);
    }

    /**
     * adds a map marker by lat/lng coordinates - DEPRECATION WARNING: Tabs are no longer supported in V3, if this changes this can be easily updated.
     * 
     * @param string $lon the map longitude (horizontal)
     * @param string $lat the map latitude (vertical)
     * @param string $title the title display in the sidebar
     * @param string $html the HTML block to display in the info bubble (if empty, title is used)
     * @param string $tooltip Tooltip to display (deprecated?)
     * @param string $icon_filename Web file location (eg http://somesite/someicon.gif) to use for icon
     * @param string $icon_shadow_filename Web file location (eg http://somesite/someicon.gif) to use for icon shadow
     * @return int|bool    
     */
    function addMarkerByCoords($lon,$lat,$title = '',$html = '',$tooltip = '', $icon_filename = '', $icon_shadow_filename='') {
        $_marker['lon'] = $lon;
        $_marker['lat'] = $lat;
        $_marker['html'] = (is_array($html) || strlen($html) > 0) ? $html : $title;
        $_marker['title'] = $title;
        $_marker['tooltip'] = $tooltip;
        
        if($icon_filename!=""){
            $_marker['icon_key'] = $this->setMarkerIconKey($icon_filename, $icon_shadow_filename);
            if($icon_shadow_filename!=""){
               $_marker['shadow_icon']=1;
            }   
        }else if( $this->default_icon != ''){
        	$_marker['icon_key'] = $this->setMarkerIconKey($this->default_icon, $this->default_icon_shadow);
        	if($this->default_icon_shadow!=""){
        	   $_marker['shadow_icon']=1;
        	}
        }        
        $this->_markers[] = $_marker;
        $this->adjustCenterCoords($_marker['lon'],$_marker['lat']);
        // return index of marker
        return count($this->_markers) - 1;
    }
    
    /**
     * adds a DOM object ID to specified marker to open the marker's info window.
     *   Does nothing if the info windows is disabled.
     * @param string $marker_id ID of the marker to associate to
     * @param string $dom_id ID of the DOM object to use to open marker info window
     * @return bool true/false status
     */
    function addMarkerOpener($marker_id, $dom_id){
    	if($this->info_window === false || !isset($this->_markers[$marker_id]))
    	   return false;
    	if(!isset($this->_markers[$marker_id]["openers"]))
    	   $this->_markers[$marker_id]["openers"] = array();
        $this->_markers[$marker_id]["openers"][] = $dom_id;    	   
    }
    
    /**
     * adds polyline by passed array
     * if color, weight and opacity are not defined, use the google maps defaults
     * @param array $polyline_array array of lat/long coords
     * @param string $id An array id to use to append coordinates to a line
     * @param string $color the color of the line (format: #000000)
     * @param string $weight the weight of the line in pixels
     * @param string $opacity the line opacity (percentage)
     * @return bool|int Array id of newly added point or false
     */
    function addPolylineByCoordsArray($polyline_array,$id=false,$color='',$weight=0,$opacity=0){
    	if(!is_array($polyline_array) || sizeof($polyline_array) < 2)
    	   return false;
    	$_prev_coords = "";
    	$_next_coords = "";
    	
    	foreach($polyline_array as $_coords){
    		$_prev_coords = $_next_coords;
    		$_next_coords = $_coords;
    		
    		if($_prev_coords !== ""){
    		  $_lt1=$_prev_coords["lat"];
    		  $_ln1=$_prev_coords["long"];
    		  $_lt2=$_next_coords["lat"];
              $_ln2=$_next_coords["long"];
    		  $id = $this->addPolyLineByCoords($_ln1, $_lt1, $_ln2, $_lt2, $id, $color, $weight, $opacity);
    		}
    	}
        return $id;
    }
    
    /**
     * adds polyline by passed array
     * if color, weight and opacity are not defined, use the google maps defaults
     * @param array $polyline_array array of addresses
     * @param string $id An array id to use to append coordinates to a line
     * @param string $color the color of the line (format: #000000)
     * @param string $weight the weight of the line in pixels
     * @param string $opacity the line opacity (percentage)
     * @return bool|int Array id of newly added point or false
     */
    function addPolylineByAddressArray($polyline_array,$id=false,$color='',$weight=0,$opacity=0){
        if(!is_array($polyline_array) || sizeof($polyline_array) < 2)
           return false;
        $_prev_address = "";
        $_next_address = "";
        
        foreach($polyline_array as $_address){
            $_prev_address = $_next_address;
            $_next_address = $_address;
            
            if($_prev_address !== ""){
              $id = $this->addPolyLineByAddress($_prev_address, $_next_address, $id, $color, $weight, $opacity);
            }
        }
        return $id;
    }

    /**
     * adds a map polyline by address
     * if color, weight and opacity are not defined, use the google maps defaults
     * 
     * @param string $address1 the map address to draw from
     * @param string $address2 the map address to draw to
     * @param string $id An array id to use to append coordinates to a line
     * @param string $color the color of the line (format: #000000)
     * @param string $weight the weight of the line in pixels
     * @param string $opacity the line opacity (percentage)
     * @return bool|int Array id of newly added point or false
     */
    function addPolyLineByAddress($address1,$address2,$id=false,$color='',$weight=0,$opacity=0) {
        if(($_geocode1 = $this->getGeocode($address1)) === false)
            return false;
        if(($_geocode2 = $this->getGeocode($address2)) === false)
            return false;
        return $this->addPolyLineByCoords($_geocode1['lon'],$_geocode1['lat'],$_geocode2['lon'],$_geocode2['lat'],$id,$color,$weight,$opacity);
    }

    /**
     * adds a map polyline by map coordinates
     * if color, weight and opacity are not defined, use the google maps defaults
     * 
     * @param string $lon1 the map longitude to draw from
     * @param string $lat1 the map latitude to draw from
     * @param string $lon2 the map longitude to draw to
     * @param string $lat2 the map latitude to draw to
     * @param string $id An array id to use to append coordinates to a line
     * @param string $color the color of the line (format: #000000)
     * @param string $weight the weight of the line in pixels
     * @param string $opacity the line opacity (percentage)
	 * @return string $id id of the created/updated polyline array
     */
    function addPolyLineByCoords($lon1,$lat1,$lon2,$lat2,$id=false,$color='',$weight=0,$opacity=0) {
		if($id !== false && isset($this->_polylines[$id]) && is_array($this->_polylines[$id])){
			$_polyline = $this->_polylines[$id];
		}else{
			//only set color,weight,and opacity if new polyline
			$_polyline = array(
				"color"=>$color,
				"weight"=>$weight,
				"opacity"=>$opacity
			);
		}
		if(!isset($_polyline['coords']) || !is_array($_polyline['coords'])){
			$_polyline['coords'] = array(
				"0"=> array("lat"=>$lat1, "long"=>$lon1),
				"1"=> array("lat"=>$lat2, "long"=>$lon2)
			);
		}else{
			$last_index = sizeof($_polyline['coords'])-1;
			//check if lat1/lon1 point is already on polyline
			if($_polyline['coords'][$last_index]["lat"] != $lat1 || $_polyline['coords'][$last_index]["long"]  != $lon1){
				$_polyline['coords'][] = array("lat"=>$lat1, "long"=>$lon1);
			}
			$_polyline['coords'][] = array("lat"=>$lat2, "long"=>$lon2);
		}
        if($id === false){
            $this->_polylines[] = $_polyline;
            $id = count($this->_polylines) - 1;
        }else{
			$this->_polylines[$id] = $_polyline;
        }
        $this->adjustCenterCoords($lon1,$lat1);
        $this->adjustCenterCoords($lon2,$lat2);
        // return index of polyline
        return $id;
    }
	
	/**
	 * function to add an elevation profile for a polyline to the page
	 */
	 function addPolylineElevation($polyline_id, $elevation_dom_id, $samples=256, $width="", $height="", $focus_color="#00ff00"){
		if(isset($this->_polylines[$polyline_id])){
			$this->_elevation_polylines[$polyline_id] = array(
				"dom_id"=>$elevation_dom_id,
				"samples"=>$samples,
				"width"=>($width!=""?$width:str_replace("px","",$this->width)),
				"height"=>($height!=""?$height:str_replace("px","",$this->height)/2),
				"focus_color"=>$focus_color
			);
		}
	 }
	
	/**
	 * function to add an overlay to the map.
	 */
	 function addOverlay($bds_lat1, $bds_lon1, $bds_lat2, $bds_lon2, $img_src, $opacity = 100){
		 $_overlay = array(
			"bounds" => array(
				"ne"=>array(
					"lat"=>$bds_lat1,
					"long"=>$bds_lon1
				),
				"sw"=>array(
					"lat"=>$bds_lat2,
					"long"=>$bds_lon2
				)
			),
			"img" => $img_src,
			"opacity" => $opacity/10
		 );
		 $this->adjustCenterCoords($bds_lon1,$bds_lat1);
         $this->adjustCenterCoords($bds_lon2,$bds_lat2);
		 $this->_overlays[] = $_overlay;
		 return count($this->_overlays)-1;
	 }
	 
	 /**
	  * function to add a KML overlay to the map.
	  *  *Note that this expects a filename and file parsing/processing is done
	  *  on the client side
	  */
	 function addKMLOverlay($file){
	   $this->_kml_overlays[] = $file;
	   return count($this->_kml_overlays)-1;
	 }
        
    /**
     * adjust map center coordinates by the given lat/lon point
     * 
     * @param string $lon the map latitude (horizontal)
     * @param string $lat the map latitude (vertical)
     */
    function adjustCenterCoords($lon,$lat) {
        if(strlen((string)$lon) == 0 || strlen((string)$lat) == 0)
            return false;
		$this->_max_lon = (float) max($lon, $this->_max_lon);
        $this->_min_lon = (float) min($lon, $this->_min_lon);
        $this->_max_lat = (float) max($lat, $this->_max_lat);
        $this->_min_lat = (float) min($lat, $this->_min_lat);
		$this->center_lon = (float) ($this->_min_lon + $this->_max_lon) / 2;
        $this->center_lat = (float) ($this->_min_lat + $this->_max_lat) / 2;
        return true;
    }

    /**
     * set map center coordinates to lat/lon point
     * 
     * @param string $lon the map latitude (horizontal)
     * @param string $lat the map latitude (vertical)
     */
    function setCenterCoords($lon,$lat) {
        $this->center_lat = (float) $lat;
        $this->center_lon = (float) $lon;
    }    

    /**
     * generate an array of params for a new marker icon image
     * iconShadowImage is optional
     * If anchor coords are not supplied, we use the center point of the image by default. 
     * Can be called statically. For private use by addMarkerIcon() and setMarkerIcon() and addIcon()
     *
     * @param string $iconImage URL to icon image
     * @param string $iconShadowImage URL to shadow image
     * @param string $iconAnchorX X coordinate for icon anchor point
     * @param string $iconAnchorY Y coordinate for icon anchor point
     * @param string $infoWindowAnchorX X coordinate for info window anchor point
     * @param string $infoWindowAnchorY Y coordinate for info window anchor point
     * @return array Array with information about newly /previously created icon.
     */
    function createMarkerIcon($iconImage,$iconShadowImage = '',$iconAnchorX = 'x',$iconAnchorY = 'x',$infoWindowAnchorX = 'x',$infoWindowAnchorY = 'x') {
        $_icon_image_path = strpos($iconImage,'http') === 0 ? $iconImage : $_SERVER['DOCUMENT_ROOT'] . $iconImage;
        if(!($_image_info = @getimagesize($_icon_image_path))) {
            die('GoogleMapAPI:createMarkerIcon: Error reading image: ' . $iconImage);   
        }
        if($iconShadowImage) {
            $_shadow_image_path = strpos($iconShadowImage,'http') === 0 ? $iconShadowImage : $_SERVER['DOCUMENT_ROOT'] . $iconShadowImage;
            if(!($_shadow_info = @getimagesize($_shadow_image_path))) {
                die('GoogleMapAPI:createMarkerIcon: Error reading shadow image: ' . $iconShadowImage);
            }
        }
        
        if($iconAnchorX === 'x') {
            $iconAnchorX = (int) ($_image_info[0] / 2);
        }
        if($iconAnchorY === 'x') {
            $iconAnchorY = (int) ($_image_info[1] / 2);
        }
        if($infoWindowAnchorX === 'x') {
            $infoWindowAnchorX = (int) ($_image_info[0] / 2);
        }
        if($infoWindowAnchorY === 'x') {
            $infoWindowAnchorY = (int) ($_image_info[1] / 2);
        }
                        
        $icon_info = array(
                'image' => $iconImage,
                'iconWidth' => $_image_info[0],
                'iconHeight' => $_image_info[1],
                'iconAnchorX' => $iconAnchorX,
                'iconAnchorY' => $iconAnchorY,
                'infoWindowAnchorX' => $infoWindowAnchorX,
                'infoWindowAnchorY' => $infoWindowAnchorY
                );
        if($iconShadowImage) {
            $icon_info = array_merge($icon_info, array('shadow' => $iconShadowImage,
                                                       'shadowWidth' => $_shadow_info[0],
                                                       'shadowHeight' => $_shadow_info[1]));
        }
        return $icon_info;
    }
    
    /**
     * set the default marker icon for ALL markers on the map
     * NOTE: This MUST be set prior to adding markers in order for the defaults
     * to be set correctly.
     * @param string $iconImage URL to icon image
     * @param string $iconShadowImage URL to shadow image
     * @param string $iconAnchorX X coordinate for icon anchor point
     * @param string $iconAnchorY Y coordinate for icon anchor point
     * @param string $infoWindowAnchorX X coordinate for info window anchor point
     * @param string $infoWindowAnchorY Y coordinate for info window anchor point
     * @return string A marker icon key.
     */
    function setMarkerIcon($iconImage,$iconShadowImage = '',$iconAnchorX = 'x',$iconAnchorY = 'x',$infoWindowAnchorX = 'x',$infoWindowAnchorY = 'x') {
        $this->default_icon = $iconImage;
        $this->default_icon_shadow = $iconShadowImage;
        return $this->setMarkerIconKey($iconImage,$iconShadowImage,$iconAnchorX,$iconAnchorY,$infoWindowAnchorX,$infoWindowAnchorY);       
    }
    
    /**
     * function to check if icon is in  class "marker_iconset", if it is, 
     * returns the key, if not, creates a new array indice and returns the key
     * @param string $iconImage URL to icon image
     * @param string $iconShadowImage URL to shadow image
     * @param string $iconAnchorX X coordinate for icon anchor point
     * @param string $iconAnchorY Y coordinate for icon anchor point
     * @param string $infoWindowAnchorX X coordinate for info window anchor point
     * @param string $infoWindowAnchorY Y coordinate for info window anchor point
     * @return string A marker icon key.
     */
    function setMarkerIconKey($iconImage,$iconShadow='',$iconAnchorX = 'x',$iconAnchorY = 'x',$infoWindowAnchorX = 'x',$infoWindowAnchorY = 'x'){
    	$_iconKey = $this->getIconKey($iconImage,$iconShadow);
    	if(isset($this->_marker_icons[$_iconKey])){
    		return $_iconKey;
    	}else{
    		return $this->addIcon($iconImage,$iconShadow,$iconAnchorX,$iconAnchorY,$infoWindowAnchorX,$infoWindowAnchorY);
    	}
    }
    
    /**
     * function to get icon key
     * @param string $iconImage URL to marker icon image
     * @param string $iconShadow URL to marker icon shadow image
     * @return string Returns formatted icon key from icon or icon+shadow image name pairs
     */
    function getIconKey($iconImage,$iconShadow=""){
        return str_replace(array("/",":","."),"",$iconImage.$iconShadow);
    }
 
    /**
     * add an icon to "iconset"
     * @param string $iconImage URL to marker icon image
     * @param string $iconShadow URL to marker icon shadow image
     * @param string $iconAnchorX X coordinate for icon anchor point
     * @param string $iconAnchorY Y coordinate for icon anchor point
     * @param string $infoWindowAnchorX X coordinate for info window anchor point
     * @param string $infoWindowAnchorY Y coordinate for info window anchor point
     * @return string Returns the icon's key.  
     */
    function addIcon($iconImage,$iconShadowImage = '',$iconAnchorX = 'x',$iconAnchorY = 'x',$infoWindowAnchorX = 'x',$infoWindowAnchorY = 'x') {
    	$_iconKey = $this->getIconKey($iconImage, $iconShadowImage);
        $this->_marker_icons[$_iconKey] = $this->createMarkerIcon($iconImage,$iconShadowImage,$iconAnchorX,$iconAnchorY,$infoWindowAnchorX,$infoWindowAnchorY);
        return $_iconKey;
    }
    
    /**
     * updates a marker's icon key.
     * NOTE: To be used in lieu of addMarkerIcon, now use addIcon + updateMarkerIconKey for explicit icon association
     * @param string $markerKey Marker key to define which marker's icon to update
     * @param string $iconKey Icon key to define which icon to use.
     */
    function updateMarkerIconKey($markerKey, $iconKey){
    	if(isset($this->_markers[$markerKey])){
    		$this->_markers[$markerKey]['icon_key'] = $iconKey;
    	}
    }    

    /**
     * print map header javascript (goes between <head></head>)
     * 
     */
    function printHeaderJS() {
        echo $this->getHeaderJS();
    }
    
    /**
     * return map header javascript (goes between <head></head>)
     * 
     */
    function getHeaderJS() {
		$_headerJS = "";
        if( $this->mobile == true){
        	$_headerJS .= "
        	   <meta name='viewport' content='initial-scale=1.0, user-scalable=no' />
        	";
        }
		if(!empty($this->_elevation_polylines)||(!empty($this->_directions)&&$this->elevation_directions)){
			$_headerJS .= "<script type='text/javascript' src='http://www.google.com/jsapi'></script>";
			$_headerJS .= "
			<script type='text/javascript'>
				// Load the Visualization API and the piechart package.
				google.load('visualization', '1', {packages: ['columnchart']});
			</script>";
		}
        $_headerJS .= "<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=".(($this->mobile==true)?"true":"false")."'></script>";
		if($this->marker_clusterer){
			$_headerJS .= "<script type='text/javascript' src='".$this->marker_clusterer_location."' ></script>";
		}        
        if($this->local_search){/*TODO: Load Local Search API V3 when available*/}   
        return $_headerJS;
    }    
    
   /**
    * prints onLoad() without having to manipulate body tag.
	* call this after the print map like so...    
	*      $map->printMap();    
	*      $map->printOnLoad();    
	*/
    function printOnLoad() {
        echo $this->getOnLoad();
    }
    
    /**
     * print onLoad function name
     */
    function printOnLoadFunction(){
        echo $this->getOnLoadFunction();
    }

    /**
     * return js to set onload function
     */
    function getOnLoad() {
        return '<script language="javascript" type="text/javascript" charset="utf-8">window.onload=onLoad'.$this->map_id.';</script>';                       
    }
    
    /**
     * return js to set onload function
     */
    function getOnLoadFunction() {
        return 'onLoad'.$this->map_id;                       
    }   

    /**
     * print map javascript (put just before </body>, or in <header> if using onLoad())
     * 
     */
    function printMapJS() {
        echo $this->getMapJS();
    }    

    /**
     * return map javascript
     * 
     */
    function getMapJS() {
		$_script = "";
		$_key = $this->map_id;
		$_output = '<script type="text/javascript" charset="utf-8">' . "\n";
        $_output .= '//<![CDATA[' . "\n";
        $_output .= "/*************************************************\n";
        $_output .= " * Created with GoogleMapAPI" . $this->_version . "\n";
        $_output .= " * Author: Brad Wedell <brad AT mycnl DOT com>\n";
        $_output .= " * Link http://code.google.com/p/phpgooglemapapiv3/\n";
        $_output .= " * Copyright 2010 Brad Wedell\n";
        $_output .= " * Original Author: Monte Ohrt <monte AT ohrt DOT com>\n";
        $_output .= " * Original Copyright 2005-2006 New Digital Group\n";
        $_output .= " * Originial Link http://www.phpinsider.com/php/code/GoogleMapAPI/\n";
        $_output .= " *************************************************/\n";
		if($this->street_view_dom_id!=""){
			$_script .= "
				var panorama".$this->street_view_dom_id."$_key = '';
			";
			if(!empty($this->_markers)){
				$_script .= "
					var panorama".$this->street_view_dom_id."markers$_key = [];
				";
			}
		}
		
		if(!empty($this->_markers)){
			$_script .= "
				var markers$_key  = [];
			";
			if($this->sidebar) {        
				$_script .= "
					var sidebar_html$_key  = '';
					var marker_html$_key  = [];
				";
			}
		}
		if($this->marker_clusterer){
			$_script .= "
			  var markerClusterer$_key = null;
			";
		}        
        if($this->directions) {        
            $_script .= "
                var to_htmls$_key  = [];
                var from_htmls$_key  = [];
            ";
        }
		if(!empty($this->_directions)){
			$_script .= "
			    var directions$_key = [];
			";
		}
		//Polylines
		if(!empty($this->_polylines)){
			$_script .= "
				var polylines$_key = [];
				var polylineCoords$_key = [];
			";
			if(!empty($this->_elevation_polylines)){
				$_script .= "
					var elevationPolylines$_key = [];
				";
			}
		}
		//Elevation stuff
		if(!empty($this->_elevation_polylines)||(!empty($this->_directions)&&$this->elevation_directions)){
			$_script .= "
				var elevationCharts$_key = [];				
			";
		}
		//Overlays
		if(!empty($this->_overlays)){
			$_script .= "
				var overlays$_key = [];
			";
		}
        //KML Overlays
        if(!empty($this->_kml_overlays)){
            $_script .= "
                var kml_overlays$_key = [];
            ";
        }
        //New Icons
        if(!empty($this->_marker_icons)){
        	$_script .= "var icon$_key  = []; \n";
        	foreach($this->_marker_icons as $icon_key=>$icon_info){
        		//no need to check icon key here since that's already done with setters
        		$_script .= "
        		  icon".$_key."['$icon_key'] = {};
        		  icon".$_key."['$icon_key'].image =  new google.maps.MarkerImage('".$icon_info["image"]."',
				      // The size
				      new google.maps.Size(".$icon_info['iconWidth'].", ".$icon_info['iconHeight']."),
				      // The origin(sprite)
				      new google.maps.Point(0,0),
				      // The anchor
				      new google.maps.Point(".$icon_info['iconAnchorX'].", ".$icon_info['iconAnchorY'].")
                  );
        		";
        		if(isset($icon_info['shadow']) && $icon_info['shadow']!=""){
        		  $_script .= "
                    icon".$_key."['$icon_key'].shadow = new google.maps.MarkerImage('".$icon_info["shadow"]."',
                      // The size
                      new google.maps.Size(".$icon_info['shadowWidth'].", ".$icon_info['shadowHeight']."),
                      // The origin(sprite)
                      new google.maps.Point(0,0),
                      // The anchor
                      new google.maps.Point(".$icon_info['iconAnchorX'].", ".$icon_info['iconAnchorY'].")
                    );
                  ";
        		}
        	}
        }
                           
        $_script .= "var map$_key = null;\n";
		
		//start setting script var
        if($this->onload) {
           $_script .= 'function onLoad'.$this->map_id.'() {' . "\n";   
        } 
                
        if(!empty($this->browser_alert)) {
        	//TODO:Update with new browser catch - GBrowserIsCompatible is deprecated
            //$_output .= 'if (GBrowserIsCompatible()) {' . "\n";
        }
        
        /*
         *TODO:Update with local search bar once implemented in V3 api 
		$strMapOptions = "";
		if($this->local_search){
			$_output .= "
				mapOptions.googleBarOptions= {
					style : 'new'
					".(($this->local_search_ads)?",
					adsOptions: {
					    client: '".$this->ads_pub_id."',
					    channel: '".$this->ads_channel."',
					    language: 'en'
					":"")."
				};
			";            
			$strMapOptions .= ", mapOptions";
		}
        */
		
		if($this->display_map){
			$_script .= sprintf('var mapObj%s = document.getElementById("%s");', $_key, $this->map_id) . "\n";
			$_script .= "if (mapObj$_key != 'undefined' && mapObj$_key != null) {\n";
			
			$_script .= "
				var mapOptions$_key = {
					zoom: ".$this->zoom.",
					mapTypeId: google.maps.MapTypeId.".$this->map_type.",
					mapTypeControl: ".($this->type_controls?"true":"false").",
					mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.".$this->type_controls_style."}
				};
			";
			if(isset($this->center_lat) && isset($this->center_lon)) {
				// Special care for decimal point in lon and lat, would get lost if "wrong" locale is set; applies to (s)printf only
				$_script .= "
					mapOptions".$_key.".center = new google.maps.LatLng(
						".number_format($this->center_lat, 6, ".", "").",
						".number_format($this->center_lon, 6, ".", "")."
					);
				";
			}   
			
			if($this->street_view_controls){
				$_script .= "
					mapOptions".$_key.".streetViewControl= true;
	
				";
			}
			
			$_script .= "
				map$_key = new google.maps.Map(mapObj$_key,mapOptions$_key);
			";
				
			if($this->street_view_dom_id!=""){
				$_script .= "
					panorama".$this->street_view_dom_id."$_key = new  google.maps.StreetViewPanorama(document.getElementById('".$this->street_view_dom_id."'));
					map$_key.setStreetView(panorama".$this->street_view_dom_id."$_key);
				";
				
				if(!empty($this->_markers)){
					
					//Add markers to the street view
					if($this->street_view_dom_id!=""){
						$_script .= $this->getAddMarkersJS($this->map_id, $pano=true);
					}
					//set center to last marker
					$last_id = count($this->_markers)-1;
					
					$_script .= "
						panorama".$this->street_view_dom_id."$_key.setPosition(new google.maps.LatLng(
							".$this->_markers[$last_id]["lat"].",
							".$this->_markers[$last_id]["lon"]."
						));
						panorama".$this->street_view_dom_id."$_key.setVisible(true);
					";
				}
			}
			
			if(!empty($this->_directions)){
				$_script .= $this->getAddDirectionsJS();
			}
			
			//TODO:add support for Google Earth Overlay once integrated with V3
			//$_output .= "map.addMapType(G_SATELLITE_3D_MAP);\n";
			
			// zoom so that all markers are in the viewport
			if($this->zoom_encompass && (count($this->_markers) > 1 || count($this->_polylines) >= 1 || count($this->_overlays) >= 1)) {
				// increase bounds by fudge factor to keep
				// markers away from the edges
				$_len_lon = $this->_max_lon - $this->_min_lon;
				$_len_lat = $this->_max_lat - $this->_min_lat;
				$this->_min_lon -= $_len_lon * $this->bounds_fudge;
				$this->_max_lon += $_len_lon * $this->bounds_fudge;
				$this->_min_lat -= $_len_lat * $this->bounds_fudge;
				$this->_max_lat += $_len_lat * $this->bounds_fudge;
	
				$_script .= "var bds$_key = new google.maps.LatLngBounds(new google.maps.LatLng($this->_min_lat, $this->_min_lon), new google.maps.LatLng($this->_max_lat, $this->_max_lon));\n";
				$_script .= 'map'.$_key.'.fitBounds(bds'.$_key.');' . "\n";
			}
			
			/*
			 * TODO: Update controls to use new API v3 methods.(Not a priority, see below)
			 * default V3 functionality caters control display according to the 
			 * device that's accessing the page, as well as the specified width
			 * and height of the map itself.
			if($this->map_controls) {
			  if($this->control_size == 'large')
				  $_output .= 'map.addControl(new GLargeMapControl(), new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(10,10)));' . "\n";
			  else
				  $_output .= 'map.addControl(new GSmallMapControl(), new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(10,60)));' . "\n";
			}
			if($this->type_controls) {
				$_output .= 'map.addControl(new GMapTypeControl(), new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(10,10)));' . "\n";
			}
			
			if($this->scale_control) {
				if($this->control_size == 'large'){
					$_output .= 'map.addControl(new GScaleControl(), new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(35,190)));' . "\n";
				}else {
					$_output .= 'map.addControl(new GScaleControl(), new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(190,10)));' . "\n";
				}
			}
			if($this->overview_control) {
				$_output .= 'map.addControl(new GOverviewMapControl());' . "\n";
			}
			
			 * TODO: Update with ads_manager stuff once integrated into V3
			if($this->ads_manager){
				$_output .= 'var adsManager = new GAdsManager(map, "'.$this->ads_pub_id.'",{channel:"'.$this->ads_channel.'",maxAdsOnMap:"'.$this->ads_max.'"});
		   adsManager.enable();'."\n";
			
			}
			 * TODO: Update with local search once integrated into V3
			if($this->local_search){
				$_output .= "\n
					map.enableGoogleBar();
				";
			}
			*/
			
			if($this->traffic_overlay){
				$_script .= "
					var trafficLayer = new google.maps.TrafficLayer();
					trafficLayer.setMap(map$_key);
				";            
			}
			
			if($this->biking_overlay){
				$_script .= "
					var bikingLayer = new google.maps.BicyclingLayer();
					bikingLayer.setMap(map$_key);
				";            
			}
			
			$_script .= $this->getAddMarkersJS();
			
			$_script .= $this->getPolylineJS();
			$_script .= $this->getAddOverlayJS();
			
			if($this->_kml_overlays!==""){
			  foreach($this->_kml_overlays as $_kml_key=>$_kml_file){
				  $_script .= "
					  kml_overlays$_key[$_kml_key]= new google.maps.KmlLayer('$_kml_file');
					  kml_overlays$_key[$_kml_key].setMap(map$_key);
				  ";
			  }
			  $_script .= "
				  
			  ";
			}

			 //end JS if mapObj != "undefined" block
			$_script .= '}' . "\n";
		
		}//end if $this->display_map==true
       
        if(!empty($this->browser_alert)) {
            //TODO:Update with new browser catch SEE ABOVE
           // $_output .= '} else {' . "\n";
           // $_output .= 'alert("' . str_replace('"','\"',$this->browser_alert) . '");' . "\n";
           // $_output .= '}' . "\n";
        }                        

        if($this->onload) {
           $_script .= '}' . "\n";
        }
        
		$_script .= $this->getMapFunctions();
        
		if($this->_minify_js && class_exists("JSMin")){
			$_script = JSMin::minify($_script);
		}
		
		//Append script to output
		$_output .= $_script;
		$_output .= '//]]>' . "\n";
        $_output .= '</script>' . "\n";
        return $_output;
    }
	
	/**
	 * function to render utility functions for use on the page
	 */
	 function getMapFunctions(){
		$_script = "";
		if($this->_display_js_functions===true){
	    	$_script = $this->getUtilityFunctions();           
        }
		return $_script;
	 }
	 
	 function getUtilityFunctions(){
		 $_script = "";
		 if(!empty($this->_markers))
		 	$_script .= $this->getCreateMarkerJS();
		 if(!empty($this->_overlays))
		 	$_script .= $this->getCreateOverlayJS();
		 if(!empty($this->_elevation_polylines)||(!empty($this->_directions)&&$this->elevation_directions))
		 	$_script .= $this->getPlotElevationJS();
		 // Utility functions used to distinguish between tabbed and non-tabbed info windows
		 $_script .= 'function isArray(a) {return isObject(a) && a.constructor == Array;}' . "\n";
		 $_script .= 'function isObject(a) {return (a && typeof a == \'object\') || isFunction(a);}' . "\n";
		 $_script .= 'function isFunction(a) {return typeof a == \'function\';}' . "\n";
		 $_script .= 'function isEmpty(obj) { for(var i in obj) { return false; } return true; }'."\n";	 
		 return $_script;
	 }

    /**
     * overridable function for generating js to add markers
     */
    function getAddMarkersJS($map_id = "", $pano= false) {
		//defaults
		if($map_id == ""){
			$map_id = $this->map_id;
		}
		
		if($pano==false){
			$_prefix = "map";
		}else{
			$_prefix = "panorama".$this->street_view_dom_id;
		}
        $_output = '';
        foreach($this->_markers as $_marker) {
            $iw_html = str_replace('"','\"',str_replace(array("\n", "\r"), "", $_marker['html']));
            $_output .= "var point = new google.maps.LatLng(".$_marker['lat'].",".$_marker['lon'].");\n";
            $_output .= sprintf('%s.push(createMarker(%s%s, point,"%s","%s", %s, %s, "%s", %s ));',
				(($pano==true)?$_prefix:"")."markers".$map_id,
				$_prefix,                
				$map_id,
				str_replace('"','\"',$_marker['title']),
				str_replace('/','\/',$iw_html),
				(isset($_marker["icon_key"]))?"icon".$map_id."['".$_marker["icon_key"]."'].image":"''",
				(isset($_marker["icon_key"])&&isset($_marker["shadow_icon"]))?"icon".$map_id."['".$_marker["icon_key"]."'].shadow":"''",
				(($this->sidebar)?$this->sidebar_id:""),
				((isset($_marker["openers"])&&count($_marker["openers"])>0)?json_encode($_marker["openers"]):"''")
            ) . "\n";
        }
        
        if($this->marker_clusterer && $pano==false){//only do marker clusterer for map, not streetview
        	$_output .= "
        	   markerClusterer".$map_id." = new MarkerClusterer(".$_prefix.$map_id.", markers".$map_id.", {
		          maxZoom: ".$this->marker_clusterer_options["maxZoom"].",
		          gridSize: ".$this->marker_clusterer_options["gridSize"].",
		          styles: ".$this->marker_clusterer_options["styles"]."
		        });
		        	
        	";
        }
        return $_output;
    }

    /**
     * overridable function to generate polyline js - for now can only be used on a map, not a streetview
     */
    function getPolylineJS() {
        $_output = '';
        foreach($this->_polylines as $polyline_id =>$_polyline) {
        	$_coords_output = "";
        	foreach($_polyline["coords"] as $_coords){
				if($_coords_output != ""){$_coords_output.=",";}
        		$_coords_output .= "
        		    new google.maps.LatLng(".$_coords["lat"].", ".$_coords["long"].")
        		";
        	}
        	$_output .= "
        	   polylineCoords".$this->map_id."[$polyline_id] = [".$_coords_output."];    	
			   polylines".$this->map_id."[$polyline_id] = new google.maps.Polyline({
				  path: polylineCoords".$this->map_id."[$polyline_id]
				  ".(($_polyline['color']!="")?", strokeColor: '".$_polyline['color']."'":"")."
				  ".(($_polyline['opacity']!=0)?", strokeOpacity: ".$_polyline['opacity']."":"")."
				  ".(($_polyline['weight']!=0)?", strokeWeight: ".$_polyline['weight']."":"")."
			  });			
			  polylines".$this->map_id."[$polyline_id].setMap(map".$this->map_id.");
        	";
			
			//Elevation profiles
			if(!empty($this->_elevation_polylines) && isset($this->_elevation_polylines[$polyline_id])){
				$elevation_dom_id=$this->_elevation_polylines[$polyline_id]["dom_id"];
				$width = $this->_elevation_polylines[$polyline_id]["width"];
				$height = $this->_elevation_polylines[$polyline_id]["height"];
				$samples = $this->_elevation_polylines[$polyline_id]["samples"];
				$focus_color = $this->_elevation_polylines[$polyline_id]["focus_color"];
				$_output .= "
					elevationPolylines".$this->map_id."[$polyline_id] = {
						'selector':'$elevation_dom_id',
						'chart': new google.visualization.ColumnChart(document.getElementById('$elevation_dom_id')),
						'service': new google.maps.ElevationService(),
						'width':$width,
						'height':$height,
						'focusColor':'$focus_color',
						'marker':null
					};
					elevationPolylines".$this->map_id."[$polyline_id]['service'].getElevationAlongPath({
						path: polylineCoords".$this->map_id."[$polyline_id],
						samples: $samples
					}, function(results,status){plotElevation(results,status, elevationPolylines".$this->map_id."[$polyline_id], map".$this->map_id.", elevationCharts".$this->map_id.");});
				";
			}
		}
        return $_output;
    }
	
	/**
	 * function to render proper calls for directions - for now can only be used on a map, not a streetview
	 */
	 function getAddDirectionsJS(){
		$_output = ""; 
		 
		foreach($this->_directions as $directions){
			$dom_id = $directions["dom_id"];			
			$travelModeParams = array();
			$directionsParams = "";         
			if($this->walking_directions==TRUE)
				$directionsParams .= ", \n travelMode:google.maps.DirectionsTravelMode.WALKING";
			else if($this->biking_directions==TRUE)
				$directionsParams .= ", \n travelMode:google.maps.DirectionsTravelMode.BICYCLING";
			else
				$directionsParams .= ", \n travelMode:google.maps.DirectionsTravelMode.DRIVING";
		   
			if($this->avoid_highways==TRUE)
			   $directionsParams .= ", \n avoidHighways: true";
			if($this->avoid_tollways==TRUE)
			   $directionsParams .= ", \n avoidTolls: true";			
			
			$_output .= "
			    directions".$this->map_id."['$dom_id'] = {
					displayRenderer:new google.maps.DirectionsRenderer(),
					directionService:new google.maps.DirectionsService(),
					request:{
						origin: '".$directions["start"]."',
						destination: '".$directions["dest"]."'
						$directionsParams
					}
					".(($this->elevation_directions)?",		
					   selector: '".$directions["elevation_dom_id"]."',
					   chart: new google.visualization.ColumnChart(document.getElementById('".$directions["elevation_dom_id"]."')),
					   service: new google.maps.ElevationService(),
					   width:".$directions["width"].",
					   height:".$directions["height"].",
					   focusColor:'#00FF00',
					   marker:null
				   ":"")."
				};
				directions".$this->map_id."['$dom_id'].displayRenderer.setMap(map".$this->map_id.");
				directions".$this->map_id."['$dom_id'].displayRenderer.setPanel(document.getElementById('$dom_id'));
				directions".$this->map_id."['$dom_id'].directionService.route(directions".$this->map_id."['$dom_id'].request, function(response, status) {
					if (status == google.maps.DirectionsStatus.OK) {
					   directions".$this->map_id."['$dom_id'].displayRenderer.setDirections(response);
					   ".(($this->elevation_directions)?"
						   directions".$this->map_id."['$dom_id'].service.getElevationAlongPath({
							   path: response.routes[0].overview_path,
							   samples: ".$directions["elevation_samples"]."
						   }, function(results,status){plotElevation(results,status, directions".$this->map_id."['$dom_id'], map".$this->map_id.", elevationCharts".$this->map_id.");});
					   ":"")."
					}
				});
			 ";
		 }
		 
		 return $_output;
	 }
	
	/**
	 * function to get overlay creation JS.
	 */
	 function getAddOverlayJS(){
		 $_output = "";
		 foreach($this->_overlays as $_key=>$_overlay){
			 $_output .= "
			 	 var bounds = new google.maps.LatLngBounds(new google.maps.LatLng(".$_overlay["bounds"]["ne"]["lat"].", ".$_overlay["bounds"]["ne"]["long"]."), new google.maps.LatLng(".$_overlay["bounds"]["sw"]["lat"].", ".$_overlay["bounds"]["sw"]["long"]."));
				 var image = '".$_overlay["img"]."';
			     overlays".$this->map_id."[$_key] = new CustomOverlay(bounds, image, map".$this->map_id.", ".$_overlay["opacity"].");
			 ";
		 }
		 return $_output;
	 }

    /**
     * overridable function to generate the js for the js function for creating a marker.
     */
    function getCreateMarkerJS() {
    	$_output = "
    	   function createMarker(map, point, title, html, icon, icon_shadow, sidebar_id, openers){
			    var marker_options = {
			        position: point,
			        map: map,
			        title: title};  
			    if(icon!=''){marker_options.icon = icon;}
			    if(icon_shadow!=''){marker_options.icon_shadow = icon_shadow;}
			    //create marker
			    var new_marker = new google.maps.Marker(marker_options);
			    if(html!=''){
					".(($this->info_window)?"
			        var infowindow = new google.maps.InfoWindow({content: html});
			        google.maps.event.addListener(new_marker, '".$this->window_trigger."', function() {
			          infowindow.open(map,new_marker);
			        });
					if(openers != ''&&!isEmpty(openers)){
			           for(var i in openers){
			             var opener = document.getElementById(openers[i]);
			             opener.on".$this->window_trigger." = function(){infowindow.open(map,new_marker); return false};
			           }
			        }
					":"")."
			        if(sidebar_id != ''){
			            var sidebar = document.getElementById(sidebar_id);
						if(sidebar!=null && sidebar!=undefined && title!=null && title!=''){
							var newlink = document.createElement('a');
							".(($this->info_window)?"
			        		newlink.onclick=function(){infowindow.open(map,new_marker); return false};
							":"
							newlink.onclick=function(){map.setCenter(point); return false};
							")."
							newlink.innerHTML = title;
							sidebar.appendChild(newlink);
						}
			        }
                }
			    return new_marker;  
			}
    	";
    	return $_output;
    }
	
	/**
	 * Get create overlay js
	 */
	 function getCreateOverlayJS(){
		 $_output = "
		 	CustomOverlay.prototype = new google.maps.OverlayView();	
			function CustomOverlay(bounds, image, map, opacity){
				this.bounds_ = bounds;
				this.image_ = image;
				this.map_ = map;
				this.div_ = null;
				this.opacity = (opacity!='')?opacity:10;
				this.setMap(map);
			}
			CustomOverlay.prototype.onAdd = function() {
				var div = document.createElement('DIV');
				div.style.borderStyle = 'none';
				div.style.borderWidth = '0px';
				div.style.position = 'absolute';
				var img = document.createElement('img');
				img.src = this.image_;
				img.style.width = '100%';
				img.style.height = '100%';
				img.style.opacity = this.opacity/10;
				img.style.filter = 'alpha(opacity='+this.opacity*10+')';	
				div.appendChild(img);
				this.div_ = div;	
				var panes = this.getPanes();
				panes.overlayImage.appendChild(div);
			}
			CustomOverlay.prototype.draw = function() {
				var overlayProjection = this.getProjection();
				var sw = overlayProjection.fromLatLngToDivPixel(this.bounds_.getSouthWest());
				var ne = overlayProjection.fromLatLngToDivPixel(this.bounds_.getNorthEast());
				var div = this.div_;
				div.style.left = sw.x + 'px';
				div.style.top = ne.y + 'px';
				div.style.width = (ne.x - sw.x) + 'px';
				div.style.height = (sw.y - ne.y) + 'px';
			}
			CustomOverlay.prototype.onRemove = function() {
				this.div_.parentNode.removeChild(this.div_);
				this.div_ = null;
			}
		 ";		 
		 return $_output;
	 }
	 
	 /**
	  * print helper function to draw elevation results as a chart
	  */
	function getPlotElevationJS(){
		$_output = "
			function plotElevation(results, status, elevation_data, map, charts_array) {
				charts_array[elevation_data.selector] = {
					results:results,
					data:new google.visualization.DataTable()
				};
				charts_array[elevation_data.selector].data.addColumn('string', 'Sample');
				charts_array[elevation_data.selector].data.addColumn('number', 'Elevation');
				for (var i = 0; i < charts_array[elevation_data.selector].results.length; i++) {
				  charts_array[elevation_data.selector].data.addRow(['', charts_array[elevation_data.selector].results[i].elevation]);
				}
				document.getElementById(elevation_data.selector).style.display = 'block';
				elevation_data.chart.draw(charts_array[elevation_data.selector].data, {
				  width: elevation_data.width,
				  height: elevation_data.height,
				  legend: 'none',
				  titleY: 'Elevation (m)',
				  focusBorderColor: elevation_data.focusColor
				});
		";
		if($this->elevation_markers){
			$_output .= $this->getElevationMarkerJS();			
		}
		$_output .= "}";
		return $_output;
	}	  
	
	/**
	 * create JS that is inside of JS plot elevation function
	 */
	 function getElevationMarkerJS(){
		 $_output = "
			google.visualization.events.addListener(elevation_data.chart, 'onmouseover', function(e) {
				if(elevation_data.marker==null){
					elevation_data.marker = new google.maps.Marker({
					  position: charts_array[elevation_data.selector].results[e.row].location,
					  map: map,
					  icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
					});
				}else{
					elevation_data.marker.setPosition(charts_array[elevation_data.selector].results[e.row].location);
				}
				map.setCenter(charts_array[elevation_data.selector].results[e.row].location);
			});
			document.getElementById(elevation_data.selector).onmouseout = function(){
				elevation_data.marker = clearElevationMarker(elevation_data.marker);
			};
			function clearElevationMarker(marker){
			  if(marker!=null){
				  marker.setMap(null);
				  return null;
			  }
			}
		";
		return $_output;
	 }
	 

    /**
     * print map (put at location map will appear)
     * 
     */
    function printMap() {
        echo $this->getMap();
    }

    /**
     * return map
     * 
     */
    function getMap() {
        $_output = '<script type="text/javascript" charset="utf-8">' . "\n" . '//<![CDATA[' . "\n";
        //$_output .= 'if (GBrowserIsCompatible()) {' . "\n";
        if(strlen($this->width) > 0 && strlen($this->height) > 0) {
            $_output .= sprintf('document.write(\'<div id="%s" style="width: %s; height: %s; position:relative;"><\/div>\');',$this->map_id,$this->width,$this->height) . "\n";
        } else {
            $_output .= sprintf('document.write(\'<div id="%s" style="position:relative;"><\/div>\');',$this->map_id) . "\n";     
        }
        //$_output .= '}';

        //if(!empty($this->js_alert)) {
        //    $_output .= ' else {' . "\n";
        //    $_output .= sprintf('document.write(\'%s\');', str_replace('/','\/',$this->js_alert)) . "\n";
        //    $_output .= '}' . "\n";
        //}

        $_output .= '//]]>' . "\n" . '</script>' . "\n";

        if(!empty($this->js_alert)) {
            $_output .= '<noscript>' . $this->js_alert . '</noscript>' . "\n";
        }

        return $_output;
    }

    
    /**
     * print sidebar (put at location sidebar will appear)
     * 
     */
    function printSidebar() {
        echo $this->getSidebar();
    }    

    /**
     * return sidebar html
     * 
     */
    function getSidebar() {
        return sprintf('<div id="%s" class="'.$this->sidebar_id.'"></div>',$this->sidebar_id) . "\n";
    }    
            
    /**
     * get the geocode lat/lon points from given address
     * look in cache first, otherwise get from Yahoo
     * 
     * @param string $address
     * @return array GeoCode information
     */
    function getGeocode($address) {
        if(empty($address))
            return false;
        $_geocode = false;  
        if(($_geocode = $this->getCache($address)) === false) {
        	if(($_geocode = $this->geoGetCoords($address)) !== false) {
            	$this->putCache($address, $_geocode['lon'], $_geocode['lat']);
            }
        }
        return $_geocode;
    }
   
    /**
     * get the geocode lat/lon points from cache for given address
     * 
     * @param string $address
     * @return bool|array False if no cache, array of data if has cache
     */
    function getCache($address) {
        if(!isset($this->dsn))
            return false;
        
        $_ret = array();
        
        // PEAR DB
        require_once('DB.php');          
        $_db =& DB::connect($this->dsn);
        if (PEAR::isError($_db)) {
            die($_db->getMessage());
        }
        $_res =& $_db->query("SELECT lon,lat FROM {$this->_db_cache_table} where address = ?", $address);
        if (PEAR::isError($_res)) {
            die($_res->getMessage());
        }
        if($_row = $_res->fetchRow()) {            
            $_ret['lon'] = $_row[0];
            $_ret['lat'] = $_row[1];
        }
        
        $_db->disconnect();
        
        return !empty($_ret) ? $_ret : false;
    }
    
    /**
     * put the geocode lat/lon points into cache for given address
     * 
     * @param string $address
     * @param string $lon the map latitude (horizontal)
     * @param string $lat the map latitude (vertical)
     * @return bool Status of put cache request
     */
    function putCache($address, $lon, $lat) {
        if(!isset($this->dsn) || (strlen($address) == 0 || strlen($lon) == 0 || strlen($lat) == 0))
           return false;
        // PEAR DB
        require_once('DB.php');         
        $_db =& DB::connect($this->dsn);
        if (PEAR::isError($_db)) {
        	die($_db->getMessage());
        }
        $_res =& $_db->query('insert into '.$this->_db_cache_table.' values (?, ?, ?)', array($address, $lon, $lat));
        if (PEAR::isError($_res)) {
            die($_res->getMessage());
        }
        $_db->disconnect();
        return true;        
    }
   
    /**
     * get geocode lat/lon points for given address from Yahoo
     * 
     * @param string $address
     * @return bool|array false if can't be geocoded, array or geocdoes if successful
     */
    function geoGetCoords($address,$depth=0) {
        switch($this->lookup_service) {
            case 'GOOGLE':
                $_url = sprintf('http://%s/maps/api/geocode/json?sensor=%s&address=%s',$this->lookup_server['GOOGLE'], $this->mobile==true?"true":"false", rawurlencode($address));
                $_result = false;
                if($_result = $this->fetchURL($_url)) {
                    $_result_parts = json_decode($_result);
                    if($_result_parts->status!="OK"){
                    	return false;
                    }
                    $_coords['lat'] = $_result_parts->results[0]->geometry->location->lat;
                    $_coords['lon'] = $_result_parts->results[0]->geometry->location->lng;
                }
                break;
            case 'YAHOO':
            default:        
                $_url = 'http://%s/MapsService/V1/geocode';
                $_url .= sprintf('?appid=%s&location=%s',$this->lookup_server['YAHOO'],$this->app_id,rawurlencode($address));
                $_result = false;
                if($_result = $this->fetchURL($_url)) {
                    preg_match('!<Latitude>(.*)</Latitude><Longitude>(.*)</Longitude>!U', $_result, $_match);
                    $_coords['lon'] = $_match[2];
                    $_coords['lat'] = $_match[1];
                }
                break;
        }         
        return $_coords;       
    }
    
	/**
     * get full geocode information for given address from Google
	 * NOTE: This does not use the getCache function as there is 
	 * a lot of data in a full geocode response to cache.
     * 
     * @param string $address
     * @return bool|array false if can't be geocoded, array or geocdoes if successful
     */
    function geoGetCoordsFull($address,$depth=0) {
        switch($this->lookup_service) {
            case 'GOOGLE':
                $_url = sprintf('http://%s/maps/api/geocode/json?sensor=%s&address=%s',$this->lookup_server['GOOGLE'], $this->mobile==true?"true":"false", rawurlencode($address));
                $_result = false;
                if($_result = $this->fetchURL($_url)) {
                    return json_decode($_result);
                }
                break;
            case 'YAHOO':
            default:        
                $_url = 'http://%s/MapsService/V1/geocode';
                $_url .= sprintf('?appid=%s&location=%s',$this->lookup_server['YAHOO'],$this->app_id,rawurlencode($address));
                $_result = false;
                if($_result = $this->fetchURL($_url)) {
                    return $_match;
                }
                break;
        }         
    }    

    /**
     * fetch a URL. Override this method to change the way URLs are fetched.
     * 
     * @param string $url
     */
    function fetchURL($url) {

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_URL, $url);
	$url = curl_exec($curl);
	curl_close($curl);

	return($url);

    }

    /**
     * get distance between to geocoords using great circle distance formula
     * 
     * @param float $lat1
     * @param float $lat2
     * @param float $lon1
     * @param float $lon2
     * @param float $unit   M=miles, K=kilometers, N=nautical miles, I=inches, F=feet
     * @return float
     */
    function geoGetDistance($lat1,$lon1,$lat2,$lon2,$unit='M') {
        
      // calculate miles
      $M =  69.09 * rad2deg(acos(sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon1 - $lon2)))); 

      switch(strtoupper($unit))
      {
        case 'K':
          // kilometers
          return $M * 1.609344;
          break;
        case 'N':
          // nautical miles
          return $M * 0.868976242;
          break;
        case 'F':
          // feet
          return $M * 5280;
          break;            
        case 'I':
          // inches
          return $M * 63360;
          break;            
        case 'M':
        default:
          // miles
          return $M;
          break;
      }
      
    }    
    
}

?>