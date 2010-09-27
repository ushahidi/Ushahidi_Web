<?php

/**
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
	'organization_name' => array
	(
		'required'	=> 'Введите название Организации.',
		'length'	=> 'Длина названия организации должна быть не менее 3х и не более 70 символов.',
		'standard_text' => 'Имя пользователя введено неправильно',
	),

	'organization_website' => array
	(
		'required'	=> 'Введите Веб сайт вашей Организации.',
		'url'		=> 'Введите URL правильно. Например, http://www.ushahidi.com'
	),

	'organization_description' => array
	(
		'required'	=> 'Опишите Вашу организацию.'
	),

	'organization_email' => array
	(
		'email'		=> 'Email организации введен неправильно.',
		'length'	=> 'Длина Email организации не должен быть менее 4х и более 100 символов.'
	),

	'organization_phone1' => array
	(
		'length'	=> 'Длина телефонного номера не должна превышать 50 символов.'
	),

	'organization_phone2' => array
	(
		'length'	=> 'Длина телефонного номера не должна превышать 50 символов'
	)
);
?>