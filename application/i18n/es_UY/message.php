<?php

$lang = array
(
	'name' => array
	(
		'required'=> 'El campo nombre es obligatorio.',
		'length'		=> 'El campo nombre debe tener al menos 3 caracteres de largo.',
	),
	
	'email' => array
	(
		'required'		=> 'El campo Email es obligatorio cuando el checkbox está marcado..',
		'email'		  => 'El campo Email parece no tener una dirección de email válida?',
		'length'	  => 'El campo Email debe tener al menos 4 y no más de 64 caracteres de largo.'
	),	
	
	'phone' => array
	(
		'length'		=> 'El campo teléfono no es válido.',
	),
		
	'message' => array
	(
		'required'		=> 'El campo comentarios es obligatorio.'
	),
	
	'captcha' => array
	(
		'required' => 'Por favor ingrese el código de seguridad', 
		'default' => 'Por favor ingrese un código de seguridad válido'
	)
	
);
