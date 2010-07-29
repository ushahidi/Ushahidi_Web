<?php

/**
 * SETTINGS INTERFACE INTERNATIONALIZATION
 *
 * en_US
 */

$lang = array
(
    'default_location' => 'Eneo la awali',
    'select_default_location' => 'Tafadhali chagua nchi ya awali',
    'download_city_list' => 'Pata miji kutokana na Geonames',
    'multiple_countries' => 'Je hii Ushahidi inatumika kwenye nchi nyingi', 
	'default_location' => 'Default Location',
    'map_settings' => 'Vipimo vya Ramani',
    'configure_map' => 'Sanidi Ramani',
    'default_map_view' => 'Mandhari ya Ramani ya Awali',
    'default_zoom_level' => 'Zoom ya Ramani ya Awali',
    'set_location' => 'Bonyeza uburute ramani kwa eneo unayoitaka',
    'map_provider' => array
	(
		'name'		=> 'Map provider',
		'info'		=> 'Kueka map provider ni jambo rahisi. Chagua provider, Pata API key Kutoka kwa tovuti ya provider, ubandike API key hapa',
        'choose'    => 'Chagua Map Provider',
        'get_api'   => 'Pata API Key',
        'enter_api' => 'Andika API Key mpya'
	),
    'site' => array
	(
		'display_contact_page' => 'Onyesha kurasa ya  mawasiliano',
        'display_howtohelp_page' => 'Onyesha kurasa ya "Ushaidizi"',
        'email_alerts' => 'Anwani ya barua pepe ya kutanabahisha',
        'email_notice' => '<span>Ili kupata repoti na barua pepe, tafadhali <a href="'.url::base().'admin/settings/email">sanidi vipimo vyako vya anwani yako ya barua pepe</a>.</span>',
        'email_site' => 'Anwani ya barua pepe ya tovuti',
        'title'		=> 'Vipimo vya tovuti',
		'name'		=> 'Jina la tovuti',
        'tagline'    => 'Tagline ya tovuti',
        'items_per_page'   => 'Vipengele kwa kila kurasa - Uso wa tovuti',
        'items_per_page_admin' => 'Vipengele kwa kila kurasa - Meneja',
        'allow_reports' => 'Ruhusu watumizi kutunga ripoti',
        'allow_comments' => 'Ruhusu watumizi kutunga fikira zao kwa kila ripoti',
        'allow_feed' => 'Weka tawanyiko la habari za RSS kwa tovuti',
        'allow_clustering' => 'Kushanya repoti kwa ramani',
        'default_category_colors' => 'Rangi ya asili kwa jamii zote',
        'google_analytics' => 'Google Analytics',
        'google_analytics_example' => 'Web Property ID - Formato: UA-XXXXX-XX',
        'twitter_configuration' => 'Sifa za Twitter',
        'twitter_hashtags' => 'Hashtags - Tofautisha na comma ',
        'laconica_configuration' => 'Sifa za Laconica',
        'laconica_site' => 'Tovuti ya Laconica ',
        'language' => 'Lugha ya tovuti',
        'api_akismet' => 'Key ya Akismet',
        'kismet_notice' => 'Zuia spam kwa fikira kutumia <a href="http://akismet.com/" target="_blank">Akismet</a> kutoka Automattic. <BR />Unaeza pata API key ya bure kwa kujisajilisha kwa <a href="http://en.wordpress.com/api-keys/" target="_blank">akaunti ya mtumizi ya WordPress.com</a>',
        'share_site_stats' => 'Shriki na takwimu za API na wengine'
	),    
    'sms' => array
	(
        'title' => 'Vipimo vya SMS',
        'option_1'		=> 'Chaguo la 1: Tumia Frontline SMS', 
		'option_2'		=> 'Chaguo la 2: Tumia SMS Gateway ya kimataifa',
        'flsms_description' => 'FrontlineSMS is free open source software that turns a laptop and a mobile phone into a central communications hub. Once installed, the program enables users to send and receive text messages with large groups of people through mobile phones. Click on the grey box to request a download from FrontlineSMS.com',
        'flsms_download'    => 'Pakua Frontline SMS na uingilize kwa computer yako',
        'flsms_synchronize'   => 'Sync na Ushahidi',
        'flsms_instructions' => 'Ujumbe zinazopatikana kutoka kwa FrontlineSMS hub zinaeza kuwa synched na Ushahidi. Namna ya kufanya hivi inapatikana <strong><a href="http://wiki.ushahididev.com/doku.php?id=how_to_use_ushahidi_alpha#how_to_setup_frontlinesms_to_sync_with_ushahidi" target="_blank">hapa</a></strong>. Utahitaji key na kiungo kinachofuata kutumia FrontlineSMS',
        'flsms_key' => 'Your Ushahidi Sync Key',
        'flsms_link' => 'FrontlineSMS HTTP Post LINK', 
        'flsms_text_1' => 'Andika namba za simu za Frontline SMS kwa sehemu inayofuata',
        'flsms_text_2' => 'Andika namba za simu + au mapengo kwa sehemu ifuatayo',
        'clickatell_text_1' => 'Jiunganishe na Clickatells kwa <a href="https://www.clickatell.com/central/user/client/step1.php?prod_id=2" target="_blank">kubonya hapa</a>',
        'clickatell_text_2' => 'Ingiza maarifa ya kuungana na Clickatell kwa sehemu inayofuata',
        'clickatell_api' => 'Namba yako ya Clickatell API',
        'clickatell_username' => 'Jina la mtumizi wa akaunti yako ya Clickatell', 
        'clickatell_password' => 'Nywila yako ya Clickatell', 
        'clickatell_check_balance' => 'Pesa zilizobaki za Clickatell', 
        'clickatell_load_balance' => 'Engeza Credit ya Clickatell'
	),      
    
    'map' => array
	(
		'zoom'		=> 'Kipimo cha zoom',
		'default_location'	=> 'Kueka map provider ni jambo rahisi. Chagua provider, Pata API key Kutoka kwa tovuti ya provider, ubandike API key hapa'
	),   
    
	'site_name' => array
	(
		'required'		=> 'Jina la tovuti linahitajika.',
		'length'		=> 'Jina la tovuti lazma liwe baina ya herufi 3 na 50.',
	),
	
	'site_tagline' => array
	(
		'required'		=> 'Jina la tagline linahitajika.',
		'length'		=> 'Jina la tagline lazma liwe baina ya herufi 3 na 100.',
	),
	
	'site_email' => array
	(
		'email'		  => 'Anwani ya barua pepe ya tovuti inaonekana si sahihi',
		'length'	  => 'Anwani ya barua pepe ya tovuti lazma liwe baina ya herufi 3 na 100.'
	),
	
	'items_per_page' => array
	(
		'required'		=> 'Sehemu ya vipengele kwa kurasa (Uso wa tovuti) inahitajika.',
		'between' => 'Sehemu ya vipengele kwa kurasa (Uso wa tovuti) inaonekana si sahihi'
	),
	
	'items_per_page_admin' => array
	(
		'required'		=> 'Sehemu ya vipengele kwa kurasa (Meneja) inahitajika.',
		'between' => 'Sehemu ya vipengele kwa kurasa (Meneja) inaonekana si sahihi'
	),
	
	'allow_reports' => array
	(
		'required'		=> 'Sehemu ya ruhusu ripoti inahitajika.',
		'between' => 'Sehemu ya ruhusu ripoti inaonekana si sahihi'
	),
	
	'allow_comments' => array
	(
		'required'		=> 'Sehemu ya ruhusu fikira inahitajika.',
		'between' => 'Sehemu ya ruhusu fikira inaonekana si sahihi'
	),
	
	'allow_stat_sharing' => array
	(
		'required'		=> 'Sehemu ya gawanya takwimu inahitajika.',
		'between' => 'Sehemu ya gawanya takwimu inaonekana si sahihi'
	),
	
	'allow_feed' => array
	(
		'required'		=> 'Sehemu ya ruhusu tawanyiko inahitajika.',
		'between' => 'Sehemu ya ruhusu tawanyiko inaonekana si sahihi'
	),
	
	'default_map_all' => array
	(
		'required'		=> 'Sehemu ya rangi inahitajika.',
		'alpha_numeric'		=> 'Sehemu ya rangi inaonekana si sahihi',
		'length'		=> 'Sehemu ya rangi lazima iwe na herufi 6.',
	),
	
	'api_akismet' => array
	(
		'alpha_numeric'		=> 'Sehemu ya Akismet inaonekana si sahihi',
		'length'		=> 'Sehemu ya Akismet inaonekana si sahihi'
	),		
	
	'sms_no1' => array
	(
		'numeric'		=> 'Sehemu ya number ya simu ya kwanza lazma iwe na nambari.',
		'length' => 'Sehemu ya number ya simu ya kwanza inaonekana si sahihi'
	),
	
	'sms_no2' => array
	(
		'numeric'		=> 'Sehemu ya number ya simu ya pili lazma iwe na nambari.',
		'length' => 'Sehemu ya number ya simu ya pili inaonekana si sahihi'
	),
	
	'sms_no3' => array
	(
		'numeric'		=> 'Sehemu ya number ya simu ya tatu lazma iwe na nambari.',
		'length' => 'Sehemu ya number ya simu ya tatu inaonekana si sahihi'
	),
	
	'clickatell_api' => array
	(
		'required'		=> 'Sehemu ya nambari ya Clickatell API inahitajika.',
		'length'		=> 'Sehemu ya nambari ya Clickatell API haiezi pitisha herufi 20.',
	),
	
	'clickatell_username' => array
	(
		'required'		=> 'Sehemu ya nambari ya jina la mtumizi wa Clickatell API inahitajika.',
		'length'		=> 'Sehemu ya nambari ya jina la mtumizi wa Clickatell API haiezi pitisha herufi 20.',
	),
	
	'clickatell_password' => array
	(
		'required'		=> 'Sehemu ya nywila ya Clickatell inahitajika.',
		'length'		=> 'Sehemu ya nywila ya Clickatell lazma iwe baina ya herufi 5 na 50.',
	),

	'google_analytics' => array
	(
		'length'		=> 'Sehemu ya Google Analytics lazma iwe na Web Property ID sahihi kwa fomati ya UA-XXXXX-XX.',
	),
	
	'email_username' => array
	(
		'required'		=> 'Sehemu ya jina la mtumizi wa Mail Server inahitajika.',
		'length'		=> 'Sehemu ya jina la mtumizi wa Mail Server haiezi pitisha herufi 50.',
	),
	
	'email_password' => array
	(
		'required'		=> 'Sehemu ya nywila ya Mail Server inahitajika.',
		'length'		=> 'Sehemu ya nywila ya Mail Server lazma iwe baina ya herufi 5 an 50.',
	),
	
	'email_port' => array
	(
		'numeric'		=> 'Sehemu ya port ya Mail Server lazima iwe nambari.',
		'length' 		=> 'Port ya Mail Server ni ndefu sana'
	),
	
	'email_host' => array
	(
		'numeric'		=> 'Sehemu ya port ya Mail Server lazima iwe nambari.',
		'length' 		=> 'Port ya Mail Server ni ndefu sana'
	),
	
	'email_servertype' => array
	(
		'required'		=> 'Sehemu ya aina ya Mail Server inahitajika.',
		'length' 		=> 'Port ya Mail Server ni ndefu sana'
	)		
	
);