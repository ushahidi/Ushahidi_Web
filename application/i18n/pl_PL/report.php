<?php

$lang = array
(
	'locale' => array
	(
		'required'		=> 'Pole locale jest wymagane.',
		'length'		=> 'Pole locale posiada nieprawidłową długość. ',
		'alpha_dash'	=> 'Pole locale zawiera nieprawidłowe dane. ',
		'locale'		=> 'Orginaly alert oraz tłumaczenie zawierają tą samą wartość locale.',
		'exists'		=> 'Ten raport już posiada tłumaczenie w tym języku.'
	),
	
	'incident_title' => array
	(
		'required'		=> 'Tutuł alertu jest wymagany.',
		'length'		=> 'Tutuł alertu musi mieć min. 3 znaki (max. 200).'
	),
	
	'incident_description' => array
	(
		'required'		=> 'Opis alertu jest wymagany.'
	),	
	
	'incident_date' => array
	(
		'required'		=> 'Data alertu jest wymagana.',
		'date_mmddyyyy' => 'Format daty alertu jest nieprawidłowy.',
		'date_ddmmyyyy' => 'Format daty alertu jest nieprawidłowy.'
	),
	
	'incident_hour' => array
	(
		'required'		=> 'Godzina alertu jest wymagana.',
		'between' => 'Format albo zakres godzinowy jest nieprawidłowy.'
	),
	
	'incident_minute' => array
	(
		'required'		=> 'Pole z minutą jest wymagane.',
		'between' => 'Format albo zakres pola minutowego jest nieprawidłowy.'
	),
	
	'incident_ampm' => array
	(
		'validvalues' => 'Pole AM/PM zawiera nieprawidłową wartość'
	),
	
	'latitude' => array
	(
		'required'		=> 'Wartość szerokośći geograficznej musi zostać podana. Proszę zaznacz ja na mapie.',
		'between' => 'Pole szerokości geograficznej zawiera nieprawidłową wartość.'
	),
	
	'longitude' => array
	(
		'required'		=> 'Wartość długości geograficznej musi zostać podana. Proszę zaznacz ja na mapie.',
		'between' => 'Pole długości geograficznej zawiera nieprawidłową wartość.'
	),
	
	'location_name' => array
	(
		'required'		=> 'Nazwa lokalizacji jest wymagana.',
		'length'		=> 'Nazwa lokalizacji musi zawierać min. 3 znaki (max. 200)',
	),
			
	'incident_category' => array
	(
		'required'		=> 'Kategoria jest wymagana.',
		'numeric'		=> 'Pole kategoria zawiera nieprawidłowe dane.'
	),
	
	'incident_news' => array
	(
		'url'		=> 'Źródło alertu zawiera nieprawidłowy adres URL.'
	),
	
	'incident_video' => array
	(
		'url'		=> 'Video alertu zwiera nieprawidłowy adres URL.'
	),
	
	'incident_photo' => array
	(
		'valid'		=> 'Wgrywane zdjęcie nie wydaje się być w prawidłowym formacie.',
		'type'		=> 'Wgrywane zdjęcie nie wydaje się być w prawidłowym formacie. Akceptowalne formaty to: .JPG, .PNG and .GIF.',
		'size'		=> 'Limit wielkości zdjęcia to 2MB.'
	),
	
	'person_first' => array
	(
		'length'		=> 'Imię musi zawierać min 3 znaki (max 100).'
	),
	
	'person_last' => array
	(
		'length'		=> 'Nazwisko musi zawierać min 3 znaki (max 100).'
	),
	
	'person_email' => array
	(
		'email'		  => 'Proszę podaj prawidłowy adres email.',
		'length'	  => 'Adres email musi posiadać min 4 znaki (max 64).'
	),
	
	// Admin - Report Download Validation
	'data_point' => array
	(
		'required'		  => 'Please select a valid type of report to download',
		'numeric'		  => 'Please select a valid type of report to download',
		'between'		  => 'Please select a valid type of report to download'
	),
	'data_include' => array
	(
		'numeric'		  => 'Please select a valid item to include in the download',
		'between'		  => 'Please select a valid item to include in the download'
	),
	'from_date' => array
	(
		'date_mmddyyyy'		  => 'Pole DATA OD zawiera nieprawidłowe dane.',
		'range'	  => 'Pole DATA OD zawiera nieprawidłowe dane (nie może być większe od dzisiejszej daty).'
	),
	'to_date' => array
	(
		'date_mmddyyyy'		  => 'Pole DATA DO zawiera nieprawidłowe dane.',
		'range'	  => 'Pole DATA DO zawiera nieprawidłowe dane (nie może być większe od dzisiejszej daty).',
		'range_greater'	=> 'Pole DATA DO zawiera nieprawidłowe dane (nie może być większe od dzisiejszej daty).'
	),
	'custom_field' => array
	(
		'values'		  => 'Proszę wypełnij wszystkie wymagane pola.'
	),
	
	'incident_active' => array
	(
		'required'		=> 'Proszę podaj właście dane przy akceptacji alertu.',
		'between'		=> 'Proszę podaj właście dane przy akceptacji alertu.'
	),
	
	'incident_verified' => array
	(
		'required'		=> 'Proszę podaj właście dane przy weryfikacji alertu.',
		'between'		=> 'Proszę podaj właście dane przy weryfikacji alertu'
	),
	
	'incident_source' => array
	(
		'alpha'		=> 'Proszę podaj właście dane źródła alertu.',
		'length'		=> 'Proszę podaj właście dane źródła alertu.'
	),
	
	'incident_information' => array
	(
		'alpha'		=> 'Proszę podaj właście dane przy prawdopodobieństwu alertu.',
		'length'		=> 'Proszę podaj właście dane przy prawdopodobieństwu alertu.'
	)
);
