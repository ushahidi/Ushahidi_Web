<?php
	$lang = array
	(
		'form_title' => array
		(
			'required' => 'Veuillez entrer le nom du formulaire.',
			'length'   => 'Le nom du formulaire doit comporter entre 3 et 100 caractères.'
		),
		
		'form_description' => array
		(
			'required' => 'Veuillez entrer une description du formulaire.'
		),
		
		'form_id' => array
		(
			'default' => 'Le formulaire pas défault ne peut pas être supprimé.',
			'required' => 'Veuillez selectionner le formulaire auquel ce champ appartient.',
			'numeric' => 'Veuillez selectionner le formulaire auquel ce champ appartient.'
		),
		
		'field_type' => array
		(
			'required' => 'Veuillez sélectionner un type de champ.',
			'numeric' => 'Veuillez sélectionner un type de champ valide.'
		),
		
		'field_name' => array
		(
			'required' => 'Veuillez spécifier le nom du champ.',
			'length'   => 'Le nom du champ doit comporter entre 3 et 100 caractères.'
		),
		
		'field_default' => array
		(
			'length'   => 'Le nom du champ doit comporter entre 3 et 200 caractères.'
		),
		
		'field_required' => array
		(
			'required' => 'Veuillez indiquer si ce champ est obligatoire (Oui ou Non)',
			'between'   => 'Valeur «champ obligatoire» invalide'
		),
		
		'field_width' => array
		(
			'between' => 'Veuillez indiquer la largeur du champ, entre 0 et 300'
		),
		
		'field_height' => array
		(
			'between' => 'Veuillez indiquer la hauteur du champ, entre 0 et 50'
		),
		
		'field_isdate' => array
		(
			'required' => 'Veuillez selectionner Oui ou Non pour le champ «date».',
			'between'   => 'Vous avez entré un champ «date» invalide.'
		)
	);

?>