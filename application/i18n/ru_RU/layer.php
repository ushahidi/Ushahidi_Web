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
	'layer_name' => array
	(
		'required'	=> 'Введите название наслоения.',
		'length'	=> 'Длина Названия наслоения не может быть менее 3х и более 80 символов.',
	),

	'layer_url' => array
	(
		'url' => 'Введите действующий URL. Eg. http://www.ushahidi.com/layerl.kml',
		'atleast' => 'Введите или KML/KMZ Url или KML/KMZ Файл',
		'both' => 'Вы не можете ввести и URL и файл одновременно'
	),

	'layer_color' => array
	(
		'required'	=> 'Введите цвет наслоения.',
		'length'	=> 'Длина цвета должна быть из 6 символов.',
	),

	'layer_file' => array
	(
		'valid'		=> 'Файл повреждён или в неправильном формате',
		'type'		=> 'Файл повреждён или в неправильном формате. Правильные Форматы .KMZ, .KML.'
	),	
);
?>