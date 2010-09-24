<?php

/**
 * SETTINGS INTERFACE INTERNATIONALIZATION
 *
 * en_US
 */

$lang = array
(
    'default_location' => 'المكان الأصلى',
    'select_default_location' => 'الدولة الأصلية',
    'download_city_list' => 'الحصول على المدن من الأسماء الجغرافية',
    'multiple_countries' => 'Does this Ushahidi Deployment Span Multiple Countries', 
    'map_settings' => 'إعدادات الخريطة',
    'configure_map' => 'تكوين الخريطة',
    'default_map_view' => 'الرؤية الأصلية للخريطة',
    'default_zoom_level' => 'Default Zoom Level',
    'set_location' => 'Click and drag the map to set your exact location',
    'map_provider' => array
	(
		'name'		=> 'موفر الخرائط',
		'info'		=> 'إعداد موفر الخرائط الخاص بك هى عملية مباشرة. اختار موفر خرائط, أحصل على مفتاح API من الموقع, ثم أدخل هذا المفتاح',
        'choose'    => 'اختار موفر خرائط',
        'get_api'   => 'أحصل على API مفتاح',
        'enter_api' => 'أدخل مفتاح API جديد'
	),
    'site' => array
	(
		'display_contact_page' => 'اعرض صفحة معلومات الاتصال',
        'display_howtohelp_page' => 'اعرض صفحة كيف يمكننا المساعدة',
        'email_alerts' => 'عنوان البريد الإليكترونى الخاص بالتنبيهات',
        'email_notice' => '<span>In order to receive reports by email, please <a href="'.url::base().'admin/settings/email">configure your email account settings</a>.</span>',
        'email_site' => 'البريد الإليكترونى للموقع',
        'title'		=> 'إعدادات الموقع',
		'name'		=> 'اسم الموقع',
        'tagline'    => 'سطر الوسم',
        'items_per_page'   => 'بنود الصفحة الواحدة - الواجهة النهائية',
        'items_per_page_admin' => 'بنود الصحفة الواحدة - المسئول الإدارى',
        'allow_reports' => 'صرح للمستخدمين بإصدار تقارير',
        'allow_comments' => 'صرح للمستخدمين بوضع تعليقات على التقارير',
        'allow_feed' => 'السماح بإرسال آخر الأخبار بنظام آر إس إس',
        'allow_clustering' => 'جمع التقارير على الخريطة',
        'default_category_colors' => 'وضع لون أساسى لكل الفئات',
        'google_analytics' => 'تحاليل جوجل',
        'google_analytics_example' => 'هوية ملكية الشبكة - Formato: UA-XXXXX-XX',
        'twitter_configuration' => 'اعتماد رسائل التويتر',
        'twitter_hashtags' => 'الوسوم المختلطة - الفصل بفواصل ',
        'laconica_configuration' => 'Laconica Credentials',
        'laconica_site' => 'Laconica Site ',
        'language' => 'لغة الموقع',
        'api_akismet' => 'Akismet Key',
        'kismet_notice' => 'Prevent comment spam using <a href="http://akismet.com/" target="_blank">Akismet</a> from Automattic. <BR />You can get a free API key by registering for a <a href="http://en.wordpress.com/api-keys/" target="_blank">WordPress.com user account</a>',
        'share_site_stats' => 'Share Site Statistics in API'
	),
	'cleanurl' => array 
	(
		'title' => 'Clean URLs',
		'enable_clean_url' => 'Enable Clean URLs',
		'clean_url_enabled' => 'هذه الخاصية تمكن من الولوج إلى أوشاهيدى من خلال الروابط النظيفة. Without "index.php" in the URL.',
		'clean_url_disabled' => 'الخادم الخاص بم غير معرف على التعامل مع خاصية الروابط النظيفة. ستحتاج لتغيير خصائص الخادم الخاص بك ليتعامل مع خاصية الروابط النظيفة. تعرف على المزيد حول كيفية التعامل مع خاصية الروابط النظيفة بالذهاب إلىche-mod-rewrite" target="_blank">post</a>',
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
		'required'		=> 'يجب ملء حقل اسم الموقع',
		'length'		=> 'حقل اسم الموقع يجب ألا يقل عن 3 والا يزيد عن 50 حرف',
	),
	
	'site_tagline' => array
	(
		'required'		=> 'يجب ملء حقل سطر الوسم',
		'length'		=> 'حقل سطر الوسم يجب ألا يقل عن 3 وألا يزيد عن 100 حرف',
	),
	
	'site_email' => array
	(
		'email'		  => 'حقل البريد الإليكترونى للموقع لا يحتوى على قيمة صالحة',
		'length'	  => 'حقل البريد الإليكترونى للموقع يجب ألا يقل عن 4 وألا يزيد عن 100'
	),
	
	'items_per_page' => array
	(
		'required'		=> 'يجب ملء حقل بنود الصفحة الواحدة للواجهة',
		'between' => 'حقل البنود الصفحة الواحدة للواجهة لايحتوى على قيمة صالحة'
	),
	
	'items_per_page_admin' => array
	(
		'required'		=> 'يجب ملء حقل بنود الصفحة الواحدة للمسئول الإدارى',
		'between' => 'حقل بنود الصفحة الواحدة للمسئول الإدارى لاتحتوى على قيمة صالحة'
	),
	
	'allow_reports' => array
	(
		'required'		=> 'يجب ملء حقل التقارير المسموحة',
		'between' => 'حقل التقارير المسموحة لايحتوى على قيمة صالحة'
	),
	
	'allow_comments' => array
	(
		'required'		=> 'يجب ملء حقل التعليقات المسموحة',
		'between' => 'حقل التعليقات المسموحة لايحتوى على قيمة صالحة'
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
		'required'		=> 'يجب ملء حقل اللون',
		'alpha_numeric'		=> 'حقل التغذية المرجعية بحسب اللون لا تحتوى على قيمة صالحة',
		'length'		=> 'حقل اللون يجب الا يتعدى 6 حروف',
	),
	
	'api_akismet' => array
	(
		'alpha_numeric'		=> 'The Akismet field does not appear to contain a valid value?',
		'length'		=> 'The Akismet field does not appear to contain a valid value?'
	),		
	
	'sms_no1' => array
	(
		'numeric'		=> 'حقل التليفون 1 يجب ان يحتوى على ارقام فقط',
		'length' => 'لايبدو ان حقل التليفون 1 يحتوى على قيمة صالحة'
	),
	
	'sms_no2' => array
	(
		'numeric'		=> 'حقل التليفون 2 يجب ان يحتوى على ارقام فقط',
		'length' => 'محتويات حقل التليفون 2 طويلة'
	),
	
	'sms_no3' => array
	(
		'numeric'		=> 'يجب ان يحتوى على حقل التليفون 3 على ارقام فقط',
		'length' => 'محتويات حقل التليفون 3 طويلة'
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
		'length'		=> 'حقل تحليات جوجل يجب ان تحتوى على هوية صحيحة لملكية الويب بالهيئة الآتية UA-XXXXX-XX.',
	),
	
	'email_username' => array
	(
		'required'		=> 'يجب ملء حقل اسم المستخدم لخادم البريد',
		'length'		=> 'حقل اسم المستخدم لخادم البريد يجب ألا يزيد عن 50 حرف',
	),
	
	'email_password' => array
	(
		'required'		=> 'يجب ملء حقل كلمة المرور لخادم البريد',
		'length'		=> 'حقل كلمة المرور يجب ألا يقل عن 5 ولا يزيد عن 50 حرف',
	),
	
	'email_port' => array
	(
		'numeric'		=> 'يجب ان يحتوى حقل ميناء خادم البريد على ارقام فقط',
		'length' 		=> 'محتوى حقل ميناء خادم البريد طويل'
	),
	
	'email_host' => array
	(
		'numeric'		=> 'يجب ان يحتوى حقل ميناء خادم البريد على ارقام فقط',
		'length' 		=> 'محتوى حقل ميناء خادم البريد طويل'
	),
	
	'email_servertype' => array
	(
		'required'		=> 'يجب ملء حقل نوع خادم البريد',
		'length' 		=> 'محتوى حقل ميناء خادم البريد طويل'
	)		
	
);