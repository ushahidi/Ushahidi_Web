<?php

$lang = array
(
	'alert_mobile' => array
	(
		'required'		=> 'El campo Teléfono Móvil es obligatorio si el checkbox esta marcado.',
		'numeric'		=> 'El campo Teléfono Móvil no parece contener un numero valido. Por favor ingrese los números solo incluyendo el código del país.',
		'one_required'	=> 'Usted debe ingresar su numero de teléfono móvil o su dirección de correo electrónico.',
		'mobile_check'	=> 'Ese numero de teléfono móvil ya esta registrado para recibir alertas en esa ubicación',
		'length'		=> 'El campo Teléfono Móvil parece no contener la correcta cantidad de dígitos.'
	),
	
	'alert_email' => array
	(
		'required'		=> 'El campo Email es obligatorio si el checkbox esta marcado.',
		'email'		  => 'El campo Email parece no tener una dirección de correo electrónica valida?',
		'length'	  => 'El campo Email debe tener entre 4 y 64 caracteres.',
		'email_check'	=> 'Ese correo electrónico ya esta registrado para recibir alertas para esa ubicación',
		'one_required' => ''
	),
	
	'alert_lat' => array
	(
		'required'		=> 'No ha seleccionado una ubicación valida en el mapa.',
		'between' => 'No ha seleccionado una ubicación valida en el mapa.'
	),
	
	'alert_lon' => array
	(
		'required'		=> 'No ha seleccionado una ubicación valida en el mapa.',
		'between' => 'No ha seleccionado una ubicación valida en el mapa.'
	),

    'code_not_found' => 'Este código de verificación  no ha sido encontrado! Por favor confirma que lo ha ingresado correctamente. Puede usar el formulario que esta mas abajo para re-ingresar su código de verificación:',
    'code_already_verified' => 'Este código ya ha sido verificado anteriormente!',
    'code_verified' => ' Su código fue verificado correctamente. Ahora usted va a recibir alertas sobre incidentes que vayan sucediendo.',
    'mobile_alert_request_created' => 'Su pedido de Alerta Móvil ha sido creada y el mensaje de verificación ya fue enviado a ',
	'verify_code' => 'Usted no va a recibir alertas en esta ubicación hasta que confirme su pedido.',
	'mobile_code' => 'Por favor ingrese a continuación el código de confirmación SMS que recibió en su teléfono: ',
	'mobile_ok_head' =>'Su Pedido de Alerta Móvil  ha sido guardada!',
	'mobile_error_head' => 'Su Pedido de Alerta Móvil NO ha sido guardada!',
	'error_body' => 'El sistema no pudo procesar su pedido de confirmación!',
	'email_alert_request_created' => 'Su pedido de Alerta por Correo Electrónico  ha sido creado y el mensaje de verificación ha sido enviado a ',
	'email_ok_head' =>'Su pedido de Alerta por Correo Electrónico ha sido guardado!',
	'email_error_head' => 'Su pedido de Alerta por Correo Electrónico NO ha sido guardado!',
    'create_more_alerts' => 'Regrese a la pagina de Alertas para crear mas alertas',
);
