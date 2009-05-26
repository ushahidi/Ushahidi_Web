<?php
	$lang = array
	(
		'form_title' => array
		(
			'required' => 'Please enter the name of the form.',
			'length'   => 'The form name field must be at least 3 and no more 
				100 characters long.'
		),
		
		'form_description' => array
		(
			'required' => 'Please enter form\'s Description.'
		),
		
		'form_id' => array
		(
			'default' => 'The default form cannot be deleted.',
			'required' => 'Please select which form to add this field to.',
			'numeric' => 'Please select which form to add this field to.'
		),
		
		'field_type' => array
		(
			'required' => 'Please select a Field Type.',
			'numeric' => 'Please select a valid Field Type.'
		),
		
		'field_name' => array
		(
			'required' => 'Please enter Field Name.',
			'length'   => 'The Field Name must be at least 3 and no more 
				100 characters long.'
		),
		
		'field_default' => array
		(
			'length'   => 'The Field Name must be at least 3 and no more 
				200 characters long.'
		),
		
		'field_required' => array
		(
			'required' => 'Please select Yes or No for Field Required',
			'between'   => 'You have entered an invalid value for Field Required'
		),
		
		'field_width' => array
		(
			'between' => 'Please enter a value 0 to 300 for the Field Width'
		),
		
		'field_height' => array
		(
			'between' => 'Please enter a value 0 to 50 for the Field Height'
		),
		
		'field_isdate' => array
		(
			'required' => 'Please select Yes or No for the Date Field',
			'between'   => 'You have entered an invalid value for Date Field'
		)
	);

?>