<?php

$lang = array
(
	'comment_author' => array
	(
		'required'		=> 'Pole nazwy musi być wypełnione.',
		'length'        => 'Pole nazwy musi zawierać co najmniej 3 znaki.'
	),
	
	'comment_description' => array
	(
		'required'        => 'Pole komentarza musi być wypełnione.'
	),
	
	'comment_email' => array
	(
		'required'    => 'Pole adresu emailowego musi być wypełnione, jeżeli zaznaczono pole wyboru.',
		'email'		  => 'Pole adresu emailowego prawdopodobnie nie zawiera ważnego adresu emailowego',
		'length'	  => 'Pole adresu emailowego musi zawierać co najmniej 4 i nie więcej niż 64 znaki.'
	),
	
	'captcha' => array
	(
		'required' => 'Wprowadź kod bezpieczeństwa', 
		'default' => 'Wprowadź ważny kod bezpieczeństwa'
	)
	
);
