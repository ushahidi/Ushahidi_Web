<?php

$lang = array
(
	'layer_name' => array
	(
		'required'		=> 'Nazwa tego pola jest wymagana.',
		'length'		=> 'Nazwa tego pola musi posiadać min. 3 znaki i być nie dłuższa niż 80 znaków.',
	),
	
	'layer_url' => array
	(
		'url' => 'Proszę podaj prawidłowy URL, np. http://www.ushahidi.com/layerl.kml',
		'atleast' => 'Wymagane jest podanie albo pliku albo KML URL',
		'both' => 'Nie możesz podać obu (pliku albo KML URL) - albo jedno albo drugie.'
	),
	
	'layer_color' => array
	(
		'required'		=> 'Pole koloru jest wymagane.',
		'length'		=> 'Pole koloru musi mieć min. 6 znaków',
	),
	
	'layer_file' => array
	(
		'valid'		=> 'Pole nie zawiera prawidłowego pliku.',
		'type'		=> 'Pole nie zawiera prawidłowego pliku. Jedyne akceptowalne formaty to .KMZ, .KML.'
	),	
);
