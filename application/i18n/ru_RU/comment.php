<?php

$lang = array
(
	'comment_author' => array
	(
		'required'		=> 'Введите название коммента.',
		'length'        => 'Длинна Названия не может быть менее 3х символов.'
	),

	'comment_description' => array
	(
		'required'        => 'Введите свой коммент.'
	),

	'comment_email' => array
	(
		'required'		=> 'Нужно ввести адрес Email, или уберите галочку.',
		'email'		  => 'Ваш Email адрес введен не правильно, введите правильно.',
		'length'	  => 'Длинна Email не может быть менее 4 и более 64 символов.'
	),

	'captcha' => array
	(
		'required' => 'Введите защитный код', 
		'default' => 'Введите правильный защитный код'
	)

);