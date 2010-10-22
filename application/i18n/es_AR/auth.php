<?php
	$lang = array(
	'email' => array(
		'email' => 'El campo email parece no contener una direcciÃ³n de correo electrónico valida?',
		'exists' => 'Lo siento, ya existe una cuenta de usuario que usa ese correo electrÃ³nico.',
		'length' => 'El campo email debe tener por lo menos 4 y no mas de 64 caracteres.',
		'required' => 'El campo email es obligatorio.',
	),
	'name' => array(
		'length' => 'El campo nombre completo debe tener por lo menos 3 y no mas de 100 caracteres.',
		'required' => 'El campo nombre completo es obligatorio.',
		'standard_text' => 'El campo nombre de usuario contiene caracteres no permitidos.',
	),
	'password' => array(
		'alpha_numeric' => 'El campo clave contiene caracteres no permitidos.',
		'length' => 'El campo clave debe tener por lo menos 5 y no mas de 16 caracteres en el largo.',
		'login error' => 'Por favor revise que ha ingresado la clave correcta.',
		'matches' => 'Por favor ingrese la misma clave en ambos campos.',
		'required' => 'El campo clave es obligatorio.',
	),
	'password_confirm' => array(
		'matches' => 'El campo confirmación de la clave debe ser igual al campo clave..',
	),
	'resetemail' => array(
		'email' => 'El campo email parece no contener una direcciÃ³n de correo electrónico valida?',
		'invalid' => 'Lo siento, no tenemos su dirección de correo electrónico',
		'required' => 'El campo email es obligatorio.',
	),
	'roles' => array(
		'required' => 'Usted debe definir al menos un rol.',
		'values' => 'Usted debe seleccionar el rol ADMIN o USUARIO.',
	),
	'username' => array(
		'admin' => 'El rol del usuario administrador no puede ser modificado.',
		'alpha' => 'El campo nombre de usuario contiene caracteres no permitidos.',
		'exists' => 'Lo siento, este nombre de usuario ya esta siendo usado.',
		'length' => 'El campo nombre de usuario debe tener por lo menos 2 y no mas de 16 caracteres.',
		'login error' => 'Por favor revise que ingreso el nombre de usuario correcto.',
		'required' => 'El campo nombre de usuario es obligatorio.',
		'superadmin' => 'El rol del usuario super-administrador no puede ser modificado.',
	));
?>
