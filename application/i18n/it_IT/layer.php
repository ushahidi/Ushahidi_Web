<?php
	$lang = array(
	'layer_color' => array(
		'length' => 'The color field must be 6 characters long.',
		'required' => 'The color field is required.',
	),
	'layer_file' => array(
		'type' => 'The file field does not appear to contain a valid file. The only accepted formats are .KMZ, .KML.',
		'valid' => 'The file field does not appear to contain a valid file',
	),
	'layer_name' => array(
		'length' => 'The name field must be at least 3 and no more 80 characters long.',
		'required' => 'The name field is required.',
	),
	'layer_url' => array(
		'atleast' => 'Either a KML Url or File is required',
		'both' => 'You can\'t have both a KML file and a url',
		'url' => 'Please enter a valid URL. Eg. http://www.ushahidi.com/layerl.kml',
	));
?>
