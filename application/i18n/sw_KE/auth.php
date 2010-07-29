<?php

$lang = array
(
	'name' => array
	(
		'required'		=> 'Jina kwa ukamili linahitajika.',
		'length'		=> 'Jina kwa ukamili lazima iwe baina ya herufi 3 na 100.',
		'standard_text' => 'Jina la mtumizi limeandikwa kwa herufi zisizokubalika.',
		'login error'	=> 'Tafadhali hakikisha umeandika jina sahihi.'
	),
	
	'email' => array
	(
		'required'	  => 'Anwani ya barua pepe inahitajika.',
		'email'		  => 'Anwani ya barua pepe inaonekana kuwa na herufi zisizotakikana',
		'length'	  => 'Anwani ya barua pepe lazima iwe baina ya herufi 4 na 64.',
		'exists'	  => 'Pole, Anwani ya barua pepe uliopatia ilishasajilishwa.',
		'login error' => 'Tafadhali hakikisha umeandika anwani ya barua pepe iliyo sahihi.'
	),

	'username' => array
	(
		'required'		=> 'Jina la mtumizi linahitajika.',
		'length'		=> 'Jina la mtumizi lazma liwe na baina ya herufi 2 na 16.',
		'standard_text' => 'Jina la mtumizi liko na herufi zisizotakikana.',
		'admin' 		=> 'Jukumu la meneja haliwezi kubadilishwa.',
		'superadmin'	=> 'Jukumu la meneja haliwezi kubadilishwa.',
		'exists'		=> 'Pole, Jina la mtumizi lilishasajilishwa.',
		'login error'	=> 'Tafadhali hakikisha umeandika jina la mtumizi kisahihi.'
	),

	'password' => array
	(
		'required'		=> 'Nywila inahitajika.',
		'length'		=> 'Nywila lazima iwe baina ya herufi 5 na 16.',
		'standard_text' => 'Nywila iko na herufi zisizotakikana.',
		'login error'	=> 'Tafadhali hakikisha umeandika nywila sahihi.',
		'matches'		=> 'Tafadhali andika nywila zilofanana kwenye sehemu ya nywila na kuthibitisha nywila.'
	),

	'password_confirm' => array
	(
		'matches' => 'Sehemu ya kuthibitisha nywila lazima iwe sawa na sehemu ya nywila.'
	),

	'roles' => array
	(
		'required' => 'Lazima ufasili jukumu lako.',
		'values' => 'Lazima uchague jukumu la MENEJA au MTUMIZI.'
	),
	
	'resetemail' => array
        (
    	        'required' => 'Anwani ya barua pepe inahitajika.',
       	        'invalid' => 'Pole, hatuna anwani yako ya barua pepe',
                'email'  => 'Anwani ya barua pepe inaonekana si sahihi',
        ),

);
