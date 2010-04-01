<?php

$lang = array
(
	'site_name' => array
	(
		'required'		=> 'Veuillez entrer le nom du site.',
		'length'		=> 'le nom du site doit comporter entre 3 et 50 caractères.',
	),
	
	'site_tagline' => array
	(
		'required'		=> 'Veuiller entrer un slogan.',
		'length'		=> 'Le slogan doit comporter entre 3 et 100 caractères.'
	),
	
	'site_email' => array
	(
		'email'		  => 'L\'adresse email du site n\'est pas valide.',
		'length'	  => 'L\'adresse email du site doit comporter entre 4 et 100 caractères.'
	),
	
	'items_per_page' => array
	(
		'required'		=> 'Veuillez indiquer le nombre d\'éléments par page (Frontend).',
		'between' => 'Le nombre d\'éléments par page (Frontend) n\'est pas valide'
	),
	
	'items_per_page_admin' => array
	(
		'required'		=> 'Veuillez indiquer le nombre d\'éléments par page (Admin).',
		'between' => 'Le nombre d\'éléments par page (Admin) n\'est pas valide'
	),
	
	'allow_reports' => array
	(
		'required'		=> 'Veuillez indiquer si les rapports sont autorisés.',
		'between' => 'Veuillez indiquer correctement si les rapports sont autorisés.'
	),
	
	'allow_comments' => array
	(
		'required'		=> 'Veuillez indiquer si les commentaires sont autorisés.',
		'between' => 'Veuillez indiquer correctement si les commentaires sont autorisés.'
	),
	
	'allow_stat_sharing' => array
	(
		'required'		=> 'Veuillez indiquer une valeur pour le partage des statistiques.',
		'between' => 'Le champ «partage des statistiques» a une valeur incorrecte'
	),
	
	'allow_feed' => array
	(
		'required'		=> 'Veuillez indiquer si le fil est inclus.',
		'between' => 'Veuillez indiquer correctement si le fil est inclus.'
	),
	
	'sms_no1' => array
	(
		'numeric'		=> 'Le premier numéro de téléphone ne doit comporter que des chiffres.',
		'length' => 'Le premier numéro de téléphone a un format invalide'
	),
	
	'sms_no2' => array
	(
		'numeric'		=> 'Le second numéro de téléphone ne doit comporter que des chiffres.',
		'length' => 'Le second numéro de téléphone a un format invalide'
	),
	
	'sms_no3' => array
	(
		'numeric'		=> 'Le troisième numéro de téléphone ne doit comporter que des chiffres.',
		'length' => 'Le troisième numéro de téléphone a un format invalide'
	),
	
	'clickatell_api' => array
	(
		'required'		=> 'Veuillez indiquer un numéro d\'API Clickatell.',
		'length'		=> 'Le numéro d\'API Clickatell doit comporter moins de 20 caractères.'
	),
	
	'clickatell_username' => array
	(
		'required'		=> 'Veuillez indiquer un nom d\'utilisateur Clickatell.',
		'length'		=> 'Le nom d\'utilisateur Clickatell doit comporter moins de 20 caractères.'
	),
	
	'clickatell_password' => array
	(
		'required'		=> 'Veuillez indiquer un mot de passe Clickatell.',
		'length'		=> 'Le mot de passe Clickatell doit comporter entre 5 et 50 caractères.'
	),

	'google_analytics' => array
	(
		'length'		=> 'Le champ «Google Analytics» doit contenir un identifiant «Web Property» au format UA-XXXXX-XX.'
	)
);