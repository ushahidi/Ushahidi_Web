<?php

/**
 * SETTINGS INTERFACE INTERNATIONALIZATION
 *
 * en_US
 */

$lang = array
(
    'default_location' => 'Default Location',
    'select_default_location' => 'Please select a default country',
    'download_city_list' => 'Retrieve Cities From Geonames',
    'multiple_countries' => 'Does this Ushahidi Instance Span Multiple Countries', 
    'map_settings' => 'Map Settings',
    'configure_map' => 'Configure Map',
    'default_map_view' => 'Default Map View',
    'default_zoom_level' => 'Default Zoom Level',
    'set_location' => 'Click and drag the map to set your exact location',
    'map_provider' => array
	(
		'name'		=> 'Map provider',
		'info'		=> 'Setting up your map provider is a straight- forward process. Select a provider, obtain an API key from the provider\'s site, and enter the API key',
        'choose'    => 'Select a Map Provider',
        'get_api'   => 'Get an API Key',
        'enter_api' => 'Enter the new API Key'
	),
    'site' => array
	(
		'display_contact_page' => 'Display Contact Page',
        'display_howtohelp_page' => 'Display "How to Help" Page',
        'email_alerts' => 'Alerts Email Address',
        'email_notice' => '<span>In order to receive reports by email, please <a href="'.url::base().'admin/settings/email">configure your email account settings</a>.</span>',
        'email_site' => 'Site Email Address',
        'title'		=> 'Site Settings',
		'name'		=> 'Site Name',
        'tagline'    => 'Site Tagline',
        'items_per_page'   => 'Items Per Page - Front End',
        'items_per_page_admin' => 'Items Per Page - Admin',
        'allow_reports' => 'Allow Users To Submit Reports',
        'allow_comments' => 'Allow Users to Submit Comments to Reports',
        'allow_feed' => 'Include RSS News Feed on Website',
        'allow_clustering' => 'Cluster Reports on Map',
        'default_category_colors' => 'Default Color For All Categories',
        'google_analytics' => 'Google Analytics',
        'google_analytics_example' => 'Web Property ID - Formato: UA-XXXXX-XX',
        'twitter_configuration' => 'Twitter Credentials',
        'twitter_hashtags' => 'Hashtags - Separate with commas ',
        'laconica_configuration' => 'Laconica Credentials',
        'laconica_site' => 'Laconica Site ',
        'language' => 'Site Language',
        'api_akismet' => 'Akismet Key',
        'kismet_notice' => 'Prevent comment spam using <a href="http://akismet.com/" target="_blank">Akismet</a> from Automattic. <BR />You can get a free API key by registering for a <a href="http://en.wordpress.com/api-keys/" target="_blank">WordPress.com user account</a>',
        'share_site_stats' => 'Share Site Statistics in API'
	),    
    'sms' => array
	(
        'title' => 'SMS Setup Options',
        'option_1'		=> 'Option 1: Use Frontline SMS', 
		'option_2'		=> 'Option 2: Use a Global SMS Gateway',
        'flsms_description' => 'FrontlineSMS is free open source software that turns a laptop and a mobile phone into a central communications hub. Once installed, the program enables users to send and receive text messages with large groups of people through mobile phones. Click on the grey box to request a download from FrontlineSMS.com',
        'flsms_download'    => 'Download Frontline SMS and install it on your computer',
        'flsms_synchronize'   => 'Sync with Ushahidi',
        'flsms_instructions' => 'Messages received into a FrontlineSMS hub can be synched with Ushahidi. Detailed instructions on how to sync can be found <strong><a href="http://wiki.ushahididev.com/doku.php?id=how_to_use_ushahidi_alpha#how_to_setup_frontlinesms_to_sync_with_ushahidi" target="_blank">here</a></strong>. You will require the key and link below to set up the sync with FrontlineSMS',
        'flsms_key' => 'Your Ushahidi Sync Key',
        'flsms_link' => 'FrontlineSMS HTTP Post LINK', 
        'flsms_text_1' => 'Enter phone number(s) connected to Frontline SMS in the field(s) below',
        'flsms_text_2' => 'Enter the number without any + or dashes below',
        'clickatell_text_1' => 'Sign up for Clickatells service by <a href="https://www.clickatell.com/central/user/client/step1.php?prod_id=2" target="_blank">clicking here</a>',
        'clickatell_text_2' => 'Enter your Clickatell access information below',
        'clickatell_api' => 'Your Clickatell API Number',
        'clickatell_username' => 'Your Clickatell User Name', 
        'clickatell_password' => 'Your Clickatell Password', 
        'clickatell_check_balance' => 'Check Your Clickatell Credit Balance', 
        'clickatell_load_balance' => 'Load Credit Balance'
	),      
    
    'map' => array
	(
		'zoom'		=> 'Zoom Level',
		'default_location'	=> 'Setting up your map provider is a straight- forward process. Select a provider, obtain an API key from the provider\'s site, and enter the API key'
	),   
    
	'site_name' => array
	(
		'required'		=> 'The site name field is required.',
		'length'		=> 'The site name field must be at least 3 and no more 50 characters long.',
	),
	
	'site_tagline' => array
	(
		'required'		=> 'The tagline field is required.',
		'length'		=> 'The tagline field must be at least 3 and no more 100 characters long.',
	),
	
	'site_email' => array
	(
		'email'		  => 'The site email field does not appear to contain a valid email address?',
		'length'	  => 'The site email field must be at least 4 and no more 100 characters long.'
	),
	
	'items_per_page' => array
	(
		'required'		=> 'The items per page (Frontend) field is required.',
		'between' => 'The items per page (Frontend) field does not appear to contain a valid value?'
	),
	
	'items_per_page_admin' => array
	(
		'required'		=> 'The items per page (Admin) field is required.',
		'between' => 'The items per page (Admin) field does not appear to contain a valid value?'
	),
	
	'allow_reports' => array
	(
		'required'		=> 'The allow reports field is required.',
		'between' => 'The allow reports field does not appear to contain a valid value?'
	),
	
	'allow_comments' => array
	(
		'required'		=> 'The allow comments field is required.',
		'between' => 'The allow comments field does not appear to contain a valid value?'
	),
	
	'allow_stat_sharing' => array
	(
		'required'		=> 'The stat sharing field is required.',
		'between' => 'The stat sharing field does not appear to contain a valid value?'
	),
	
	'allow_feed' => array
	(
		'required'		=> 'The include feed field is required.',
		'between' => 'The include feed field does not appear to contain a valid value?'
	),
	
	'default_map_all' => array
	(
		'required'		=> 'The color field is required.',
		'alpha_numeric'		=> 'The color feed field does not appear to contain a valid value?',
		'length'		=> 'The color field must be no more 6 characters long.',
	),
	
	'api_akismet' => array
	(
		'alpha_numeric'		=> 'The Akismet field does not appear to contain a valid value?',
		'length'		=> 'The Akismet field does not appear to contain a valid value?'
	),		
	
	'sms_no1' => array
	(
		'numeric'		=> 'The phone 1 field should contain numbers only.',
		'length' => 'The phone 1 field does not appear to contain a valid value?'
	),
	
	'sms_no2' => array
	(
		'numeric'		=> 'The phone 2 field should contain numbers only.',
		'length' => 'The phone 2 field is too long'
	),
	
	'sms_no3' => array
	(
		'numeric'		=> 'The phone 3 field should contain numbers only.',
		'length' => 'The phone 3 field is too long'
	),
	
	'clickatell_api' => array
	(
		'required'		=> 'The Clickatell API number field is required.',
		'length'		=> 'The Clickatell API number field must be no more 20 characters long.',
	),
	
	'clickatell_username' => array
	(
		'required'		=> 'The Clickatell Username field is required.',
		'length'		=> 'The Clickatell Username field must be no more 50 characters long.',
	),
	
	'clickatell_password' => array
	(
		'required'		=> 'The Clickatell Password field is required.',
		'length'		=> 'The Clickatell Password field must be at least 5 and no more 50 characters long.',
	),

	'google_analytics' => array
	(
		'length'		=> 'The Google Analytics field must contain a valid Web Property ID in the format UA-XXXXX-XX.',
	),
	
	'email_username' => array
	(
		'required'		=> 'The Mail Server Username field is required.',
		'length'		=> 'The Mail Server Username field must be no more 50 characters long.',
	),
	
	'email_password' => array
	(
		'required'		=> 'The Mail Server Password field is required.',
		'length'		=> 'The Mail Server Password field must be at least 5 and no more 50 characters long.',
	),
	
	'email_port' => array
	(
		'numeric'		=> 'The Mail server port field should contain numbers only.',
		'length' 		=> 'The Mail server port field is too long'
	),
	
	'email_host' => array
	(
		'numeric'		=> 'The Mail server port field should contain numbers only.',
		'length' 		=> 'The Mail server port field is too long'
	),
	
	'email_servertype' => array
	(
		'required'		=> 'The Mail Server Type field is required.',
		'length' 		=> 'The Mail server port field is too long'
	)		
	
);