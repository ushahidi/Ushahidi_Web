<?php

$lang = array
(
	'name' => array
	(
		'required'=> 'Pole "nazwa" jest wymagane.',
		'length'		=> 'Polę "nazwa" musi zawierać min. 3 znaki.',
	),
	
	'email' => array
	(
		'required'		=> 'Polę email jest wymagane (jeżeli jest zaznaczone).',
		'email'		  => 'To chyba nie jest prawidłowy adres email?',
		'length'	  => 'Email musi mieć min 4 znaki i być nie dłuższy niż 64 znaki.'
	),	
	
	'phone' => array
	(
		'length'		=> 'Podany został nieprawidłowy numer telefonu.',
	),
		
	'message' => array
	(
		'required'		=> 'Pole komentarz jest wymagane.'
	),
	
	'captcha' => array
	(
		'required' => 'Podaj proszę prawidłowy kod bezpieczeństwa.', 
		'default' => 'Podaj proszę prawidłowy kod bezpieczeństwa.'
	)
	
);
