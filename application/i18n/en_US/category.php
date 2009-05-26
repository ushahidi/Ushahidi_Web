<?php

$lang = array
(
	'category_title' => array
	(
		'required'		=> 'The title field is required.',
		'length'		=> 'The title field must be at least 3 and no more 80 characters long.',
	),
	
	'category_description' => array
	(
		'required'		=> 'The description field is required.'
	),	
	
	'category_color' => array
	(
		'required'		=> 'The color field is required.',
		'length'		=> 'The color field must be 6 characters long.',
	),
	
	'category_image' => array
	(
		'valid'		=> 'The image field does not appear to contain a valid file',
		'type'		=> 'The image field does not appear to contain a valid image. The only accepted formats are .JPG, .PNG and .GIF.',
		'size'		=> 'Please ensure that image uploads sizes are limited to 50KB.'
	),	
);