<?php

/**
 * SHARING INTERFACE INTERNATIONALIZATION
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
    'contact' => 'Контакт',	
    'date' => 'Дата регистрации',
    'date_added' => 'Дата добавления',
    'last_access' => 'Последнее посещение',
    'sent_info' => 'Данная информация будет отправлена с заявкой',
    'sharing_key' => 'Ключ',
    'sharing_url' => array
	(
		'required'	=> 'Введите url сайта',
		'url'		=> 'Адрес сайта введен неверно',
		'valid'		=> 'Данный сайт не подходит по требованиям Ushahidi, или не был включен Sharing',
		'exists'	=> 'URL сайта уже существует',
		'edit'		=> 'Вы можете редактировать удаленно. Sharing может быть прерван или Вы можете создать новый.'
	),

    'sharing_email' => array
	(
		'email'		=> 'Поле Email введен неверно.',
		'required'	=> 'Введите Email Вашего веб сайта. Зайдите в Параметры для добавления нового Email.',
	),	

    'sharing_color' => array
	(
		'required'	=> 'Введите цвет',
		'length'	=> 'Длина цвета должен быть 6 значным',
	),

    'sharing_limits' => array
	(
		'required'	=> 'Введите уровень доступа',
		'between'	=> 'Уровень доступа введён неверно',
	),

    'sharing_type' => array
	(
		'between'	=> 'Тип Sharing введён неверно',
	)	
);
?>