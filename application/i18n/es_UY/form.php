<?php
	$lang = array
	(
		'form_title' => array
		(
			'required' => 'Por favor ingrese el nombre del formulario.',
			'length'   => 'El campo nombre de formulario debe tener al menos 3 y no más de 100 caracteres de largo.'
		),
		
		'form_description' => array
		(
			'required' => 'Por favor ingrese la descripción del formulario.'
		),
		
		'form_id' => array
		(
			'default' => 'El formulario por defecto no puede ser eliminado.',
			'required' => 'Por favor seleccione a cual formulario hay que agregarle este campo.',
			'numeric' => 'Por favor seleccione a cual formulario hay que agregarle este campo.'
		),
		
		'field_type' => array
		(
			'required' => 'Por favor seleccione un Tipo de Campo.',
			'numeric' => 'Por favor seleccione un Tipo de Campo Válido.'
		),
		
		'field_name' => array
		(
			'required' => 'Por favor ingrese un Nombre de Campo.',
			'length'   => 'El Nombre de Campo debe tener por lo menos 3 y no más de 100 caracteres de largo.'
		),
		
		'field_default' => array
		(
			'length'   => 'El Nombre de Campo debe tener al menos 3 y no más de 200 caracteres de largo.'
		),
		
		'field_required' => array
		(
			'required' => 'Por favor seleccione Si ó No para el Campo Obligatorio',
			'between'   => 'Usted ha ingresado un valor no válido para Campo Obligatorio'
		),
		
		'field_width' => array
		(
			'between' => 'Por favor ingrese un valor entre 0 y 200 para Ancho de Campo'
		),
		
		'field_height' => array
		(
			'between' => 'Por favor ingrese un valor entre 0 y 50 para Alto de Campo'
		),
		
		'field_isdate' => array
		(
			'required' => 'Por favor seleccione Si ó No para Campo Fecha',
			'between'   => 'Usted ha ingresado un valor no valido para Campo Fecha'
		)
	);

?>
