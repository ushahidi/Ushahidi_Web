<?php

$lang = array
(
	'comment_author' => array
	(
		'required'=> 'El campo nombre es obligatorio.',
		'length'		=> 'El campo nombre debe tener al menos 3 caracteres de largo.',
	),
	
	'comment_description' => array
	(
		'required'		=> 'El campo comentarios es obligatorio.'
	),
	
	'comment_email' => array
	(
		'required'		=> 'El campo Email es obligatorio si el checkbox está marcado..',
		'email'		  => 'El campo Email parece no tener una dirección de email válida?',
		'length'	  => 'El campo Email debe tener al menos 4 y no más de 64 caracteres de largo.'
	),
	
	'captcha' => array
	(
		'required' => 'Por favor ingrese el código de seguridad', 
		'default' => 'Por favor ingrese un código de seguridad válido'
	)
	
);
