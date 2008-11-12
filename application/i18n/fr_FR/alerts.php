<?php

$lang = array
(
	'alert_mobile' => array
	(
		'required'		=> 'The Mobile Phone field is required if the checkbox is checked.',
		'numeric'		=> 'The Mobile Phone field does not appear to contain a valid phone. Please input numbers only including Country Code.',
		'one_required'	=> 'You must enter either your Mobile Phone Number or your Email Address.',
		'mobile_check'	=> 'That Mobile Phone Number has already been registered to receive alerts for that location',
		'length'		=> 'The Mobile Phone field does not seem to contain the right amount of digits.'
	),
	
	'alert_email' => array
	(
		'required'		=> 'The Email field is required if the checkbox is checked.',
		'email'		  => 'The Email field does not appear to contain a valid email address?',
		'length'	  => 'The Email field must be at least 4 and no more 64 characters long.',
		'email_check'	=> 'That Email address has already been registered to receive alerts for that location',
		'one_required' => ''
	),
	
	'alert_lat' => array
	(
		'required'		=> 'You have not selected a valid location on the map.',
		'between' => 'You have not selected a valid location on the map.'
	),
	
	'alert_lon' => array
	(
		'required'		=> 'You have not selected a valid location on the map.',
		'between' => 'You have not selected a valid location on the map.'
	)
);