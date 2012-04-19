<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Map helper class
 *
 * Portions of this class credited to: zzolo, phayes, tmcw, brynbellomy, bdragon
 *
 * @package    Map
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */


class map_Core {

	/**
	 * Generate the Javascript for Each Layer
	 * .. new OpenLayers.Layer.XXXX
	 * if $all is set to TRUE all maps are rendered
	 * **caveat is that each mapping api js must be loaded**
	 *
	 * @param   bool  $all
	 * @return  string $js
	 */
	public static function layers_js($all = FALSE)
	{
		// Javascript
		$js = "";

		// Get All Layers
		$layers = map::base();

		// Next get the default base layer
		$default_map = Kohana::config('settings.default_map');

		if ( ! isset($layers[$default_map]))
		{ // Map Layer Doesn't Exist - default to google
			$default_map = "osm_mapnik";
		}

		// Get OpenLayers type
		$openlayers_type = $layers[$default_map]->openlayers;
		foreach ($layers as $layer)
		{
			if ($layer->active)
			{
				if ($all == TRUE)
				{
					$js .= "var ".$layer->name." = new OpenLayers.Layer.".$layer->openlayers."(\"".$layer->title."\", ";

					if($layer->openlayers == 'XYZ')
					{
						if(isset($layer->data['url']))
						{
							$js .= '"'.$layer->data['url'].'", ';
						}
					}

					$js .= "{ \n";
					foreach ($layer->data AS $key => $value)
					{
						if ( ! empty($value)
						 	AND $key != 'baselayer'
						 	AND ($key == 'attribution' AND $layer->openlayers == 'XYZ')
							AND $key != 'url')
						{
							if ($key == "type")
							{
								$js .= " ".$key.": ".urlencode($value).",\n";
							}
							else
							{
								$js .= " ".$key.": '".urlencode($value)."',\n";
							}
						}
					}

					$js .= " sphericalMercator: true,\n";
					$js .= " maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)});\n\n";
				}
				else
				{
					if ($layer->openlayers == $openlayers_type)
					{
						$js .= "var ".$layer->name." = new OpenLayers.Layer.".$layer->openlayers."(\"".$layer->title."\", ";

						if($layer->openlayers == 'XYZ')
						{
							if(isset($layer->data['url']))
							{
								$js .= '"'.$layer->data['url'].'", ';
							}
						}

						$js .= "{ \n";

						foreach ($layer->data AS $key => $value)
						{
							if ( ! empty($value)
							 	AND $key != 'baselayer'
							 	AND ($key == 'attribution' AND $layer->openlayers == 'XYZ')
								AND $key != 'url')
							{
								if ($key == "type")
								{
									$js .= " ".$key.": ".urlencode($value).",\n";
								}
								else
								{
									$js .= " ".$key.": '".urlencode($value)."',\n";
								}
							}
						}

						$js .= " sphericalMercator: true,\n";
						$js .= " maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)});\n\n";
					}
				}
			}
		}

		// Hack on XYZ / Esri Attribution layer here since XYZ doesn't support images in attribution
		if (stripos($default_map,'esri_') !== FALSE AND $all == FALSE)
		{
			$js .= "$('div#map').append('<div style=\"position:absolute;right:0;z-index:1000;margin: -40px 10px 0 90px;\"><img src=\"http://www.arcgis.com/home/images/map/logo-sm.png\" style=\"float:right;\"/><small>Sources: Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, IGN, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community</small></div>');";
		}

		return $js;
	}

	/**
	 * Generate the Map Array.
	 * These are the maps that show up in the Layer Switcher
	 * if $all is set to TRUE all maps are rendered
	 * **caveat is that each mapping api js must be loaded **
	 *
	 * @param   bool  $all
	 * @return  string $js
	 */
	public static function layers_array($all = FALSE)
	{
		// Javascript
		$js = "[";

		// Get All Layers
		$layers = map::base();

		// Next get the default base layer
		$default_map = Kohana::config('settings.default_map');

		if ( ! isset($layers[$default_map]))
		{ // Map Layer Doesn't Exist - default to google
			$default_map = "google_normal";
		}

		// Get openlayers type
		$openlayers_type = $layers[$default_map]->openlayers;
		$js .= $default_map;
		foreach ($layers as $layer)
		{
			if ($layer->name != $default_map AND $layer->active)
			{
				if ($all == TRUE)
				{
					$js .= ",".$layer->name;
				}
				else
				{
					if ($layer->openlayers == $openlayers_type)
					{
						$js .= ",".$layer->name;
					}
				}
			}
		}
		$js .= "]";

		return $js;
	}

