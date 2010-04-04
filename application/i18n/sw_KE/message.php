<?php

$lang = array
(
	'name' => array
	(
		'required'=> 'Sehemu ya jina inahitajika.',
		'length'		=> 'Sehemu ya jina lazima iwe herufi 3 au zaidi.',
	),
	
	'email' => array
	(
		'required'		=> 'Anwani ya barua pepe inahitajika kama kisanduku tiki chake kimewekwa tiki.',
		'email'		  => 'Anwani ya barua pepe inaonekana si sahihi',
		'length'	  => 'Anwani ya barua pepe lazima iwe baina ya herufi 4 na 64.'
	),	
	
	'phone' => array
	(
		'length'		=> 'Sehemu ya nambari ya simu si sahihi.',
	),
		
	'message' => array
	(
		'required'		=> 'Sehemu ya fikira inahitajika.'
	),
	
	'captcha' => array
	(
		'required' => 'Tafadhali andika ishara ya ulinzi', 
		'default' => 'Tafadhali andika ishara ya ulinzi iliyo sahihi'
	)
	
);