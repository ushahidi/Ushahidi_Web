<?php

$lang = array
(
	'name' => array
	(
		'required'=> 'The name field is required.',
		'length'		=> 'The name field must be at least 3 characters long.',
	),
	
	'email' => array
	(
		'required'		=> 'The Email field is required if the checkbox is checked.',
		'email'		  => 'The Email field does not appear to contain a valid email address?',
		'length'	  => 'The Email field must be at least 4 and no more 64 characters long.'
	),	
	
	'phone' => array
	(
		'length'		=> 'The phone field is not valid.',
	),
		
	'message' => array
	(
		'required'		=> 'The comments field is required.'
	),
	
	'captcha' => array
	(
		'required' => 'Please enter the security code', 
		'default' => 'Please enter a valid security code'
	)
	
);