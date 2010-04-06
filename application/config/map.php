<?php defined('SYSPATH') or die('No direct script access.');

/**
* MAP CONFIGURATION
*/

/**
 * Set single marker radius
 * Values from 1 to 10
 * Default: 4
 */
$config['marker_radius'] = "4";


/**
 * Set marker opacity.
 * Values from 1 (very transparent) to 10 (Opaque)
 * Default: 8
 */
$config['marker_opacity'] = "8";


/**
 * Set marker stroke width
 * Each marker circle can have a line around it
 * Values from 0 to 5
 * Default: 2
 */
$config['marker_stroke_width'] = "2";


/**
 * Set marker stroke opacity.
 * Values from 1 (very transparent) to 10 (Opaque)
 * Default: 9
 */
$config['marker_stroke_opacity'] = "9";


/**
 * Set list of map layers to use
 * Values ???
 * Default: array()
 */
$config['layers'] = array();

/**
 * Set number of zoom levels.
 * If maxZoomLevel - minZoomLevel > numZoomLevels then numZoomLevels has priority
 */

$config['numZoomLevels'] = "21";


/**
 * Set minimum zoom level, as defined by the provider. (0-21)
 * http://code.google.com/apis/maps/documentation/staticmaps/#Zoomlevels
 */

$config['minZoomLevel'] = "0";


/**
 * Set maximum zoom level, as defined by the provider. (0-21)
 * http://code.google.com/apis/maps/documentation/staticmaps/#Zoomlevels
 */

$config['maxZoomLevel'] = "21";

/**
 * Set maximum extents for the map. This will limit the area on the map
 * that users can access.
 * Default values allow the user to see the whole world
 */

$config['lonFrom'] = "-180";
$config['latFrom'] = "-85";
$config['lonTo'] = "180";
$config['latTo'] = "85";



