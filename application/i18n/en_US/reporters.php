<?php

$lang = array
(
	'reporter_id' => array
	(
		'required'		  => 'Invalid Reporter',
		'numeric'		  => 'Invalid Reporter'
	),
	
	'level_id' => array
	(
		'required'		  => 'The Reporter Level field does not appear to contain a valid Level?',
		'numeric'		  => 'The Reporter Level field does not appear to contain a valid Level?'
	),	
	
	'latitude' => array
	(
		'required'		=> 'The latitude field is required. Please click on the map to pinpoint a location.',
		'between' => 'The latitude field does not appear to contain a valid latitude?'
	),
	
	'longitude' => array
	(
		'required'		=> 'The longitude field is required. Please click on the map to pinpoint a location.',
		'between' => 'The longitude field does not appear to contain a valid longitude?'
	),
	
	'location_name' => array
	(
		'required'		=> 'The location name field is required.',
		'length'		=> 'The location name field must be at least 3 and no more 200 characters long.',
	)
);