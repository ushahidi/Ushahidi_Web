<?php

$lang = array
(
	'locale' => array
	(
		'required'		=> 'Sehemu ya mandhari inahitajika.',
		'length'		=> 'Sehemu ya mandhari haiko sahihi. ',
		'alpha_dash'	=> 'Sehemu ya mandhari haiko sahihi. ',
		'locale'		=> 'Repoti awali na tafsri yake iko na mandhari sawa (lugha)',
		'exists'		=> 'Repoti hii iko na tafiri ya lugha hii tayari.'
	),
	
	'incident_title' => array
	(
		'required'		=> 'Sehemu ya jina inahitajika.',
		'length'		=> 'Sehemu ya jina lazima iwe na baina ya herufi 3 na 200.'
	),
	
	'incident_description' => array
	(
		'required'		=> 'Sehemu ya maelezo inahitajika.'
	),	
	
	'incident_date' => array
	(
		'required'		=> 'Sehemu ya tarehe inahitajika.',
		'date_mmddyyyy' => 'Sehemu ya tarehe inaonekana si sahihi',
		'date_ddmmyyyy' => 'Sehemu ya tarehe inaonekana si sahihi'
	),
	
	'incident_hour' => array
	(
		'required'		=> 'Sehemu ya saa inahitajika.',
		'between' => 'Sehemu ya saa inaonekana si sahihi.'
	),
	
	'incident_minute' => array
	(
		'required'		=> 'Sehemu ya dakika inahitajika.',
		'between' => 'Sehemu ya dakika inaonekana si sahihi'
	),
	
	'incident_ampm' => array
	(
		'validvalues' => 'Sehemu ya am/pm inaonekana si sahihi'
	),
	
	'latitude' => array
	(
		'required'		=> 'Sehemu ya latitudo inahitajika. Tafadhali binya kwenye ramani kuashiria eneo.',
		'between' => 'Sehemu ya latitudo inaonekana si sahihi'
	),
	
	'longitude' => array
	(
		'required'		=> 'Sehemu ya longitudo inahitajika. Tafadhali binya kwenye ramani kuashiria eneo.',
		'between' => 'Sehemu ya longitudo inaonekana si sahihi'
	),
	
	'location_name' => array
	(
		'required'		=> 'Jina la eneo linahitajika.',
		'length'		=> 'Jina la eneo lazima liwe baina ya herufi 3 na 200.',
	),
			
	'incident_category' => array
	(
		'required'		=> 'Sehemu ya jamii inahitajika.',
		'numeric'		=> 'Sehemu ya jamii inaonekana si sahihi'
	),
	
	'incident_news' => array
	(
		'url'		=> 'Sehemu ya anwani ya habari inaonekana si sahihi'
	),
	
	'incident_video' => array
	(
		'url'		=> 'Sehemu ya anwani ya video inaonekana si sahihi'
	),
	
	'incident_photo' => array
	(
		'valid'		=> 'Sehemu ya kupakia picha inaonekana haina faili sahihi',
		'type'		=> 'Sehemu ya kupakia picha inaonekana haina faili sahihi. Faili zinazokubalika ni za .JPG, .PNG na .GIF.',
		'size'		=> 'Tafadhali hakikisha picha zinazopakiwa hazizidi 2MB.'
	),
	
	'person_first' => array
	(
		'length'		=> 'Jina la kwanza lazma liwe baina ya herufi 3 na 100.'
	),
	
	'person_last' => array
	(
		'length'		=> 'Jina la mwisho lazma liwe baina ya herufi 3 na 100.'
	),
	
	'person_email' => array
	(
		'email'		  => 'Sehemu ya anwani ya barua pepe inaonekana si sahihi',
		'length'	  => 'Sehemu ya anwani ya barua pepe lazima iwe baina ya herufi 4 na 64.'
	),
	
	// Admin - Report Download Validation
	'data_point' => array
	(
		'required'		  => 'Tafadhali chagua aina ya sahihi ya repoti ya kupakua',
		'numeric'		  => 'Tafadhali chagua aina ya sahihi ya repoti ya kupakua',
		'between'		  => 'Tafadhali chagua aina ya sahihi ya repoti ya kupakua'
	),
	'data_include' => array
	(
		'numeric'		  => 'Tafadhali chagua kipengele cha sahihi cha kupakua',
		'between'		  => 'Tafadhali chagua kipengele cha sahihi cha kupakua'
	),
	'from_date' => array
	(
		'date_mmddyyyy'		  => 'Tarehe KUTOKA inaonekana si sahihi',
		'range'	  => 'Tafadhali tumia tarehe ya sahihi katika sehemu ya tarehe KUTOKA.'
	),
	'to_date' => array
	(
		'date_mmddyyyy'		  => 'Tarehe MPAKA inaonekana si sahihi',
		'range'	  => 'Tafadhali tumia tarehe ya sahihi katika sehemu ya tarehe MPAKA.',
		'range_greater'	=> 'Tarehe KUTOKA haiezi kuwa baada ya tarehe MPAKA.'
	),
	'custom_field' => array
	(
		'values'		  => 'Tafadhali jaza uga ya kidesturi kisahihi'
	),
	
	'incident_active' => array
	(
		'required'		=> 'Tafadhali jaza kisahihi kwa sehemu ya Pitisha Ripoti',
		'between'		=> 'Tafadhali jaza kisahihi kwa sehemu ya Guna Ripoti'
	),
	
	'incident_verified' => array
	(
		'required'		=> 'Tafadhali jaza kisahihi kwa sehemu ya Kuthibitisha Ripoti',
		'between'		=> 'Tafadhali jaza kisahihi kwa sehemu ya Kuthibitisha Ripoti'
	),
	
	'incident_source' => array
	(
		'alpha'		=> 'Tafadhali jaza kisahihi kwa sehemu ya Uaminifu wa Asili',
		'length'		=> 'Tafadhali jaza kisahihi kwa sehemu ya Uaminifu wa Asili'
	),
	
	'incident_information' => array
	(
		'alpha'		=> 'Tafadhali jaza kisahihi kwa sehemu ya Uwezekano wa Maarifa',
		'length'		=> 'Tafadhali jaza kisahihi kwa sehemu ya Uwezekano wa Maarifa'
	)
);