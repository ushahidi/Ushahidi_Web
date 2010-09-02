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
	'parent_id' => array
	(
		'required'		=> 'Введите поле исходной категории.',
		'numeric'		=> 'Поле исходной категории должен быть нумеричный.',
		'exists'		=> 'Исходная категория не существует.',
		'same'			=> 'Категория и исходная категория не может быть похожим.',
	),

	'category_title' => array
	(
		'required'		=> 'Введите название.',
		'length'		=> 'Длина Названия не может быть менее 3х и более 80 символов.',
	),

	'category_description' => array
	(
		'required'		=> 'Введите описание категории.'
	),	

	'category_color' => array
	(
		'required'		=> 'Задайте цвет категории.',
		'length'		=> 'Номер цвета должен быть длинной 6 символов.',
	),

	'category_image' => array
	(
		'valid'		=> 'Загружаемый Файл картинки содержит ошибки или файл испорчен.',
		'type'		=> 'Файл испорчен или не в правильном формате, файл должен быть в .JPG, .PNG или .GIF. формате',
		'size'		=> 'Файл не может быть больше 50КВ.'
	),	
);
?>