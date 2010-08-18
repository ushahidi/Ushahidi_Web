<?php

$lang = array
(
	'parent_id' => array
	(
		'required'		=> 'The parent category field is required.',
		'numeric'		=> 'The parent category field must be numeric.',
		'exists'		=> 'The parent category does not exist.',
		'same'			=> 'The category and the parent category cannot be the same.',
	),
	
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