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
	'contact_name' => array
	(
		'required' => 'Введите имя.',
		'length' => 'Длина Поля Имени не должна содержать менее 3х символов.'
	),

	'contact_subject' => array
	(
		'required' => ' Введите тему.',
		'length' => 'Длина темы не может быть менее 3х символов.'
	),

	'contact_message' => array
	(
		'required' => 'Введите сообщение.'
	),

	'contact_email' => array
	(
		'required' => 'Нужно ввести адрес Email, или уберите галочку.',
		'email'	=> 'Ваш Email адрес введен неверно, введите правильно.',
		'length' => 'Длина Email не может быть менее 4х и более 64х символов.'
	),

	'captcha' => array
	(
		'required' => 'Введите защитный код', 
		'default' => 'Введите правильный защитный код'
	)
);
?>