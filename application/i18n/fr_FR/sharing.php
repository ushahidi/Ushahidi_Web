<?php

$lang = array
(
	'sharing_url' => array
	(
		'required'	=> 'Veuillez spécifier l\'URL du site.',
		'url'		=> 'L\'URL du site est incorrecte.',
		'valid'	=> 'L\'URL ne pointe pas vers une instance valide d\'Ushahidi, ou bien celle-ci n\'est pas en mode partage.',
		'exists'	=> 'L\'URL du site existe déjà.',
		'edit'	=> 'Vous ne pouvez pas changer l\'URL du site. Le partage doit être annulé et une nouvelle requête de partage doit être faite.'
	),
	
	'sharing_email' => array
	(
		'email'		  => 'Adresse email incorrecte',
		'required'	=> 'Une adresse email est requise pour votre site. Vous pouvez l\'ajouter dans les réglages.',
	),	
	
	'sharing_color' => array
	(
		'required'		=> 'Veuillez indiquer une couleur.',
		'length'		=> 'Le champ «couleur» doit comporter 6 caractères.',
	),
	
	'sharing_limits' => array
	(
		'required'		=> 'Veuillez indiquer les limites d\'accès.',
		'between'		=> 'Limites d\'accès invalides.',
	),

	'sharing_type' => array
	(
		'between'		=> 'Type de partage non valide',
	)	
);