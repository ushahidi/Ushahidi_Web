<?php

$lang = array
(
	'contact_name' => array
	(
		'required'		=> 'Jina linahitajika.',
		'length'        => 'Jina lazima liwe na dhati tatu au zaidi.'
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
		'required'    => 'Anwani ya barua pepe lazima iwepo kama umepiga tiki kitiki.',
		'email'		  => 'Anwani ya barua pepe yaonekana sio halali?',
		'length'	  => 'Uga wa barua pepe lazima uwe na vibambo 4 na visizidi 64.'
	),
	
	'captcha' => array
	(
		'required' => 'Tafadhali weka code ya usalama', 
		'default' => 'Tafadhali weka codi ya usalama halali'
	)
	
);
