<?php

$lang = array
(
	'contact_name' => array
	(
		'required'		=> 'The name field is required.',
		'length'        => 'The name field must be at least 3 characters long.'
	),

	'contact_subject' => array
	(
		'required'		=> 'The subject field is required.',
		'length'        => 'The subject field must be at least 3 characters long.'
	),
	
	'contact_message' => array
	(
		'required'        => 'The message field is required.'
	),
	
	'contact_email' => array
	(
		'required'    => 'The Email field is required if the checkbox is checked.',
		'email'		  => 'The Email field does not appear to contain a valid email address?',
		'length'	  => 'The Email field must be at least 4 and no more 64 characters long.'
	),
	
	'captcha' => array
	(
		'required' => 'Please enter the security code', 
		'default' => 'Please enter a valid security code'
	)
	
);
