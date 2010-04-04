<?php

$lang = array
(
	'name' => array
	(
		'required'=> 'Veuillez indiquer un nom.',
		'length'		=> 'Le nom doit comporter plus de 3 caractères.',
	),
	
	'email' => array
	(
		'required'		=> 'Une adresse email est requise si la case est cochée.',
		'email'		  => 'Adress email invalide',
		'length'	  => 'L\'adresse email doit comporter entre 4 et 64 caractères.'
	),	
	
	'phone' => array
	(
		'length'		=> 'Numéro de téléphone invalide.',
	),
		
	'message' => array
	(
		'required'		=> 'Veuillez entrer un commentaire.'
	),
	
	'captcha' => array
	(
		'required' => 'Veuillez taper le code de sécurité', 
		'default' => 'Veuillez taper un code de sécurité valide'
	)
	
);