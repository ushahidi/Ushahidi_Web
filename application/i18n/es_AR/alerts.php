<?php

$lang = array
(
	'alert_mobile' => array
	(
		'required'		=> 'El campo TelÃ©fono MÃ³vil es obligatorio si el checkbox esta marcado.',
		'numeric'		=> 'El campo TelÃ©fono MÃ³vil no parece contener un numero valido. Por favor ingrese los nÃºmeros solo incluyendo el cÃ³digo del paÃ­s.',
		'one_required'	=> 'Usted debe ingresar su numero de telÃ©fono mÃ³vil o su direcciÃ³n de correo electrÃ³nico.',
		'mobile_check'	=> 'Ese numero de telÃ©fono mÃ³vil ya esta registrado para recibir alertas en esa ubicaciÃ³n',
		'length'		=> 'El campo TelÃ©fono MÃ³vil parece no contener la correcta cantidad de dÃ­gitos.'
	),
	
	'alert_email' => array
	(
		'required'		=> 'El campo Email es obligatorio si el checkbox esta marcado.',
		'email'		  => 'El campo Email parece no tener una direcciÃ³n de correo electrÃ³nica valida?',
		'length'	  => 'El campo Email debe tener entre 4 y 64 caracteres.',
		'email_check'	=> 'Ese correo electrÃ³nico ya esta registrado para recibir alertas para esa ubicaciÃ³n',
		'one_required' => ''
	),
	
	'alert_lat' => array
	(
		'required'		=> 'No ha seleccionado una ubicaciÃ³n valida en el mapa.',
		'between' => 'No ha seleccionado una ubicaciÃ³n valida en el mapa.'
	),
	
	'alert_lon' => array
	(
		'required'		=> 'No ha seleccionado una ubicaciÃ³n valida en el mapa.',
		'between' => 'No ha seleccionado una ubicaciÃ³n valida en el mapa.'
	),

    'code_not_found' => 'Este código de verificaciÃ³n  no ha sido encontrado! Por favor confirma que lo ha ingresado correctamente. Puede usar el formulario que esta mas abajo para re-ingresar su cÃ³digo de verificaciÃ³n:',
    'code_already_verified' => 'Este cÃ³digo ya ha sido verificado anteriormente!',
    'code_verified' => ' Su cÃ³digo fue verificado correctamente. Ahora usted va a recibir alertas sobre incidentes que vayan sucediendo.',
    'mobile_alert_request_created' => 'Su pedido de Alerta MÃ³vil ha sido creada y el mensaje de verificaciÃ³n ya fue enviado a ',
	'verify_code' => 'Usted no va a recibir alertas en esta ubicaciÃ³n hasta que confirme su pedido.',
	'mobile_code' => 'Por favor ingrese a continuaciÃ³n el cÃ³digo de confirmaciÃ³n SMS que recibiÃ³ en su telÃ©fono: ',
	'mobile_ok_head' =>'Su Pedido de Alerta MÃ³vil  ha sido guardada!',
	'mobile_error_head' => 'Su Pedido de Alerta MÃ³vil NO ha sido guardada!',
	'error_body' => 'El sistema no pudo procesar su pedido de confirmaciÃ³n!',
	'email_alert_request_created' => 'Su pedido de Alerta por Correo ElectrÃ³nico  ha sido creado y el mensaje de verificaciÃ³n ha sido enviado a ',
	'email_ok_head' =>'Su pedido de Alerta por Correo ElectrÃ³nico ha sido guardado!',
	'email_error_head' => 'Su pedido de Alerta por Correo ElectrÃ³nico NO ha sido guardado!',
    'create_more_alerts' => 'Regrese a la pagina de Alertas para crear mas alertas',
);
