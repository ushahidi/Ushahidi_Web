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
		'required'		=> 'El campo Email es obligatorio cuando el checkbox estÃ¡ marcado..',
		'email'		  => 'El campo Email parece no tener una direcciÃ³n de email vÃ¡lida?',
		'length'	  => 'El campo Email debe tener al menos 4 y no mÃ¡s de 64 caracteres de largo.'
	),	
	
	'phone' => array
	(
		'length'		=> 'El campo telÃ©fono no es vÃ¡lido.',
	),
		
	'message' => array
	(
		'required'		=> 'El campo comentarios es obligatorio.'
	),
	
	'captcha' => array
	(
		'required' => 'Por favor ingrese el cÃ³digo de seguridad', 
		'default' => 'Por favor ingrese un cÃ³digo de seguridad vÃ¡lido'
	)
	
);
