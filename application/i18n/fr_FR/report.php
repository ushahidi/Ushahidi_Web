<?php

$lang = array
(
	'incident_title' => array
	(
		'required'		=> 'Veuillez indiquer un titre.',
		'length'		=> 'Le titre doit comporter entre 3 et 200 caractères',
	),
	
	'incident_description' => array
	(
		'required'		=> 'Veuillez indiquer une description.'
	),	
	
	'incident_date' => array
	(
		'required'		=> 'Veuillez indiquer la date.',
		'date_mmddyyyy' => 'date incorrecte',
		'date_ddmmyyyy' => 'date incorrecte'
	),
	
	'incident_hour' => array
	(
		'required'		=> 'Veuillez indiquer une heure.',
		'between' => 'Format de l\'heure invalide'
	),
	
	'incident_minute' => array
	(
		'required'		=> 'Veuillez indiquer les minutes.',
		'between' => 'Format des minutes invalide'
	),
	
	'incident_ampm' => array
	(
		'validvalues' => 'Champ «am/pm» invalide'
	),
	
	'latitude' => array
	(
		'required'		=> 'Veuillez indiquer une latitude.',
		'between' => 'Format de latitude invalide'
	),
	
	'longitude' => array
	(
		'required'		=> 'Veuillez indiquer une longitude.',
		'between' => 'Format de longitude invalide'
	),
	
	'location_name' => array
	(
		'required'		=> 'Veuillez indiquer un lieu.',
		'length'		=> 'Le champ «lieu» doit comporter entre 3 et 200 caractères.',
	),
			
	'incident_category' => array
	(
		'required'		=> 'Veuillez indiquer une catégorie.',
		'numeric'		=> 'Catégorie invalide'
	),
	
	'incident_news' => array
	(
			'url'		=> 'URL de source d\'actualité invalide'
	),
	
	'incident_video' => array
	(
		'url'		=> 'URL de vidéo invalide'
	),
	
	'incident_photo' => array
	(
		'valid'		=> 'Fichier image invalide',
		'type'		=> 'Format de fichier image invalide. Seuls les formats .JPG, .PNG and .GIF. sont acceptés',
		'size'		=> 'Photo de taille supérieure à 2Mo. Veuiller choisir une photo plus petite.'
	),
	
	'person_first' => array
	(
		'length'		=> 'Le prénom doit comporter entre 3 et 100 caractères.'
	),
	
	'person_last' => array
	(
		'length'		=> 'Le nom de famille doit comporter entre 3 et 100 caractères.'
	),
	
	'person_email' => array
	(
		'email'		  => 'Adresse email invalide.',
		'length'	  => 'L\'adresse email doit comporter entre 4 et 64 caractères.'
	)
);