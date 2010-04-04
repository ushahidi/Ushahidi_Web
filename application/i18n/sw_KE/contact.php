<?php

$lang = array
(
	'contact_name' => array
	(
		'required'		=> 'Sehemu ya jina inahitajika.',
		'length'        => 'Sehemu ya jina lazima iwe herufi 3 au zaidi.'
	),

	'contact_subject' => array
	(
		'required'		=> 'Sehemu ya maudhui inahitajika.',
		'length'        => 'Sehemu ya maudhui lazima iwe herufi 3 au zaidi.'
	),
	
	'contact_message' => array
	(
		'required'        => 'Sehemu ya ujumbe inahitajika.'
	),
	
	'contact_email' => array
	(
		'required'    => 'Sehemu ya anwani ya barua pepe inahitajika kama sanduku tiki yake imewekwa tiki.',
		'email'		  => 'Anwani ya barua pepe inaonekana si sahihi',
		'length'	  => 'Anwani ya barua pepe lazima iwe baina ya herufi 4 na 64.'
	),
	
	'captcha' => array
	(
		'required' => 'Tafadhali andika ishara ya ulinzi', 
		'default' => 'Tafadhali andika ishara ya ulinzi iliyo sahihi'
	)
	
);
