<?php

$lang = array
(
	'site_name' => array
	(
		'required'		=> 'El campo nombre del sitio es obligatorio.',
		'length'		=> 'El campo nombre del sitio debe tener al menos 3 y no más de 50 caracteres de largo.',
	),
	
	'site_tagline' => array
	(
		'required'		=> 'El campo tagline es obligatorio.',
		'length'		=> 'El campo tagline debe tener al menos 3 y no más de 100 caracteres de largo.',
	),
	
	'site_email' => array
	(
		'email'		  => 'El campo email del sitio parece no contener una dirección de email válida?',
		'length'	  => 'El campo email del sitio debe tener al menos 4 y no más de 100 caracteres de largo.'
	),
	
	'items_per_page' => array
	(
		'required'		=> 'El campo items por página (Frontend) es obligatorio.',
		'between' => 'El campo items por página (Frontend) parece no contener un valor válido?'
	),
	
	'items_per_page_admin' => array
	(
		'required'		=> 'El campo items por página (Admin) es obligatorio.',
		'between' => 'El campo items por página (Admin)parece no contener un valor válido?'
	),
	
	'allow_reports' => array
	(
		'required'		=> 'El campo informes permitidos es obligatorio.',
		'between' => 'El campo informes permitidos parece no contener un valor válido?'
	),
	
	'allow_comments' => array
	(
		'required'		=> 'El campo comentarios permitidos es obligatorio.',
		'between' => 'El campo comentarios permitidos parece no contener un valor válido?'
	),
	
	'allow_stat_sharing' => array
	(
		'required'		=> 'El campo "stat sharing" es obligatorio.',
		'between' => 'El campo "stat sharing" parece no contener un valor válido?'
	),
	
	'allow_feed' => array
	(
		'required'		=> 'El campo feed incluido es obligatorio.',
		'between' => 'El campo feed incluido parece no contener un valor válido?'
	),
	
	'sms_no1' => array
	(
		'numeric'		=> 'El campo teléfono 1 debe contener sólo números.',
		'length' => 'El campo teléfono 1 parece no contener un valor válido?'
	),
	
	'sms_no2' => array
	(
		'numeric'		=> 'El campo teléfono 2 debe sólo contener números.',
		'length' => 'El campo teléfono 2 es demasiado largo'
	),
	
	'sms_no3' => array
	(
		'numeric'		=> 'El campo teléfono 3 debe sólo contener números.',
		'length' => 'El campo teléfono 3 es demasiado largo'
	),
	
	'clickatell_api' => array
	(
		'required'		=> 'El campo número Clickatell API es obligatorio.',
		'length'		=> 'El campo número Clickatell API debe no tener más de 20 caracteres de largo.',
	),
	
	'clickatell_username' => array
	(
		'required'		=> 'El campo Nombre de Usuario Clickatell es obligatorio.',
		'length'		=> 'El campo Nombre de Usuario Clickatell debe no tener más de 50 caracteres de largo.',
	),
	
	'clickatell_password' => array
	(
		'required'		=> 'El campo Clave de Clickatell es obligatorio.',
		'length'		=> 'El campo Clave de Clickatell debe tener al menos 5 y no más de 50 caracteres de largo.',
	),

	'google_analytics' => array
	(
		'length'		=> 'El campo Google Analytics debe contener un ID Web Property válido en el formato UA-XXXXX-XX.',
	)		
	
);
