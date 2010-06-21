<?php

/**
 * SETTINGS INTERFACE INTERNATIONALIZATION
 * translated by Ismailov A. altyni@gmail.com
 * ru_KG
 */

$lang = array
(
    'default_location' => 'Местоположение по умолчанию',
    'select_default_location' => 'Выберите местоположение по умолчанию',
    'download_city_list' => 'Получить названия из ГЕОназваний',
    'multiple_countries' => 'Объеденяет ли Ushahidi несколько государств', 
    'map_settings' => 'Настройка карты',
    'configure_map' => 'Настроить карту',
    'default_map_view' => 'Видеть карту по умолчанию',
    'default_zoom_level' => 'уровень Zoom по умолчанию',
    'set_location' => 'Кликните на карту чтобы указать на точное местоположение',
    'map_provider' => array
	(
		'name'		=> 'Провайдер карты',
		'info'		=> 'Выбор провайдера карты происходит напрямую с провайдером. Выберите провайдера, получите API ключ, введите ключ на сайте',
        'choose'    => 'Выберите провайдера карты',
        'get_api'   => 'Получить API ключ',
        'enter_api' => 'Выберите новый API ключ'
	),
    'site' => array
	(
		'display_contact_page' => 'Отоброзить страницу контактов',
        'display_howtohelp_page' => 'Отоброзить страницу "Как помочь" ',
        'email_alerts' => 'Email для оповещения',
        'email_notice' => '<span> Чтобы получить отчёты по почте, пожалуйста <a href="'.url::base().'admin/settings/email"> настройте параметры Вашего email</a>.</span>',
        'email_site' => 'Email Веб сайта',
        'title'		=> 'Параметры сайта',
		'name'		=> 'Названия сайта',
        'tagline'    => 'Заголовок сайта',
        'items_per_page'   => 'Количество элементов на страницу - Главный',
        'items_per_page_admin' => 'Количество элементов на страницу - Admin',
        'allow_reports' => 'Позволить пользователям отправлять отчёты о событиях',
        'allow_comments' => 'Позволить пользователям отправлять комментарии к отчётам',
        'allow_feed' => 'Включить новостную ленту RSS в Веб сайте',
        'allow_clustering' => 'Кластерировать Отчёты по Карте',
        'default_category_colors' => 'Цвет по умолчанию для всей категорий',
        'google_analytics' => 'Google Analytics',
        'google_analytics_example' => 'Web Property ID - Formato: UA-XXXXX-XX',
        'twitter_configuration' => 'Twitter аккаунт',
        'twitter_hashtags' => 'Хэштеги - Отделите запятыми ',
        'laconica_configuration' => 'Laconica аккаунт',
        'laconica_site' => 'Laconica Сайт ',
        'language' => 'Язык Сайта',
        'api_akismet' => 'Akismet ключ',
        'kismet_notice' => 'Защитите от СПАМА используя <a href="http://akismet.com/" target="_blank">Akismet</a> от Automattic. <BR /> Вы можете получить бесплатный API ключ в <a href="http://en.wordpress.com/api-keys/" target="_blank">WordPress.com user account</a>',
        'share_site_stats' => 'Отправлять статистику в API'
	),
	'cleanurl' => array 
	(
		'title' => 'Чистые URLs',
		'enable_clean_url' => 'Позволить чистые URL',
		'clean_url_enabled' => 'Это позволит Ushahidi заходить на "чистые" сайты без вхождения на "index.php".',
		'clean_url_disabled' => 'Ваш сервер не настроен для чистых URL. Настойте Ваш сервер до того как включить данную услугу. Для более подробной информацмм зайдите <a href="http://forums.ushahidi.com/topic/server-configuration-for-apache-mod-rewrite" target="_blank">post</a>',
	),
    'sms' => array
	(
        'title' => 'Настройка SMS',
        'option_1'		=> 'Option 1: Используйте Frontline SMS', 
		'option_2'		=> 'Option 2: Используйте Global SMS Gateway',
        'flsms_description' => 'FrontlineSMS бесплатная программа которая позволяет превратить ноутбук или мобильный телефон в центр управления. Установив, программа позволяет пользователям отправлять и получать СМС для большой группы через мобильные телефоны. Нажмите на кнопу для установки от FrontlineSMS.com',
        'flsms_download'    => 'Скачайте Frontline SMS и установите на Ваш компьютер',
        'flsms_synchronize'   => 'Синхронизировать с Ushahidi',
        'flsms_instructions' => 'Сообщения полученные в FrontlineSMS может быть синхронизировать с Ushahidi. Информацию о том как синхронизировать с Ushahidi, можно найти в <strong><a href="http://wiki.ushahididev.com/doku.php?id=how_to_use_ushahidi_alpha#how_to_setup_frontlinesms_to_sync_with_ushahidi" target="_blank">here</a></strong>. Вы должны получить ключ по ссылке чтобы синхронизировать с FrontlineSMS',
        'flsms_key' => 'Это ключ синхронизации Ushahidi',
        'flsms_link' => 'FrontlineSMS HTTP Post ссылка', 
       'flsms_text_1' => 'Введите номер телефона подключенный к Frontline SMS в поле внизу',
        'flsms_text_2' => 'Введите номер без символов + или скобок',
        'clickatell_text_1' => 'Подключитесь к услуге Clickatell в <a href="https://www.clickatell.com/central/user/client/step1.php?prod_id=2" target="_blank">clicking here</a>',
        'clickatell_text_2' => 'Введите Вашу информацию Clickatell внизу',
        'clickatell_api' => 'Введите ваш Clickatell API Номер',
        'clickatell_username' => 'Введите вашу Clickatell имя пользователя', 
        'clickatell_password' => 'Введите Ваш Clickatell Пароль', 
        'clickatell_check_balance' => 'Проверьте Ваш баланс на Clickatell', 
        'clickatell_load_balance' => ' Загрузите баланс на Clickatell'
	),      
    
    'map' => array
	(
		'zoom'		=> 'Zoom уровень',
		'default_location'	=> 'Выбор провайдера карты происходит напрямую с провайдером. Выберите провайдера, получите API ключ, введите ключ на сайте'
	),   
    
	'site_name' => array
	(
		'required'		=> 'Введите название сайта.', 
		'length'		=> 'Длинна названия сайта должен быть не менее 3х символов и более 50 символов.',
	),

	'site_tagline' => array
	(
		'required'		=> 'Введите поле заголовки.',
		'length'		=> 'Длинна поля заголовки должна быть не менее 3х и  более 100 символов.',
	),

	'site_email' => array
	(
		'email'		  => 'Email веб сайта введен неправильно',
		'length'	  => 'Длинна Email веб сайта не должна содержать менее 4х и более 100 символов.'
	),

	'items_per_page' => array
	(
		'required'		=> 'Количество отчётов на страницу для (Главный).',
		'between' => 'Поле количества на страницу  (Главный) заполнен в неправильном формате'
	),

	'items_per_page_admin' => array
	(
		'required'		=> 'Количество отчётов на страницу для (Admin).',
		'between' => 'Поле количества на страницу  (Admin) заполнен в неправильном формате'
	),

	'allow_reports' => array
	(
		'required'		=> 'Введите поле о допустимости отправлять отчёты.',
		'between' => 'Поле о допустимости отправлять отчёты заполнен неверно'
	),

	'allow_comments' => array
	(
		'required'		=> 'Введите поле о допустимости отправлять комменты.',
		'between' => 'Поле о допустимости отправлять комменты заполнен неверно'
	),

	'allow_stat_sharing' => array
	(
		'required'		=> 'Введите поле об отправке статистики.',
		'between' => 'Поле об отправке статистики заполнен неверно'
	),

	'allow_feed' => array
	(
		'required'		=> 'Введите поле о ленте RSS.',
		'between' => 'Поле о ленте RSS заполнен неверно'
	),

	'default_map_all' => array
	(
		'required'		=> 'Введите поле цвета.',
		'alpha_numeric'		=> 'Поле цвета содержит неверные данные',
		'length'		=> 'Поле цвета должен быть 6ти значным.',
	),

	'api_akismet' => array
	(
		'alpha_numeric'		=> 'Поле Akismet введен неверно',
		'length'		=> 'Поле Akismet введен неверно'
	),		

	'sms_no1' => array
	(
		'numeric'		=> 'Телефон 1 должен содержать только цифры.',
		'length' => 'Телефон 1 содержит неверные значения'
	),

	'sms_no2' => array
	(
		'numeric'		=> 'Телефон 2 должен содержать только цифры.',
		'length' => 'Телефон 2 содержит неверные значения'
	),

	'sms_no3' => array
	(
		'numeric'		=> 'Телефон 3 должен содержать только цифры.',
		'length' => 'Телефон 3 содержит неверные значения'
	),

	'clickatell_api' => array
	(
		'required'		=> 'Введите Clickatell API номер.',
		'length'		=> 'Длинна номера Clickatell API не должна превышать 20 символов.',
	),

	'clickatell_username' => array
	(
		'required'		=> 'Введите имя пользователя Clickatell.',
		'length'		=> 'Длинна имени пользователя Clickatell не должна превышать 50 символов.',
	),

	'clickatell_password' => array
	(
		'required'		=> 'Введите пароль Clickatell.',
		'length'		=> 'Длинна пароли Clickatell не должна превышать 50 символов.',
	),

	'google_analytics' => array
	(
		'length'		=> 'Поле Google Analytics должна содержать действующий Web Property ID в формате UA-XXXXX-XX.',
	),

	'email_username' => array
	(
		'required'		=> 'Введите имя пользователя сервера почты.',
		'length'		=> 'Длинна имени пользователя сервера почты не должна превышать 50 символов.',
	),

	'email_password' => array
	(
		'required'		=> 'Введите пароль сервера почты.',
		'length'		=> 'Длинна пароли сервера почты не должна превышать 50 символов.',
	),

	'email_port' => array
	(
		'numeric'		=> 'Порт сервера почты должна содержать только цифры.',
		'length' 		=> 'Поле порта Сервера почты слишком длинный'
	),

	'email_host' => array
	(
		'numeric'		=> 'Хост сервера почты должна содержать только цифры.',
		'length' 		=> 'Поле хоста Сервера почты слишком длинный'
	),

	'email_servertype' => array
	(
		'required'		=> 'Введите тип Сервера почты.',
		'length' 		=> 'Поле порта Сервера почты слишком длинный'
	)		

);