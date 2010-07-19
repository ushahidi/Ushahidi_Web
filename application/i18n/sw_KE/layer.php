<?php

$lang = array
(
	'layer_name' => array
	(
		'required'		=> 'Sehemu ya jina inahitajika.',
		'length'		=> 'Sehemu ya jina lazima iwe baina ya herufi 3 na 80.',
	),
	
	'layer_url' => array
	(
		'url' => 'Tafadhali andika anwani iliyo sahihu. kwa mfano: http://www.ushahidi.com/layerl.kml',
		'atleast' => 'Anwani ya KML au faili inahitajika',
		'both' => 'Huwezi kuwa na KML na faili pamoja'
	),
	
	'layer_color' => array
	(
		'required'		=> 'Sehemu ya rangi inahitajika.',
		'length'		=> 'Sehemu ya rangi lazma iwe na herufi 6.',
	),
	
	'layer_file' => array
	(
		'valid'		=> 'Sehemu ya faili inaonekana kutokuwa na faili iliyo hitajika',
		'type'		=> 'Sehemu ya faili inaonekana kutokuwa na faili iliyo hitajika. Faili zinazokubalika ni za .KMZ, .KML.'
	),	
);