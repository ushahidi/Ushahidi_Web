<?php

$lang = array
(
	'parent_id' => array
	(
		'required'		=> 'Jamii mzazi inatakikana.',
		'numeric'		=> 'Jamii mzazi lazma iwe nambari.',
		'exists'		=> 'Jamii mzazi uliotupatia haipo.',
		'same'			=> 'Jamii pamoja na jamii mzazi haziezi kuwa sawa.',
	),
	
	'category_title' => array
	(
		'required'		=> 'Jina la jamii linahitajika.',
		'length'		=> 'Jina la jamii lazima liwe baina ya herifi 3 na 80.',
	),
	
	'category_description' => array
	(
		'required'		=> 'Sehemu ya elezo inahitajika.'
	),	
	
	'category_color' => array
	(
		'required'		=> 'Sehemu ya rangi inahitajika.',
		'length'		=> 'Sehemu ya rangi lazma iwe herufi 6.',
	),
	
	'category_image' => array
	(
		'valid'		=> 'Sehemu ya picha inaonekana haina faili ilio sawa',
		'type'		=> 'Sehemu ya picha inaonekana haina faili ilio sawa. Picha zinazokubalika ni za .JPG, .PNG na .GIF.',
		'size'		=> 'Tafadhali hakikisha faili ya picha haipiti 50KB.'
	),	
);