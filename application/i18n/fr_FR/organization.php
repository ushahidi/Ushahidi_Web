<?php
	$lang = array(
		'organization_name' => array
		(
			'required'		=> 'Veuillez spécifier un nom d\'organisation.',
			'length'		=> 'Le nom d\'organisation doit comporter entre 3 et 70 caractères.',
			'standard_text' => 'Le nom d\'utilisateur contient des caractères invalides.',
		),
		
		'organization_website' => array
		(
			'required' => 'Veuillez entrer l\'adresse web de l\'organisation.',
			'url' => 'Veuillez entrer un URL valide. Ex: http://www.ushahidi.com'
		),
		
		'organization_description' => array
		(
			'required' => 'Veuillez entrer une courte description de l\'organisation.'
		),
		
		'organization_email' => array
		(
			'email'		  => 'L\'adresse email de l\'organisation est invalide.',
			'length'	  => 'L\'adresse email de l\'organisation doit comporter entre 4 et 100 caractères.'
		),
		
		'organization_phone1' => array
		(
			'length'		=> 'Le premier numéro de téléphone de l\'organisation doit comporter entre 3 et 50 caractères.'
		),
		
		'organization_phone2' => array
		(
			'length'		=> 'Le second numéro de téléphone de l\'organisation doit comporter entre 3 et 50 caractères.'
		)
	);

?>