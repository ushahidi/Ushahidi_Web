<?php

$lang = array
(
	'locale' => array
	(
		'required'		=> 'The locale is required.',
		'length'		=> 'The locale field has an incorrect value. ',
		'alpha_dash'	=> 'The locale field has an incorrect value. ',
		'locale'		=> 'The Original Report and the Translation have the same locale (language)',
		'exists'		=> 'This report already has a translation for this language'
	),
	
	'incident_title' => array
	(
		'required'		=> 'The title field is required.',
		'length'		=> 'The title field must be at least 3 and no more 200 characters long.'
	),
	
	'incident_description' => array
	(
		'required'		=> 'The description field is required.'
	),	
	
	'incident_date' => array
	(
		'required'		=> 'The date field is required.',
		'date_mmddyyyy' => 'The date field does not appear to contain a valid date?',
		'date_ddmmyyyy' => 'The date field does not appear to contain a valid date?'
	),
	
	'incident_hour' => array
	(
		'required'		=> 'The hour field is required.',
		'between' => 'The hour field does not appear to contain a valid hour?'
	),
	
	'incident_minute' => array
	(
		'required'		=> 'The hour field is required.',
		'between' => 'The hour field does not appear to contain a valid hour?'
	),
	
	'incident_ampm' => array
	(
		'validvalues' => 'The am/pm field does not appear to contain a valid value?'
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
	),
			
	'incident_category' => array
	(
		'required'		=> 'The category field is required.',
		'numeric'		=> 'The category field does not appear to contain a valid category?'
	),
	
	'incident_news' => array
	(
		'url'		=> 'The news source links field does not appear to contain a valid URL?'
	),
	
	'incident_video' => array
	(
		'url'		=> 'The video links field does not appear to contain a valid URL?'
	),
	
	'incident_photo' => array
	(
		'valid'		=> 'The Upload Photos field does not appear to contain a valid file',
		'type'		=> 'The Upload Photos field does not appear to contain a valid image. The only accepted formats are .JPG, .PNG and .GIF.',
		'size'		=> 'Please ensure that photo uploads sizes are limited to 2MB.'
	),
	
	'person_first' => array
	(
		'length'		=> 'The first name field must be at least 3 and no more 100 characters long.'
	),
	
	'person_last' => array
	(
		'length'		=> 'The last name field must be at least 3 and no more 100 characters long.'
	),
	
	'person_email' => array
	(
		'email'		  => 'The email field does not appear to contain a valid email address?',
		'length'	  => 'The email field must be at least 4 and no more 64 characters long.'
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
		'date_mmddyyyy'		  => 'The FROM date field does not appear to contain a valid date?',
		'range'	  => 'Please enter a valid FROM date. It cannot be greater than today.'
	),
	'to_date' => array
	(
		'date_mmddyyyy'		  => 'The TO date field does not appear to contain a valid date?',
		'range'	  => 'Please enter a valid TO date. It cannot be greater than today.',
		'range_greater'	=> 'Your FROM date cannot be greater than your TO date.'
	),
	'custom_field' => array
	(
		'values'		  => 'Please enter a valid value for one of your custom form items'
	),
	
	'incident_active' => array
	(
		'required'		=> 'Please enter a valid value for Approve This Report',
		'between'		=> 'Please enter a valid value for Approve This Report'
	),
	
	'incident_verified' => array
	(
		'required'		=> 'Please enter a valid value for Verify This Report',
		'between'		=> 'Please enter a valid value for Verify This Report'
	),
	
	'incident_source' => array
	(
		'alpha'		=> 'Please enter a valid value for Source Reliability',
		'length'		=> 'Please enter a valid value for Source Reliability'
	),
	
	'incident_information' => array
	(
		'alpha'		=> 'Please enter a valid value for Information Probability',
		'length'		=> 'Please enter a valid value for Information Probability'
	)
);