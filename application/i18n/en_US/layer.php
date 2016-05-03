<?php
$lang = array(
	'layer_color' => array(
		'length' => 'The color field must be 6 characters long.',
		'required' => 'The color field is required.',
	) ,
	'layer_file' => array(
		'type' => 'The file field does not appear to contain a valid file. The only accepted formats are .KMZ and .KML.',
		'valid' => 'The file field does not appear to contain a valid file',
	) ,
	'layer_name' => array(
		'length' => 'The name field must be at least 3 and no more 80 characters long.',
		'required' => 'The name field is required.',
	) ,
	'layer_url' => array(
		'atleast' => 'Either a KML URL or File is required.',
		'both' => 'You can\'t have both a KML file and a URL.',
		'url' => 'Please enter a valid URL, e.g. http://www.ushahidi.com/layerl.kml',
	)
);
?>
