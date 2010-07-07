<?php

$lang = array
(
	'layer_name' => array
	(
		'required'		=> 'The name field is required.',
		'length'		=> 'The name field must be at least 3 and no more 80 characters long.',
	),
	
	'layer_url' => array
	(
		'url' => 'Please enter a valid URL. Eg. http://www.ushahidi.com/layerl.kml',
		'atleast' => 'Either a KML Url or File is required',
		'both' => 'You can\'t have both a KML file and a url'
	),
	
	'layer_color' => array
	(
		'required'		=> 'The color field is required.',
		'length'		=> 'The color field must be 6 characters long.',
	),
	
	'layer_file' => array
	(
		'valid'		=> 'The file field does not appear to contain a valid file',
		'type'		=> 'The file field does not appear to contain a valid file. The only accepted formats are .KMZ, .KML.'
	),	
);