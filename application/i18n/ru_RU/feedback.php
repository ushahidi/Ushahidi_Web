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
	'feedback_details' => 'Детали отзывов',
	'feedback_date' => 'Дата',
	'feedback_actions' => 'Действия',

	'feedback_title' => array
	(
		'required'		=> 'Введите название',
		'length'		=> 'Длина названия не может быть менее 3х и более 100 символов'
	),

	'feedback_message' => array
	(
		'required' => 'Введите сообщение отзыва',
	),

	'person_name' => array
	(
		'required' => 'Введите полное имя',

	),

	'person_email' => array
	(
		'required'		=> 'Введите адрес Email, или уберите галочку.',
		'email'		  => 'Ваш Email адрес введен не правильно, введите правильно.',
	),


	'feedback_captcha' => array
	(
		'required' => 'Введите защитный код', 
		'default' => 'Введите правильный защитный код'
	)

);
?>