<?php
	$lang = array(
		'organization_name' => array
		(
			'required'		=> 'El campo nombre de la organización es obligatorio.',
			'length'		=> 'El campo nombre de la organización debe tener al menos 3 y no más de 70 caracteres de largo.',
			'standard_text' => 'El campo nombre de usuario tiene caracteres no permitidos.',
		),
		
		'organization_website' => array
		(
			'required' => 'Por favor ingrese el sitio web de la organización.',
			'url' => 'Por favor ingrese una URL válida. Ej. http://www.ushahidi.com'
		),
		
		'organization_description' => array
		(
			'required' => 'Por favor ingrese una pequeña descripción de su organización.'
		),
		
		'organization_email' => array
		(
			'email'		  => 'El campo email de la organización parece no tener una dirección de email válida?',
			'length'	  => 'El campo email de la organización debe tener al menos 4 y no más de 100 caracteres de largo.'
		),
		
		'organization_phone1' => array
		(
			'length'		=> 'El campo teléfono 1 de la organización debe tener al menos 3 y no más de 50 caracteres de largo.'
		),
		
		'organization_phone2' => array
		(
			'length'		=> 'El campo teléfono 1 de la organización debe tener al menos 3 y no más de 50 caracteres de largo.'
		)
	);

?>
