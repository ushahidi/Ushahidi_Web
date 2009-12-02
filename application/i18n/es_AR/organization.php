<?php
	$lang = array(
		'organization_name' => array
		(
			'required'		=> 'El campo nombre de la organizaciÃ³n es obligatorio.',
			'length'		=> 'El campo nombre de la organizaciÃ³n debe tener al menos 3 y no mÃ¡s de 70 caracteres de largo.',
			'standard_text' => 'El campo nombre de usuario tiene caracteres no permitidos.',
		),
		
		'organization_website' => array
		(
			'required' => 'Por favor ingrese el sitio web de la organizaciÃ³n.',
			'url' => 'Por favor ingrese una URL vÃ¡lida. Ej. http://www.ushahidi.com'
		),
		
		'organization_description' => array
		(
			'required' => 'Por favor ingrese una pequeÃ±a descripciÃ³n de su organizaciÃ³n.'
		),
		
		'organization_email' => array
		(
			'email'		  => 'El campo email de la organizaciÃ³n parece no tener una direcciÃ³n de email vÃ¡lida?',
			'length'	  => 'El campo email de la organizaciÃ³n debe tener al menos 4 y no mÃ¡s de 100 caracteres de largo.'
		),
		
		'organization_phone1' => array
		(
			'length'		=> 'El campo telÃ©fono 1 de la organizaciÃ³n debe tener al menos 3 y no mÃ¡s de 50 caracteres de largo.'
		),
		
		'organization_phone2' => array
		(
			'length'		=> 'El campo telÃ©fono 1 de la organizaciÃ³n debe tener al menos 3 y no mÃ¡s de 50 caracteres de largo.'
		)
	);

?>
