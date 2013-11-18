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

		// To store options for the bing maps
		foreach ($layers as $layer)
		{
			if ($layer->active)
			{

				if ($all == TRUE OR $layer->openlayers == $openlayers_type)
				{
					//++ Bing doesn't have the first argument
					if ($layer->openlayers == "Bing")
					{
						// Options for the Bing layer constructor
						$bing_options = "{\n"
						    . "\t name: \"".$layer->data['name']."\",\n"
						    . "\t type: \"".$layer->data['type']."\",\n"
						    . "\t key: \"".$layer->data['key']."\"\n"
						    . "}";
						
						$js .= "var ".$layer->name." = new OpenLayers.Layer.".$layer->openlayers."($bing_options);\n\n";
					}
					// Allow layers to specify a custom set of OpenLayers options
					// this should allow plugins to add OpenLayers Layer types we haven't considered here
					// See http://dev.openlayers.org/docs/files/OpenLayers/Layer-js.html for other layer types
					elseif (isset($layer->openlayers_options) AND $layer->openlayers_options != null)
					{
						$js .= "var ".$layer->name." = new OpenLayers.Layer.{$layer->openlayers}({$layer->openlayers_options});\n\n";
					}
					// Finally construct JS for the majority of layers
					else
					{
						$js .= "var ".$layer->name." = new OpenLayers.Layer.".$layer->openlayers."(\"".$layer->title."\", ";

						if ($layer->openlayers == 'XYZ' || $layer->openlayers == 'WMS' || $layer->openlayers == 'TMS')
						{
							if (isset($layer->data['url']))
							{
								$js .= '"'.$layer->data['url'].'", ';
							}
						}

						// Extra parameter used by WMS - key/value pairs representing the GetMap query string
						if ($layer->openlayers == 'WMS' AND isset($layer->wms_params))
						{
							// Add some unnescessary params so that json_encode creates an object not an array.
							if (!isset($layer->wms_params['styles'])) $layer->wms_params['styles'] = '';
							if (!isset($layer->wms_params['layers'])) $layer->wms_params['layers'] = '';
							
							$js .= json_encode($layer->wms_params);
							$js .= ', ';
						}
	
						$js .= "{ \n";

						$params = $layer->data;
						if (isset($params['url'])) unset($params['url']);
						if (isset($params['baselayer'])) unset($params['baselayer']);
						$params['sphericalMercator'] = true;

						// Special handling for layer type - don't quote the value as it should be a js variable
						if (isset($params['type']) AND $params['type'] != '')
						{
							$js .= " type: ".$params['type'].",\n";
							unset($params['type']);
						}

						foreach ($params as $key => $value)
						{
							if ( ! empty($value))
							{
								$js .= " ".$key.": ".json_encode($value).",\n";
							}
						}

						$js .= " maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)});\n\n";
					}
					
				}
			}
		}

		// Hack on XYZ / Esri Attribution layer here since XYZ doesn't support images in attribution
		if (stripos($default_map,'esri_') !== FALSE AND $all == FALSE)
		{
			// We have two div ids that we use for maps, map and divMap. We need a more permanent
			//   solution to cover any div name.
			// $js .= "if ( $(\"#map\").length > 0 ) { divName = \"map\" }else{ divName= \"divMap\" }"
			// 	. "var esriAttributionDiv = document.createElement('div');"
			//     . "$(esriAttributionDiv).html('"
			// 	. "<img src=\"http://www.arcgis.com/home/images/map/logo-sm.png\" style=\"float:right;\"/>"
			// 	. "<small style=\"position: absolute; bottom: -10px;\">"
			// 	. "Sources: Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, "
			// 	. "GeoBase, IGN, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), "
			// 	. "and the GIS User Community"
			// 	. "</small>');\n"
			//     . "$(esriAttributionDiv).css({\n"
			//     . "\t'position': 'absolute',\n"
			//     . "\t'z-index': 10000,\n"
			//     . "\t'margin': '-40px 0 0 85px',\n"
			//     . "\t'right': $('div#'+divName).offset().right + 10,\n"
			//     . "\t'width': $('div#'+divName).width() - 90,\n"
			//     . "});\n"
			// 	. "$(esriAttributionDiv).appendTo($('div#'+divName));";
		}
		
		Event::run('ushahidi_filter.map_layers_js', $js);

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
		{
			// Map Layer Doesn't Exist - default to google
			$default_map = "google_normal";
		}

		// Get openlayers type
		$openlayers_type = $layers[$default_map]->openlayers;
		$js .= $default_map;
		foreach ($layers as $layer)
		{
			if ($layer->name != $default_map AND $layer->active)
			{
				if ($all == TRUE OR $layer->openlayers == $openlayers_type)
				{
					$js .= ",".$layer->name;
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
			'url' => Kohana::config('core.site_protocol').'://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/${z}/${y}/${x}',
			'type' => '',
			'transitionEffect' => 'resize',
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
			'url' => Kohana::config('core.site_protocol').'://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/${z}/${y}/${x}',
			'type' => '',
			'transitionEffect' => 'resize',
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
			'url' => Kohana::config('core.site_protocol').'://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/${z}/${y}/${x}',
			'type' => '',
			'transitionEffect' => 'resize',
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
			'url' => Kohana::config('core.site_protocol').'://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/${z}/${y}/${x}',
			'type' => '',
			'transitionEffect' => 'resize',
		);
		$layers[$layer->name] = $layer;

		// GOOGLE Satellite
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'google_satellite';
		$layer->openlayers = "Google";
		$layer->title = 'Google Maps Satellite';
		$layer->description = 'Google Maps Satellite Imagery.';
		$layer->api_url = 'https://maps.google.com/maps/api/js?v=3.7&amp;sensor=false&amp;language='.Kohana::config('locale.language.0');
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'google.maps.MapTypeId.SATELLITE',
			'animationEnabled' => TRUE,
		);
		$layers[$layer->name] = $layer;

		// GOOGLE Hybrid
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'google_hybrid';
		$layer->openlayers = "Google";
		$layer->title = 'Google Maps Hybrid';
		$layer->description = 'Google Maps with roads and terrain.';
		$layer->api_url = 'https://maps.google.com/maps/api/js?v=3.7&amp;sensor=false&amp;language='.Kohana::config('locale.language.0');
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'google.maps.MapTypeId.HYBRID',
			'animationEnabled' => TRUE,
		);
		$layers[$layer->name] = $layer;

		// GOOGLE Normal
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'google_normal';
		$layer->openlayers = "Google";
		$layer->title = 'Google Maps Normal';
		$layer->description = 'Standard Google Maps Roads';
		$layer->api_url = 'https://maps.google.com/maps/api/js?v=3.7&amp;sensor=false&amp;language='.Kohana::config('locale.language.0');
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => '',
			'animationEnabled' => TRUE,
		);
		$layers[$layer->name] = $layer;

		// GOOGLE Physical
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'google_physical';
		$layer->openlayers = "Google";
		$layer->title = 'Google Maps Physical';
		$layer->description = 'Google Maps Hillshades';
		$layer->api_url = 'https://maps.google.com/maps/api/js?v=3.7&amp;sensor=false&amp;language='.Kohana::config('locale.language.0');
		$layer->data = array(
			'baselayer' => TRUE,
			'type' => 'google.maps.MapTypeId.TERRAIN',
			'animationEnabled' => TRUE,
		);
		$layers[$layer->name] = $layer;

		// BING Road
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'bing_road';
		$layer->openlayers = "Bing";
		$layer->title = 'Bing-Road';
		$layer->description = 'Bing Road Maps';
		$layer->api_signup = Kohana::config('core.site_protocol').'://www.bingmapsportal.com/';
		$layer->api_url = '';
		$layer->data = array(
			'name' => 'Bing-Road',
			'baselayer' => TRUE,
			'key' => Kohana::config('settings.api_live'),
			'type' => 'Road',
		);
		$layers[$layer->name] = $layer;

		// BING Hybrid
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'bing_hybrid';
		$layer->openlayers = "Bing";
		$layer->title = 'Bing-Hybrid';
		$layer->description = 'Bing hybrid of streets and satellite tiles.';
		$layer->api_signup = Kohana::config('core.site_protocol').'://www.bingmapsportal.com/';
		$layer->api_url = '';
		$layer->data = array(
			'name' => 'Bing-Hybrid',
			'baselayer' => TRUE,
			'key' => Kohana::config('settings.api_live'),
			'type' => 'AerialWithLabels',
		);
		$layers[$layer->name] = $layer;

		// BING Aerial
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'bing_satellite';
		$layer->openlayers = "Bing";
		$layer->title = 'Bing-Satellite';
		$layer->description = 'Bing Satellite Tiles';
		$layer->api_signup = Kohana::config('core.site_protocol').'://www.bingmapsportal.com/';
		$layer->api_url = '';
		$layer->data = array(
			'name' => 'Bing-Satellite',
			'baselayer' => TRUE,
			'key' => Kohana::config('settings.api_live'),
			'type' => 'Aerial',
		);
		$layers[$layer->name] = $layer;

		// OpenStreetMap Mapnik
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'osm_mapnik';
		$layer->openlayers = "OSM.Mapnik";
		$layer->title = 'OSM Mapnik';
		$layer->description = 'The main OpenStreetMap map';
		$layer->api_url = Kohana::config('core.site_protocol').'://www.openstreetmap.org/openlayers/OpenStreetMap.js';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
			'url' => 'http://tile.openstreetmap.org/${z}/${x}/${y}.png',
			'type' => '',
			'transitionEffect' => 'resize',
		);
		$layers[$layer->name] = $layer;

		// OpenStreetMap Cycling Map
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'osm_cycle';
		$layer->openlayers = "OSM.CycleMap";
		$layer->title = 'OSM Cycling Map';
		$layer->description = 'OpenStreetMap with highlighted bike lanes';
		$layer->api_url = Kohana::config('core.site_protocol').'://www.openstreetmap.org/openlayers/OpenStreetMap.js';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
			'url' => 'http://tile.openstreetmap.org/cycle/${z}/${x}/${y}.png',
			'type' => '',
			'transitionEffect' => 'resize',
		);
		$layers[$layer->name] = $layer;

		// OpenStreetMap Transport
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'osm_TransportMap';
		$layer->openlayers = "OSM.TransportMap";
		$layer->title = 'OSM Transport Map';
		$layer->description = 'TransportMap';
		$layer->api_url = Kohana::config('core.site_protocol').'://www.openstreetmap.org/openlayers/OpenStreetMap.js';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
			'url' => 'http://tile.openstreetmap.org/transport/${z}/${x}/${y}.png',
			'type' => '',
			'transitionEffect' => 'resize',
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
				return FALSE;
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
		return geocode::geocode($address);
	}

	/**
	 * Reverse Geocode a point
	 *
	 * @author
	 * @param   double  $latitude
	 * @param   double  $longitude
	 * @return  string  closest approximation of the point as a display name
	 */
	public static function reverse_geocode($latitude,$longitude)
	{
		return geocode::reverseGeocode($latitude, $longitude);
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