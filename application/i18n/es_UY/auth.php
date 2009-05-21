<?php

$lang = array
(
	'name' => array
	(
		'required'		=> 'El campo nombre completo es obligatorio.',
		'length'		=> 'El campo nombre completo debe tener por lo menos 3 y no mas de 100 caracteres.',
		'standard_text' => 'El campo nombre de usuario contiene caracteres no permitidos.',
		'login error'	=> 'Por favor revise que ha ingresado el nombre correcto.'
	),
	
	'email' => array
	(
		'required'	  => 'El campo email es obligatorio.',
		'email'		  => 'El campo email parece no contener una dirección de correo electrónico valida?',
		'length'	  => 'El campo email debe tener por lo menos 4 y no mas de 64 caracteres.',
		'exists'	  => 'Lo siento, ya existe una cuenta de usuario que usa ese correo electrónico.',
		'login error' => 'Por favor revise que ha ingresado la dirección de correo electrónico correcta.'
	),

	'username' => array
	(
		'required'		=> 'El campo nombre de usuario es obligatorio.',
		'length'		=> 'El campo nombre de usuario debe tener por lo menos 2 y no mas de 16 caracteres.',
		'standard_text' => 'El campo nombre de usuario contiene caracteres no permitidos.',
		'admin' 		=> 'El rol del usuario administrador no puede ser modificado.',
		'exists'		=> 'Lo siento, este nombre de usuario ya esta siendo usado.',
		'login error'	=> 'Por favor revise que ingreso el nombre de usuario correcto.'
	),

	'password' => array
	(
		'required'		=> 'El campo clave es obligatorio.',
		'length'		=> 'El campo clave debe tener por lo menos 5 y no mas de 16 caracteres en el largo.',
		'standard_text' => 'El campo clave contiene caracteres no permitidos.',
		'login error'	=> 'Por favor revise que ha ingresado la clave correcta.'
	),

	'password_confirm' => array
	(
		'matches' => 'El campo confirmación de la clave debe ser igual al campo clave..'
	),

	'roles' => array
	(
		'required' => 'Usted debe definir al menos un rol.',
		'values' => 'Usted debe seleccionar el rol ADMIN o USUARIO.'
	),
	
	'resetemail' => array
    (
    	'required' => 'El campo email es obligatorio.',
       	'invalid' => 'Lo siento, no tenemos su dirección de correo electrónico',
        'email'  => 'El campo email parece no contener una dirección de correo electrónico valida?',
    )

);
