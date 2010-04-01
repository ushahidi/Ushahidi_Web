<?php

$lang = array
(
	'comment_author' => array
	(
		'required'=> 'Veuillez indiquer un nom.',
		'length'		=> 'Le nom doit comporter au moins 3 caractères.',
	),
	
	'comment_description' => array
	(
		'required'		=> 'Veuillez remplir le champ «commentaire».'
	),
	
	'comment_email' => array
	(
		'required'		=> 'Une adresse email doit être spécifiée si la case est cochée.',
		'email'		  => 'Adresse email invalide.',
		'length'	  => 'L\'adresse email doit comporter entre 4 et 64 caractères.'
	),
	
	'captcha' => array
	(
		'required' => 'Indiquez le code de sécurité',
		'default' => 'Indiquez un code de sécurité valide'
	)
	
);