<?php defined('SYSPATH') or die('No direct script access.');

/**
* Default Settings From Database
*/

// Retrieve Cached Settings

$cache = Cache::instance();
$subdomain = Kohana::config('settings.subdomain');
$settings = $cache->get($subdomain.'_settings');
if ( ! $settings)
{ // Cache is Empty so Re-Cache
	$settings = ORM::factory('settings', 1);
	$cache->set($subdomain.'_settings', $settings, array('settings'), 60); // 1 Day
}

// Set Site Language
Kohana::config_set('locale.language', $settings->site_language);

// Main Site Settings
Kohana::config_set('settings.site_name', $settings->site_name);
Kohana::config_set('settings.site_email', $settings->site_email);
Kohana::config_set('settings.site_banner_id', $settings->site_banner_id);
Kohana::config_set('settings.site_tagline', $settings->site_tagline);
Kohana::config_set('settings.site_style', $settings->site_style);
Kohana::config_set('settings.site_contact_page', $settings->site_contact_page);
Kohana::config_set('settings.site_help_page', $settings->site_help_page);
Kohana::config_set('settings.site_message', $settings->site_message);
Kohana::config_set('settings.site_copyright_statement', $settings->site_copyright_statement);
Kohana::config_set('settings.site_submit_report_message', $settings->site_submit_report_message);
Kohana::config_set('settings.allow_reports', $settings->allow_reports);
Kohana::config_set('settings.allow_comments', $settings->allow_comments);
Kohana::config_set('settings.allow_feed', $settings->allow_feed);
Kohana::config_set('settings.allow_stat_sharing', $settings->allow_stat_sharing);
Kohana::config_set('settings.allow_clustering', $settings->allow_clustering);
Kohana::config_set('settings.sms_provider', $settings->sms_provider);
Kohana::config_set('settings.sms_no1', $settings->sms_no1);
Kohana::config_set('settings.sms_no2', $settings->sms_no2);
Kohana::config_set('settings.sms_no3', $settings->sms_no3);
Kohana::config_set('settings.default_map', $settings->default_map);
Kohana::config_set('settings.default_map_all', $settings->default_map_all);
Kohana::config_set('settings.api_google', $settings->api_google);
Kohana::config_set('settings.api_yahoo', $settings->api_yahoo);
Kohana::config_set('settings.api_akismet', $settings->api_akismet);
Kohana::config_set('settings.default_city', $settings->default_city);
Kohana::config_set('settings.default_country', $settings->default_country);
Kohana::config_set('settings.multi_country', $settings->multi_country);
Kohana::config_set('settings.default_lat', $settings->default_lat);
Kohana::config_set('settings.default_lon', $settings->default_lon);
Kohana::config_set('settings.default_zoom', $settings->default_zoom);
Kohana::config_set('settings.items_per_page', $settings->items_per_page);
Kohana::config_set('settings.items_per_page_admin', $settings->items_per_page_admin);
Kohana::config_set('settings.blocks_per_row', $settings->blocks_per_row);
Kohana::config_set('settings.google_analytics', $settings->google_analytics);
Kohana::config_set('settings.twitter_hashtags', $settings->twitter_hashtags);
Kohana::config_set('settings.email_username', $settings->email_username);
Kohana::config_set('settings.email_password', $settings->email_password);
Kohana::config_set('settings.email_port', $settings->email_port);
Kohana::config_set('settings.email_host', $settings->email_host);
Kohana::config_set('settings.email_servertype', $settings->email_servertype);
Kohana::config_set('settings.email_ssl', $settings->email_ssl);
Kohana::config_set('settings.alerts_email', $settings->alerts_email);
Kohana::config_set('settings.checkins', $settings->checkins);
Kohana::config_set('settings.db_version', $settings->db_version);
Kohana::config_set('settings.ushahidi_version', $settings->ushahidi_version);
Kohana::config_set('settings.private_deployment', $settings->private_deployment);

// Set Site Timezone 
if (function_exists('date_default_timezone_set'))
{
	$timezone = $settings->site_timezone;
	// Set default timezone, due to increased validation of date settings
	// which cause massive amounts of E_NOTICEs to be generated in PHP 5.2+
	date_default_timezone_set(empty($timezone) ? date_default_timezone_get() : $timezone);
	Kohana::config_set('settings.site_timezone', $timezone);
}

// Cache Settings
$cache_pages = ($settings->cache_pages) ? TRUE : FALSE;
Kohana::config_set('cache.cache_pages', $cache_pages);
Kohana::config_set('cache.default.lifetime', $settings->cache_pages_lifetime);

$default_map = $settings->default_map;
$map_layer = map::base($default_map);
if ($map_layer)
{
	Kohana::config_set('settings.api_url', "<script type=\"text/javascript\" src=\"".$map_layer->api_url."\"></script>" );
}

// And in case you want to display all maps on one page...
$api_google = $settings->api_google;
$api_yahoo = $settings->api_yahoo;
Kohana::config_set('settings.api_url_all', '<script src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6"></script><script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=' . $api_yahoo . '"></script><script src="http://maps.google.com/maps/api/js?v=3.2&amp;sensor=false" type="text/javascript"></script>'.html::script('http://www.openstreetmap.org/openlayers/OpenStreetMap.js'));

// Additional Mime Types (KMZ/KML)
Kohana::config_set('mimes.kml', array('text/xml'));
Kohana::config_set('mimes.kmz', array('text/xml'));
