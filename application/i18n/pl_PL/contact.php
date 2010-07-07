<?php

$lang = array
(
	'contact_name' => array
	(
		'required'		=> 'Pole nazwy musi być wypełnione.',
		'length'        => 'Pole nazwy musi mieć co najmniej 3 znaki.'
	),

	'contact_subject' => array
	(
		'required'		=> 'Pole tematu musi być wypełnione.',
		'length'        => 'Pole tematu musi zawierać co najmniej 3 znaki.'
	),
	
	'contact_message' => array
	(
		'required'        => 'Pole wiadomości musi być wypećnione.'
	),
	
	'contact_email' => array
	(
		'required'    => 'Pole adresu emailowego musi być wypełnione, jeśeli zaznaczone zostało pole wyboru.',
		'email'		  => 'Pole adresu emailowego prawdopodobnie nie zawiera ważnego adresu mailowego',
		'length'	  => 'Pole adresu emailowego musi zawierać co najmniej 4 i nie więcej niż 64 znaki.'
	),
	
	'captcha' => array
	(
		'required' => 'Wprowadź kod bezpieczeństwa', 
		'default' => 'Wprowadź ważny kod bezpieczeństwa'
	)
	
);
