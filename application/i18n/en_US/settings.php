<?php
	$lang = array(
	'allow_comments' => array(
		'between' => 'The allow comments field does not appear to contain a valid value?',
		'required' => 'The allow comments field is required.',
	),
	'allow_feed' => array(
		'between' => 'The include feed field does not appear to contain a valid value?',
		'required' => 'The include feed field is required.',
	),
	'allow_reports' => array(
		'between' => 'The allow reports field does not appear to contain a valid value?',
		'required' => 'The allow reports field is required.',
	),
	'allow_stat_sharing' => array(
		'between' => 'The stat sharing field does not appear to contain a valid value?',
		'required' => 'The stat sharing field is required.',
	),
	'api' => array(
		'default_record_limit' => 'Default no. of records to be fetched per API request',
		'maximum_record_limit' => 'Maximum no. of records to be fetched per API request',
		'maximum_requests_per_ip_address' => 'Maximum no. of API requests per IP address',
	),
	'api_akismet' => array(
		'alpha_numeric' => 'The Akismet field does not appear to contain a valid value?',
		'length' => 'The Akismet field does not appear to contain a valid value?',
	),
	'banner_image' => array(
		'default' => 'Something went wrong with your banner image upload.',
		'size' => 'The size of your banner exceeds the size limit for this upload.'
	),
	'cache_pages' => array(
		'between' => 'The cache pages field is required.',
		'required' => 'The cache pages field does not appear to contain a valid value?',
	),
	'cache_pages_lifetime' => array(
		'in_array' => 'The cache pages lifetime field does not appear to contain a valid value?',
		'required' => 'The cache pages lifetime field is required.',
	),
	'cleanurl' => array(
		'clean_url_disabled' => 'It looks like your server is not configured to handle clean URLs. You will need to change the configuration of your server before you can enable clean URLs. See more info on how to enable clean URLs at this forum <a href="http://forums.ushahidi.com/topic/server-configuration-for-apache-mod-rewrite" target="_blank">post</a>',
		'clean_url_enabled' => 'This option makes Ushahidi to be accessed via "clean" URLs. Without "index.php" in the URL.',
		'enable_clean_url' => 'Enable Clean URLs',
		'title' => 'Clean URLs',
	),
	'clickatell_api' => array(
		'length' => 'The Clickatell API number field must be no more 20 characters long.',
		'required' => 'The Clickatell API number field is required.',
	),
	'clickatell_password' => array(
		'length' => 'The Clickatell Password field must be at least 5 and no more 50 characters long.',
		'required' => 'The Clickatell Password field is required.',
	),
	'clickatell_username' => array(
		'length' => 'The Clickatell Username field must be no more 50 characters long.',
		'required' => 'The Clickatell Username field is required.',
	),
	'configure_map' => 'Configure Map',
	'default_location' => 'Default Location',
	'default_map_all' => array(
		'alpha_numeric' => 'The color feed field does not appear to contain a valid value?',
		'length' => 'The color field must be no more 6 characters long.',
		'required' => 'The color field is required.',
	),
	'default_map_view' => 'Default Map View',
	'default_zoom_level' => 'Default Zoom Level',
	'download_city_list' => 'Retrieve Cities From Geonames',
	'email_host' => array(
		'length' => 'The Mail server port field is too long',
		'numeric' => 'The Mail server port field should contain numbers only.',
	),
	'email_password' => array(
		'length' => 'The Mail Server Password field must be at least 5 and no more 50 characters long.',
		'required' => 'The Mail Server Password field is required.',
	),
	'email_port' => array(
		'length' => 'The Mail server port field is too long',
		'numeric' => 'The Mail server port field should contain numbers only.',
	),
	'email_servertype' => array(
		'length' => 'The Mail server port field is too long',
		'required' => 'The Mail Server Type field is required.',
	),
	'email_username' => array(
		'length' => 'The Mail Server Username field must be no more 50 characters long.',
		'required' => 'The Mail Server Username field is required.',
	),
	'facebook' => array(
		'title' => 'Facebook Setup Options',
		'description' => 'To get the information below you will need to create a new facebook application at',
		'app_id' => 'Facebook App ID',
		'app_secret' => 'Facebook App Secret'
	),
	'google_analytics' => array(
		'length' => 'The Google Analytics field must contain a valid Web Property ID in the format UA-XXXXX-XX.',
	),
	'https' => array(
	   'enable_https' => 'Enable HTTPS',
	   'https_disabled' => 'This option makes Ushahidi be accessed in unsecure mode; <strong>without</strong> "https://" in the URL prefix',
	   'https_enabled' => 'This option makes Ushahidi be accessed in secure mode; with <strong>https</strong> in the URL prefix',
	   'title' => 'HTTPS'
	),
	'items_per_page' => array(
		'between' => 'The items per page (Frontend) field does not appear to contain a valid value?',
		'required' => 'The items per page (Frontend) field is required.',
	),
	'items_per_page_admin' => array(
		'between' => 'The items per page (Admin) field does not appear to contain a valid value?',
		'required' => 'The items per page (Admin) field is required.',
	),
	'map' => array(
		'default_location' => 'Setting up your map provider is a straight- forward process. Select a provider, obtain an API key from the provider\'s site, and enter the API key',
		'zoom' => 'Zoom Level',
	),
	'map_provider' => array(
		'choose' => 'Select a Map Provider',
		'enter_api' => 'Enter the new API Key',
		'get_api' => 'Get an API Key',
		'info' => 'Setting up your map provider is a straight- forward process. Select a provider, obtain an API key from the provider\'s site, and enter the API key',
		'name' => 'Map provider',
	),
	'map_settings' => 'Map Settings',
	'multiple_countries' => 'Does this Ushahidi Deployment Span Multiple Countries',
	'select_default_location' => 'Please select a default country',
	'set_location' => 'Click and drag the map to set your exact location',
	'site' => array(
		'allow_clustering' => 'Cluster Reports on Map',
		'allow_comments' => 'Allow Users to Submit Comments to Reports',
		'allow_feed' => 'Include RSS News Feed on Website',
		'allow_reports' => 'Allow Users To Submit Reports',
		'api_akismet' => 'Akismet Key',
		'banner' => 'Site Banner',
		'blocks_per_row' => 'Blocks Per Row',
		'cache_pages' => 'Cache Pages',
		'cache_pages_lifetime' => 'Cache Pages Lifetime',
		'checkins' => 'Checkins',
		'copyright_statement' => 'Site Copyright Statement',
		'default_category_colors' => 'Default Color For All Categories',
		'delete_banner_image' => 'Delete Banner Image',
		'display_contact_page' => 'Display Contact Page',
		'display_howtohelp_page' => 'Display "How to Help" Page',
		'email_alerts' => 'Alerts Email Address',
		'email_notice' => '<span>In order to receive reports by email, please configure your email account settings.</span>',
		'email_site' => 'Site Email Address',
		'google_analytics' => 'Google Analytics',
		'google_analytics_example' => 'Web Property ID - Formato: UA-XXXXX-XX',
		'items_per_page' => 'Items Per Page - Front End',
		'items_per_page_admin' => 'Items Per Page - Admin',
		'kismet_notice' => 'Prevent comment spam using <a href="http://akismet.com/" target="_blank">Akismet</a> from Automattic. <BR />You can get a free API key by registering for a <a href="http://en.wordpress.com/api-keys/" target="_blank">WordPress.com user account</a>',
		'laconica_configuration' => 'Laconica Credentials',
		'laconica_site' => 'Laconica Site ',
		'language' => 'Site Language',
		'message' => 'Site Message',
		'name' => 'Site Name',
		'private_deployment' => 'Private Deployment',
		'submit_report_message' => 'Submit Report Message',
		'share_site_stats' => 'Enable Statistics (Stored on Ushahidi\'s server)',
		'tagline' => 'Site Tagline',
		'timezone' => 'Timezone',
		'title' => 'Site Settings',
		'twitter_configuration' => 'Twitter Search Terms',
		'twitter_hashtags' => 'Hashtags - Separate with commas ',
	),
	'site_email' => array(
		'email' => 'The site email field does not appear to contain a valid email address?',
		'length' => 'The site email field must be at least 4 and no more 100 characters long.',
	),
	'site_name' => array(
		'length' => 'The site name field must be at least 3 and no more 50 characters long.',
		'required' => 'The site name field is required.',
	),
	'site_tagline' => array(
		'length' => 'The tagline field must be at least 3 and no more 100 characters long.',
		'required' => 'The tagline field is required.',
	),
	'sms' => array(
		'clickatell_api' => 'Your Clickatell API Number',
		'clickatell_check_balance' => 'Check Your Clickatell Credit Balance',
		'clickatell_load_balance' => 'Load Credit Balance',
		'clickatell_password' => 'Your Clickatell Password',
		'clickatell_text_1' => 'Sign up for Clickatells service by <a href="https://www.clickatell.com/central/user/client/step1.php?prod_id=2" target="_blank">clicking here</a>',
		'clickatell_text_2' => 'Enter your Clickatell access information below',
		'clickatell_username' => 'Your Clickatell User Name',
		'flsms_description' => 'FrontlineSMS is free open source software that turns a laptop and a mobile phone into a central communications hub. Once installed, the program enables users to send and receive text messages with large groups of people through mobile phones. Click on the grey box to request a download from FrontlineSMS.com',
		'flsms_download' => 'Download Frontline SMS and install it on your computer',
		'flsms_instructions' => 'Messages received into a FrontlineSMS hub can be synched with Ushahidi. Detailed instructions on how to sync can be found <strong><a href="http://wiki.ushahididev.com/doku.php?id=how_to_use_ushahidi_alpha#how_to_setup_frontlinesms_to_sync_with_ushahidi" target="_blank">here</a></strong>. You will require the key and link below to set up the sync with FrontlineSMS',
		'flsms_key' => 'Your Ushahidi Sync Key',
		'flsms_link' => 'FrontlineSMS HTTP Post LINK',
		'flsms_synchronize' => 'Sync with Ushahidi',
		'flsms_text_1' => 'Enter phone number(s) connected to Frontline SMS in the field(s) below',
		'flsms_text_2' => 'Enter the number without any + or dashes below',
		'option_1' => 'Option 1: Use Frontline SMS',
		'option_2' => 'Option 2: Use a Global SMS Gateway',
		'title' => 'SMS Setup Options',
	),
	'sms_no1' => array(
		'length' => 'The phone 1 field does not appear to contain a valid value?',
		'numeric' => 'The phone 1 field should contain numbers only.',
	),
	'sms_no2' => array(
		'length' => 'The phone 2 field is too long',
		'numeric' => 'The phone 2 field should contain numbers only.',
	),
	'sms_no3' => array(
		'length' => 'The phone 3 field is too long',
		'numeric' => 'The phone 3 field should contain numbers only.',
	));
?>
