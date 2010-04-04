<?php

$lang = array
(
	'parent_id' => array
	(
		'required'		=> 'Pole parent category musi być wypełnione.',
		'numeric'		=> 'Pole parent category musi być numeryczne.',
		'exists'		=> 'Podana parent category nie istnieje.',
		'same'			=> 'Pola category i parent category nie mogą być jednakowe.',
	),
	
	'category_title' => array
	(
		'required'		=> 'Pole tytułu musi być wypełnione.',
		'length'		=> 'Pole tytułu musi mieć co najmniej 3 i nie więcej niż 80 znaków.',
	),
	
	'category_description' => array
	(
		'required'		=> 'Pole opisu musi być wypełnione.'
	),	
	
	'category_color' => array
	(
		'required'		=> 'Pole koloru musi być wypełnione.',
		'length'		=> 'Pole koloru musi zawierać 6 znaków.',
	),
	
	'category_image' => array
	(
		'valid'		=> 'Pole obrazu prawdopodobnie nie zawiera ważnego pliku',
		'type'		=> 'Pole obrazu prawdopodobnie nie zawiera ważnego obrazu. Jedynymi dopuszczalnymi formatami są .JPG, .PNG i .GIF.',
		'size'		=> 'Upewnij się, czy wielkość przesyłanego obrazu nie przekracza 50KB.'
	),	
);