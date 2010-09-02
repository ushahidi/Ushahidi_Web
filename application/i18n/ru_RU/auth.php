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
		'required'		=> 'Введите Имя.',
		'length'		=> 'Длина Поля Имени не может содержать менее 3х и более 100 символов.',
		'standard_text' => 'Поле имя пользователя содержит недопустимые символы.',
		'login error'	=> 'Введите правильное имя пользователя.'
	),

	'email' => array
	(
		'required'	  => 'Введите Email.',
		'email'		  => 'Ваш Email адрес введен неверно, введите правильно.',
		'length'	  => 'Длина Email адреса не может быть менее 4х и более 64 символов.',
		'exists'	  => 'Данный Email уже используется другим пользователем.',
		'login error' => 'Введите правильный Email адрес.'
	),

	'username' => array
	(
		'required'		=> 'Введите имя пользователя.',
		'length'		=> 'Длина Имени пользователя не может быть менее 2х и более 16 символов.',
		'alpha' => 'Имя пользователя должна содержать только буквы.',
		'admin' 		=> ' Роль пользователя Admin не может быть изменен.',
		'superadmin'	=> 'Роль пользователя Super Admin не может быть изменен.',
		'exists'		=> 'Имя пользователя уже используется.',
		'login error'	=> 'Введите правильное имя пользователя.'
	),

	'password' => array
	(
		'required'		=> 'Введите  пароль.',
		'length'		=> 'Длина Пароли должна быть не менее 5 и не более 16 символов.',
		'alpha_numeric'		=> 'Пароль должен содержать только цифры и буквы.',
		'login error'	=> 'Пароль введен неверно.',
		'matches'		=> 'Введите идентичный пароль в двух полях для пароля.'
	),

	'password_confirm' => array
	(
		'matches' => 'Подтверждение пароля должна быть идентична с паролем.'
	),

	'roles' => array
	(
		'required' => 'Вы должны задать не менее одной роли.',
		'values' => 'Выберите или Admin или User.'
	),

	'resetemail' => array
        (
    	        'required' => 'Введите Email .',
       	        'invalid' => 'Ваш Email не зарегистрирован в нашей системе.',
                'email'  => 'Введите правильный Email адрес.',
        ),

);
?>