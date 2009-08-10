<?php

$lang = array
(
	'sharing_url' => array
	(
		'required'	=> 'The site url is required.',
		'url'		=> 'The site url field does not appear to contain a valid URL?',
		'valid'	=> 'The site url does not appear to be a valid Ushahidi instance, or is not Sharing enabled.',
		'exists'	=> 'The site url already exists',
		'edit'	=> 'You can\'t edit the URL of remote site. This share can be deleted and a new share request made.'
	),
	
	'sharing_email' => array
	(
		'email'		  => 'The email field does not appear to contain a valid email address?',
		'required'	=> 'Your site email is required. Please go to Settings to add an email address.',
	),	
	
	'sharing_color' => array
	(
		'required'		=> 'The color field is required.',
		'length'		=> 'The color field must be 6 characters long.',
	),
	
	'sharing_limits' => array
	(
		'required'		=> 'The Access limits field is required.',
		'between'		=> 'The Access limits does not appear to be valid?',
	),

	'sharing_type' => array
	(
		'between'		=> 'The Share Type does not appear to be valid?',
	)	
);