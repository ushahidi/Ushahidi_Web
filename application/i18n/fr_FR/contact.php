<?php

$lang = array
(
	'contact_name' => array
	(
		'required'		=> 'Veuillez indiquer un nom.',
		'length'        => 'Le nom doit comporter plus de 3 caractères.'
	),

	'contact_subject' => array
	(
		'required'		=> 'Veuillez indiquer un object.',
		'length'        => 'L\'objet doit comporter plus de 3 caractères.'
	),

	'contact_message' => array
	(
		'required'        => 'Veuillez entrer un message.'
	),

	'contact_email' => array
	(
		'required'    => 'Une adresse email est requise si la case est cochée.',
		'email'		  => 'Addresse email invalide.',
		'length'	  => 'L\'adresse email doit comporter entre 4 et 64 caractères.'
	),

	'captcha' => array
	(
		'required' => 'Veuillez entrer le code de sécurité', 
		'default' => 'Veuillez entrer un code de sécurité valide'
	)
);
