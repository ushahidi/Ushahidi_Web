<?php
	$lang = array(
	'category_color' => array(
		'length' => 'Pole koloru musi zawierać 6 znaków.',
		'required' => 'Pole koloru musi być wypełnione.',
	),
	'category_description' => array(
		'required' => 'Pole opisu musi być wypełnione.',
	),
	'category_image' => array(
		'size' => 'Upewnij się, czy wielkość przesyłanego obrazu nie przekracza 50KB.',
		'type' => 'Pole obrazu prawdopodobnie nie zawiera ważnego obrazu. Jedynymi dopuszczalnymi formatami są .JPG, .PNG i .GIF.',
		'valid' => 'Pole obrazu prawdopodobnie nie zawiera ważnego pliku',
	),
	'category_title' => array(
		'length' => 'Pole tytułu musi mieć co najmniej 3 i nie więcej niż 80 znaków.',
		'required' => 'Pole tytułu musi być wypełnione.',
	),
	'parent_id' => array(
		'exists' => 'Podana parent category nie istnieje.',
		'numeric' => 'Pole parent category musi być numeryczne.',
		'required' => 'Pole parent category musi być wypełnione.',
		'same' => 'Pola category i parent category nie mogą być jednakowe.',
	));
?>