	/**
	 * Generate the Map Base Layer Object
	 * If a layer name is passed, it will return only the object
	 * for that layer
	 *
	 * @author
	 * @param   string  $layer_name
	 * @return  string $js
	 */
	public static function base($layer_name = NULL)
	{
		$layers = array();

		// Esri Topo
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'esri_topo';
		$layer->openlayers = "XYZ";
		$layer->title = 'Esri World Topo Map';
		$layer->description = 'This world topographic map (aka "the community basemap") includes boundaries, cities, water features, physiographic features, parks, landmarks, transportation, and buildings.';
		$layer->api_url = '';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '',
			'url' => 'http://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/${z}/${y}/${x}',
			'type' => ''
		);
		$layers[$layer->name] = $layer;

		// Esri Street Map
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'esri_street';
		$layer->openlayers = "XYZ";
		$layer->title = 'Esri Street Map';
		$layer->description = 'This map service presents highway-level data for the world and street-level data for North America, Europe, Southern Africa, parts of Asia, and more.';
		$layer->api_url = '';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '',
			'url' => 'http://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/${z}/${y}/${x}',
			'type' => ''
		);
		$layers[$layer->name] = $layer;

		// Esri Imagery
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'esri_imagery';
		$layer->openlayers = "XYZ";
		$layer->title = 'Esri Imagery Map';
		$layer->description = 'This map service presents satellite imagery for the world and high-resolution imagery for the United States, Great Britain, and hundreds of cities around the world.';
		$layer->api_url = '';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '',
			'url' => 'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/${z}/${y}/${x}',
			'type' => ''
		);
		$layers[$layer->name] = $layer;

		// Esri National Geographic
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'esri_natgeo';
		$layer->openlayers = "XYZ";
		$layer->title = 'Esri National Geographic Map';
		$layer->description = 'This map is designed to be used as a general reference map for informational and educational purposes as well as a basemap by GIS professionals and other users for creating web maps and web mapping applications.';
		$layer->api_url = '';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '',
			'url' => 'http://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/${z}/${y}/${x}',
			'type' => ''
		);
		$layers[$layer->name] = $layer;

		// Google Satellite
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'google_satellite';
		$layer->openlayers = "Google";
		$layer->title = 'Google Maps Satellite';
		$layer->description = 'Google Maps Satellite Imagery.';
		$layer->api_url = 'https://maps.google.com/maps/api/js?v=3.2&amp;sensor=false';
		$layer->api_signup = 'http://code.google.com/apis/maps/signup.html';
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'google.maps.MapTypeId.SATELLITE',
		);
		$layers[$layer->name] = $layer;

		// Google Hybrid
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'google_hybrid';
		$layer->openlayers = "Google";
		$layer->title = 'Google Maps Hybrid';
		$layer->description = 'Google Maps with roads and terrain.';
		$layer->api_url = 'https://maps.google.com/maps/api/js?v=3.2&amp;sensor=false';
		$layer->api_signup = 'http://code.google.com/apis/maps/signup.html';
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'google.maps.MapTypeId.HYBRID',
		);
		$layers[$layer->name] = $layer;

		// Google Normal
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'google_normal';
		$layer->openlayers = "Google";
		$layer->title = 'Google Maps Normal';
		$layer->description = 'Standard Google Maps Roads';
		$layer->api_url = 'https://maps.google.com/maps/api/js?v=3.2&amp;sensor=false';
		$layer->api_signup = 'http://code.google.com/apis/maps/signup.html';
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => '',
		);
		$layers[$layer->name] = $layer;

		// Google Physical
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'google_physical';
		$layer->openlayers = "Google";
		$layer->title = 'Google Maps Physical';
		$layer->description = 'Google Maps Hillshades';
		$layer->api_url = 'https://maps.google.com/maps/api/js?v=3.2&amp;sensor=false';
		$layer->api_signup = 'http://code.google.com/apis/maps/signup.html';
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'google.maps.MapTypeId.TERRAIN',
		);
		$layers[$layer->name] = $layer;

		// Bing Street
		$layer = new stdClass();
		$layer->active = FALSE;
		$layer->name = 'virtualearth_street';
		$layer->openlayers = "VirtualEarth";
		$layer->title = 'Bing Street';
		$layer->description = 'Bing Street Tiles.';
		$layer->api_url = 'https://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6';
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'VEMapStyle.Road',
		);
		$layers[$layer->name] = $layer;

		// Bing Satellite
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'virtualearth_satellite';
		$layer->openlayers = "VirtualEarth";
		$layer->title = 'Bing Satellite';
		$layer->description = 'Bing Satellite Tiles.';
		$layer->api_url = 'https://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6';
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'VEMapStyle.Aerial',
		);
		$layers[$layer->name] = $layer;

		// Bing Hybrid
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'virtualearth_hybrid';
		$layer->openlayers = "VirtualEarth";
		$layer->title = 'Bing Hybrid';
		$layer->description = 'Bing hybrid of streets and satellite tiles.';
		$layer->api_url = 'https://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6';
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'VEMapStyle.Hybrid',
		);
		$layers[$layer->name] = $layer;

		// OpenStreetMap Mapnik
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'osm_mapnik';
		$layer->openlayers = "OSM.Mapnik";
		$layer->title = 'OSM Mapnik';
		$layer->description = 'The main OpenStreetMap map';
		$layer->api_url = 'https://www.openstreetmap.org/openlayers/OpenStreetMap.js';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '&copy;<a href="@ccbysa">CCBYSA</a> 2010
				<a href="@openstreetmap">OpenStreetMap.org</a> contributors',
			'url' => 'http://tile.openstreetmap.org/${z}/${x}/${y}.png',
			'type' => ''
		);
		$layers[$layer->name] = $layer;

		// OpenStreetMap Tiles @ Home
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'osm_tah';
		$layer->openlayers = "OSM.Mapnik";
		$layer->title = 'OSM Tiles@Home';
		$layer->description = 'Alternative, community-rendered OpenStreetMap';
		$layer->api_url = 'https://www.openstreetmap.org/openlayers/OpenStreetMap.js';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '&copy;<a href="@ccbysa">CCBYSA</a> 2010
				<a href="@openstreetmap">OpenStreetMap.org</a> contributors',
			'url' => 'http://tah.openstreetmap.org/Tiles/tile/${z}/${x}/${y}.png',
			'type' => ''
		);
		$layers[$layer->name] = $layer;

		// OpenStreetMap Cycling Map
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'osm_cycle';
		$layer->openlayers = "OSM.Mapnik";
		$layer->title = 'OSM Cycling Map';
		$layer->description = 'OpenStreetMap with highlighted bike lanes';
		$layer->api_url = 'https://www.openstreetmap.org/openlayers/OpenStreetMap.js';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '&copy;<a href="@ccbysa">CCBYSA</a> 2010
				<a href="@openstreetmap">OpenStreetMap.org</a> contributors',
			'url' => 'http://andy.sandbox.cloudmade.com/tiles/cycle/${z}/${x}/${y}.png',
			'type' => ''
		);
		$layers[$layer->name] = $layer;

		// OpenStreetMap 426 hybrid overlay
		$layer = new stdClass();
		$layer->active = FALSE;
		$layer->name = 'osm_4326_hybrid';
		$layer->openlayers = "OSM.Mapnik";
		$layer->title = 'OSM Overlay';
		$layer->description = 'Semi-transparent hybrid overlay. Projected into
			WSG84 for use on non spherical-mercator maps.';
		$layer->api_url = 'https://www.openstreetmap.org/openlayers/OpenStreetMap.js';
		$layer->data = array(
			'baselayer' => FALSE,
			'attribution' => '&copy;<a href="@ccbysa">CCBYSA</a> 2010
				<a href="@openstreetmap">OpenStreetMap.org</a> contributors',
			'url' => 'http://oam.hypercube.telascience.org/tiles',
			'params' => array(
				'layers' => 'osm-4326-hybrid',
			),
			'options' => array(
				'isBaseLayer' => FALSE,
				'buffer' => 1,
			),
			'type' => ''
		);
		$layers[$layer->name] = $layer;

		// Add Custom Layers
		// Filter::map_base_layers
		Event::run('ushahidi_filter.map_base_layers', $layers);

		if ($layer_name)
		{
			if (isset($layers[$layer_name]))
			{
				return $layers[$layer_name];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $layers;
		}
	}

	/**
	 * GeoCode An Address
	 *
	 * @author
	 * @param   string  $address
	 * @return  array $geocodes - lat/lon
	 */
	public static function geocode($address = NULL)
	{
		if ($address)
		{
			$map_object = new GoogleMapAPI;
			//$map_object->_minify_js = isset($_REQUEST["min"]) ? FALSE : TRUE;
			$geocodes = $map_object->getGeoCode($address);
			//$geocodes = $MAP_OBJECT->geoGetCoordsFull($address);

			return $geocodes;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Calculate distances between two points
	 *
	 * @param   double	 point 1 latitude
	 * @param   double   point 1 longitude
	 * @param   double	 point 2 latitude
	 * @param   double   point 2 longitude
	 * @param   string   unit (m, k, n)
	 */
	public static function distance($lat1, $lon1, $lat2, $lon2, $unit = "k")
	{
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K")
		{
			return ($miles * 1.609344);
		}
		else if ($unit == "N")
		{
			return ($miles * 0.8684);
		}
		else
		{
			return $miles;
		}
	}

}