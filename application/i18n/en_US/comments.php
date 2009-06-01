<?php

$lang = array
(
	'comment_author' => array
	(
		'required'=> 'El nombre del campo es obligatorio.',
		'length'		=> 'El nombre del campo debe tener por lo menos 3 caracteres de largo.',
	),
	
	'comment_description' => array
	(
		'required'		=> 'El campo comentarios es obligatorio.'
	),
	
	'comment_email' => array
	(
		'required'		=> 'El campo Email es obligatorio si el checkbox está marcado.',
		'email'		  => 'The Email field does not appear to contain a valid email address?',
		'length'	  => 'The Email field must be at least 4 and no more 64 characters long.'
	),
	
	'captcha' => array
	(
		'required' => 'Please enter the security code', 
		'default' => 'Please enter a valid security code'
	)
	
);
