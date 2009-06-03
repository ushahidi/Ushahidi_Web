<?php

$lang = array
(
	'locale' => array
	(
		'required'		=> 'El locale es obligatorio.',
		'length'		=> 'El campo locale tiene un valor incorrecto. ',
		'alpha_dash'	=> 'El campo locale tiene un valor incorrecto. ',
		'locale'		=> 'El Informe Original y la Traducción tienen el mismo locale (idioma)',
		'exists'		=> 'Este informe ya tiene una traducción para ese idioma'
	),
	
	'incident_title' => array
	(
		'required'		=> 'El campo titulo es obligatorio.',
		'length'		=> 'El campo titulo debe tener al menos 3 y no más de 200 caracteres de largo.'
	),
	
	'incident_description' => array
	(
		'required'		=> 'El campo descripción es obligatorio.'
	),	
	
	'incident_date' => array
	(
		'required'		=> 'El campo fecha es obligatorio.',
		'date_mmddyyyy' => 'El campo fecha parece no contener una fecha válida?',
		'date_ddmmyyyy' => 'El campo fecha parece no contener una fecha válida?'
	),
	
	'incident_hour' => array
	(
		'required'		=> 'El campo hora es obligatorio.',
		'between' => 'El campo hora parece no contener una hora válida?'
	),
	
	'incident_minute' => array
	(
		'required'		=> 'El campo hora es obligatorio.',
		'between' => 'El campo hora parece no contener una hora válida?'
	),
	
	'incident_ampm' => array
	(
		'validvalues' => 'El campo am/pm parece no contener un valor válido?'
	),
	
	'latitude' => array
	(
		'required'		=> 'The latitude field is required.',
		'between' => 'The latitude field does not appear to contain a valid latitude?'
	),
	
	'longitude' => array
	(
		'required'		=> 'El campo longitud es obligatorio.',
		'between' => 'El campo longitud parece no contener una longitud válida?'
	),
	
	'location_name' => array
	(
		'required'		=> 'El campo nombre de lugar es obligatorio.',
		'length'		=> 'El campo nombre de lugar debe tener al menos 3 y no más de 200 caracteres de largo.',
	),
			
	'incident_category' => array
	(
		'required'		=> 'El campo categoría es obligatorio.',
		'numeric'		=> 'El campo categoría parece no contener una categoría válida?'
	),
	
	'incident_news' => array
	(
		'url'		=> 'El campo enlaces a fuentes de noticias parece no contener una URL válida?'
	),
	
	'incident_video' => array
	(
		'url'		=> 'El campo enlaces a vídeo parece no contener una URL válida?'
	),
	
	'incident_photo' => array
	(
		'valid'		=> 'El campo Subir Fotos parece no contener un campo válido',
		'type'		=> 'El campo Subir Fotos parece no contener una imágen válida. Los únicos formatos aceptados son .JPG, .PNG y .GIF.',
		'size'		=> 'Por favor que los tamaños de las fotos subidas no tienen más de 2MB.'
	),
	
	'person_first' => array
	(
		'length'		=> 'El campo nombre debe tener al menos 3 y no más de 100 caracteres de largo.'
	),
	
	'person_last' => array
	(
		'length'		=> 'El campo apellido debe tener al menos 3 y no más de 100 caracteres de largo.'
	),
	
	'person_email' => array
	(
		'email'		  => 'El campo email parece no contener una dirección de email válida?',
		'length'	  => 'El campo email debe tener al menos 4 y no más de 64 caracteres de largo.'
	),
	
	// Admin - Report Download Validation
	'data_point' => array
	(
		'required'		  => 'Por favor seleccionar un tipo válido de informe a descargar',
		'numeric'		  => 'Por favor seleccionar un tipo válido de informe a descargar',
		'between'		  => 'Por favor seleccionar un tipo válido de informe a descargar'
	),
	'data_include' => array
	(
		'numeric'		  => 'Por favor seleccionar un item válido para incluir en la descarga',
		'between'		  => 'Por favor seleccionar  un item válido para incluir en la descarga'
	),
	'from_date' => array
	(
		'date_mmddyyyy'		  => 'El campo fecha DESDE parece no contener una fecha válida?',
		'range'	  => 'Por favor ingrese una fecha DESDE válida. No puede ser mayor al día de hoy.'
	),
	'to_date' => array
	(
		'date_mmddyyyy'		  => 'El campo fecha HASTA parece no contener una fecha válida?',
		'range'	  => 'Por favor ingrese una fecha HASTA válida. No puede ser mayor al día de hoy.',
		'range_greater'	=> 'Su fecha DESDE no puede ser mayor a su fecha HASTA.'
	),
	'custom_field' => array
	(
		'values'		  => 'Por favor ingrese un valor válido para uno de sus items de formulario customizados'
	),
	
	'incident_active' => array
	(
		'required'		=> 'Por favor ingrese un valor válido para Aprobar Este Informe',
		'between'		=> 'Por favor ingrese un valor válido para Aprobar Este Informe'
	),
	
	'incident_verified' => array
	(
		'required'		=> 'Por favor ingrese un valor válido para Verificar Este Informe',
		'between'		=> 'Por favor ingrese un valor válido para Verificar Este Informe'
	),
	
	'incident_source' => array
	(
		'alpha'		=> 'Por favor ingrese un valor válido para Source Reliability',
		'length'		=> 'Por favor ingrese un valor válido para Source Reliability'
	),
	
	'incident_information' => array
	(
		'alpha'		=> 'Por favor ingrese un valor válido para Information Probability',
		'length'		=> 'Por favor ingrese un valor válido para Information Probability'
	)
);
