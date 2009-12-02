<?php
$lang = array
(
	'default_location' => 'Ubicación Predeterminada',
    'select_default_location' => 'Por favor escoja la ubicación predeterminada',
    'download_city_list' => 'Descargar lista de ciudades',
    'multiple_countries' => 'Este sistema se utiliza en múltiples países?', 
    'map_provider' => array
	(
		'name'		=> 'Proveedor de mapas',
		'info'		=> 'Ingrese la información de configuración del proveedor de servicios de mapeo',
        'choose'    => 'Escoja un proveedor',
        'get_api'   => 'Obtenga la llave del API',
        'enter_api' => 'Ingrese la llave del API'
	),
    'site' => array
	(
		'title'		=> 'Configuaración del Sistema',  //Site Settings
		'name'		=> 'Nombre del sitio',
        'tagline'    => 'Subtítulo',
        'items_per_page'   => 'Items por Página - Página Inicial',
        'items_per_page_admin' => 'Items por Página - Administración',
        'allow_reports' => 'Los usuarios pueden enviar eventos?',
        'allow_comments' => 'Los usuarios pueden agregar informacion a los eventos?',
        'allow_feed' => 'Incluir noticias?',
        'allow_clustering' => 'Agrupar los eventos en el mapa?',
        'default_category_colors' => 'Colores predeterminados para las categorias?',
        'google_analytics' => 'Google Analytics',
        'google_analytics_example' => 'Web Property ID - Formato: UA-XXXXX-XX',
        'twitter_configuration' => 'Configuración de Twitter',
        'twitter_hashtags' => 'Hashtags (Separar con comas)',
        'laconica_configuration' => 'Configuración de Laconica',
        'laconica_site' => 'Website Laconica',
        'api_akismet' => 'Llave API de Akismet'
	),    
    'sms' => array
	(
        'options' => 'Opciones de Configuración SMS', //SMS Setup Options		
        'option_1'		=> 'Opción 1: Use Frontline SMS',  //Option 1: Use Frontline SMS
		'option_2'		=> 'Opción 2: Use un sistema SMS externo', //Option 2: Use a Global SMS Gateway
        'flsms_download'    => 'Descargar Frontline SMS e instalarlo en su equipo',
        'flsms_synchronize'   => 'Sincronizar con Ushahidi',
        'flsms_instructions' => 'Los mensajes recibidos en FrontlineSMS pueden ser sincronizados con este sistema. Se encuentran instrucciones detalladas en <strong><a href="http://wiki.ushahididev.com/doku.php?id=how_to_use_ushahidi_alpha#how_to_setup_frontlinesms_to_sync_with_ushahidi" target="_blank">here</a></strong>. La clave y el URL que se indican a continuación serán necesarias para esta sincronización',
        /*
        'Messages received into a FrontlineSMS hub can be synched with Ushahidi. Detailed instructions on how to sync can be found by <strong><a href="http://wiki.ushahididev.com/doku.php?id=how_to_use_ushahidi_alpha#how_to_setup_frontlinesms_to_sync_with_ushahidi" target="_blank">here</a></strong>. You will require the key and link below to set up the sync with FrontlineSMS'
        */
        'flsms_key' => 'Su Clave de Sincronización', //Your Ushahidi Sync Key
        'flsms_link' => 'Enlace HTTP POST para FrontlineSMS', //FrontlineSMS HTTP Post LINK
        'flsms_text_1' => 'Ingrese el número telefónico para ingreso a Frontline SMS', //Enter phone number(s) connected to Frontline SMS in the field(s) below
        'flsms_text_2' => 'Ingrese el número sin "+" ni guiones', //Enter the number without any + or dashes below
        'clickatell_text_1' => 'Asociése al servicio Clickatells <a href="https://www.clickatell.com/central/user/client/step1.php?prod_id=2" target="_blank">seleccionando aquí</a>',
        /* Sign up for Clickatells service by <a href="https://www.clickatell.com/central/user/client/step1.php?prod_id=2" target="_blank">clicking here</a>
        */
        'clickatell_text_2' => 'Ingrese su información de cuenta de Clickatell', //Enter your Clickatell access information below
        'clickatell_api' => 'Clave del API', //Your Clickatell API Number
        'clickatell_username' => 'Usuario', //Your Clickatell User Name
        'clickatell_password' => 'Clave', //Your Clickatell Password
        'clickatell_check_balance' => 'Saldo de Cuenta', //Check Your Clickatell Credit Balance
        'clickatell_load_balance' => 'Cargar Saldo' //Load Credit Balance
	),      
    
    'map' => array
	(
		'zoom'		=> 'Nivel de zoom',
		'default_location'	=> 'Ingrese la información de configuración del proveedor de servicios de mapeo'
	),   
    // Pre-existing labels
    'site_name' => array
	(
		'required'		=> 'El campo nombre del sitio es obligatorio.',
		'length'		=> 'El campo nombre del sitio debe tener al menos tres y no más de 50 caracteres de largo.',
	),
	
	'site_tagline' => array
	(
		'required'		=> 'El campo tagline es obligatorio.',
		'length'		=> 'El campo tagline debe tener al menos 3 y no mÃ¡s de 100 caracteres de largo.',
	),
	
	'site_email' => array
	(
		'email'		  => 'El campo email del sitio parece no contener una direcciÃ³n de email vÃ¡lida?',
		'length'	  => 'El campo email del sitio debe tener al menos 4 y no mÃ¡s de 100 caracteres de largo.'
	),
	
	'items_per_page' => array
	(
		'required'		=> 'El campo items por pÃ¡gina (Frontend) es obligatorio.',
		'between' => 'El campo items por pÃ¡gina (Frontend) parece no contener un valor vÃ¡lido?'
	),
	
	'items_per_page_admin' => array
	(
		'required'		=> 'El campo items por pÃ¡gina (Admin) es obligatorio.',
		'between' => 'El campo items por pÃ¡gina (Admin)parece no contener un valor vÃ¡lido?'
	),
	
	'allow_reports' => array
	(
		'required'		=> 'El campo informes permitidos es obligatorio.',
		'between' => 'El campo informes permitidos parece no contener un valor vÃ¡lido?'
	),
	
	'allow_comments' => array
	(
		'required'		=> 'El campo comentarios permitidos es obligatorio.',
		'between' => 'El campo comentarios permitidos parece no contener un valor vÃ¡lido?'
	),
	
	'allow_feed' => array
	(
		'required'		=> 'El campo feed incluido es obligatorio.',
		'between' => 'El campo feed incluido parece no contener un valor vÃ¡lido?'
	),
	
	'sms_no1' => array
	(
		'numeric'		=> 'El campo telÃ©fono 1 debe contener sÃ³lo nÃºmeros.',
		'length' => 'El campo telÃ©fono 1 parece no contener un valor vÃ¡lido?'
	),
	
	'sms_no2' => array
	(
		'numeric'		=> 'El campo telÃ©fono 2 debe sÃ³lo contener nÃºmeros.',
		'length' => 'El campo telÃ©fono 2 es demasiado largo'
	),
	
	'sms_no3' => array
	(
		'numeric'		=> 'El campo telÃ©fono 3 debe sÃ³lo contener nÃºmeros.',
		'length' => 'El campo telÃ©fono 3 es demasiado largo'
	),
	
	'clickatell_api' => array
	(
		'required'		=> 'El campo nÃºmero Clickatell API es obligatorio.',
		'length'		=> 'El campo nÃºmero Clickatell API debe no tener mÃ¡s de 20 caracteres de largo.',
	),
	
	'clickatell_username' => array
	(
		'required'		=> 'El campo Nombre de Usuario Clickatell es obligatorio.',
		'length'		=> 'El campo Nombre de Usuario Clickatell debe no tener mÃ¡s de 50 caracteres de largo.',
	),
	
	'clickatell_password' => array
	(
		'required'		=> 'El campo Clave de Clickatell es obligatorio.',
		'length'		=> 'El campo Clave de Clickatell debe tener al menos 5 y no mÃ¡s de 50 caracteres de largo.',
	),

	'google_analytics' => array
	(
		'length'		=> 'El campo Google Analytics debe contener un ID Web Property vÃ¡lido en el formato UA-XXXXX-XX.',
	)		
	
);
