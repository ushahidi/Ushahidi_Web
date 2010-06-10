<?php

$lang = array
(
	'name' => array
	(
		'required'		=> 'Veuillez indiquer le nom complet.',
		'length'		=> 'Le nom complet doit comporter entre 3 et 100 caractères.',
		'standard_text' => 'Le nom d\'utilisateur comporte des caractères invalides.',
		'login error'	=> 'Veuillez vérifier que le nom est correct.'
	),
	
	'email' => array
	(
		'required'	  => 'Veuillez indiquer une adresse email.',
		'email'		  => 'Adresse email invalide.',
		'length'	  => 'L\'adresse email doit comporter entre 4 et 64 caractères.',
		'exists'	  => 'Cette adress email existe déja.',
		'login error' => 'Veuillez vérifier que l\'adresse email est correcte.'
	),

	'username' => array
	(
		'required'		=> 'Veuillez indiquer un nom d\'utilisateur.',
		'length'		=> 'Le nom d\'utilisateur doit comporter entre 2 et 16 caractères.',
		'alpha' => 'Le nom d\'utilisateur doit être composé de lettres uniquement.',
		'admin' 		=> 'The role de l\'administrateur ne peut être modifié.',
		'exists'		=> 'Ce nom d\'utilisateur est déjà pris.',
		'login error'	=> 'Veuillez vérifier que le nom d\'utilisateur est correct.'
	),

	'password' => array
	(
		'required'		=> 'Veuillez entrer un mot de passe.',
		'length'		=> 'Le mot de passe doit comporter entre 5 et 16 caractères.',
		'alpha_numeric' => 'Le mot de passe doit être composé uniquement de lettres et de chiffres.',
		'login error'	=> 'Veuillez vérifier le mot de passe.'
	),

	'password_confirm' => array
	(
		'matches' => 'Les deux mots de passe doivent être les mêmes.'
	),

	'roles' => array
	(
		'required' => 'Veuillez définier au moins un role.',
		'values' => 'Le rôle doit être ADMINISTRATEUR ou UTILISATEUR.'
	)

);