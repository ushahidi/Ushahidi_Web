<?php

/**
 * FRONT END USER INTERFACE INTERNATIONALIZATION
 * Strings associated with the front end UI
 *
 * translated by Ismailov A. altyni@gmail.com
 * 
 * Misprints, mistypes, language errors and mistakes, new vars are
 * fixed by Sergei 'the_toon' Plaxienko sergei.plaxienko@gmail.com
 *
 * ru_RU
 *
 */

$lang = array
(
	'name' => array
	(
		'required'	=> 'Введите название сообщения.',
		'length'	=> 'Длина названия сообщения не может быть менее 3х символов.',
	),

	'email' => array
	(
		'required'	=> 'Нужно ввести адрес Email, или уберите галочку.',
		'email'		=> 'Ваш Email адрес введен не правильно, введите правильно.',
		'length'	=> 'Длина Email не может быть меньше 4х и более 64 символов.'
	),	

	'phone' => array
	(
		'length'	=> 'Номер телефона введен неверно.',
	),

	'message' => array
	(
		'required'	=> 'Введите сообщение'
	),

	'captcha' => array
	(
		'required'	=> 'Введите защитный код', 
		'default'	=> 'Введите правильный защитный код'
	)
);
?>