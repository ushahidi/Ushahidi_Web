<?php defined('SYSPATH') or die('No direct script access.');

/**
* Default Settings From Database
*/

// Retrieve Cached Settings

$cache = Cache::instance();
$subdomain = Kohana::config('settings.subdomain');
$settings = $cache->get($subdomain.'_settings');
if ( ! $settings OR ! is_array($settings))
{ // Cache is Empty so Re-Cache
	$settings = Settings_Model::get_array();
	$cache->set($subdomain.'_settings', $settings, array('settings'), 60); // 1 Day
}

// Set Site Language
Kohana::config_set('locale.language', $settings['site_language']);
ush_locale::detect_language();

// Copy everything into kohana config settings.XYZ
foreach($settings as $key => $setting)
{
	Kohana::config_set('settings.'.$key, $setting);
}

// Set Site Timezone
if (function_exists('date_default_timezone_set'))
{
	$timezone = $settings['site_timezone'];
	// Set default timezone, due to increased validation of date settings
	// which cause massive amounts of E_NOTICEs to be generated in PHP 5.2+
	date_default_timezone_set(empty($timezone) ? date_default_timezone_get() : $timezone);
	Kohana::config_set('settings.site_timezone', $timezone);
}

// Cache Settings
$cache_pages = ($settings['cache_pages']) ? TRUE : FALSE;
Kohana::config_set('cache.cache_pages', $cache_pages);
Kohana::config_set('cache.default.lifetime', $settings['cache_pages_lifetime']);

$default_map = $settings['default_map'];
$map_layer = map::base($default_map);
if (isset($map_layer->api_url) AND $map_layer->api_url != '')
{
	Kohana::config_set('settings.api_url', 
		"<script type=\"text/javascript\" src=\"".$map_layer->api_url."\"></script>");
}

// And in case you want to display all maps on one page...
$api_google = $settings['api_google'];
$api_live = $settings['api_live'];
Kohana::config_set('settings.api_url_all', 
	"<script type=\"text/javascript\" src=\"https://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6\"></script>\n"
	."<script type=\"text/javascript\" src=\"https://maps.google.com/maps/api/js?v=3.7&amp;sensor=false\"></script>\n"
	. html::script('https://www.openstreetmap.org/openlayers/OpenStreetMap.js')
);

// Additional Mime Types (KMZ/KML)
Kohana::config_set('mimes.kml', array('text/xml'));
Kohana::config_set('mimes.kmz', array('text/xml'));

// Set 'settings.forgot_password_key' if not set already
if ( ! Kohana::config('settings.forgot_password_secret'))
{
	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+[]{};:,.?`~';
	$key = text::random($pool, 64);
	Settings_Model::save_setting('forgot_password_secret', $key);
	Kohana::config_set('settings.forgot_password_secret', $key);
	$cache->delete($subdomain.'_settings');
}
