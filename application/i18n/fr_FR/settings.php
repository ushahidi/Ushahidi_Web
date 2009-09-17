<?php

$lang = array
(
	'site_name' => array
	(
		'required'		=> 'The site name field is required.',
		'length'		=> 'The site name field must be at least 3 and no more 50 characters long.',
	),
	
	'site_tagline' => array
	(
		'required'		=> 'The tagline field is required.',
		'length'		=> 'The tagline field must be at least 3 and no more 100 characters long.',
	),
	
	'site_email' => array
	(
		'email'		  => 'The site email field does not appear to contain a valid email address?',
		'length'	  => 'The site email field must be at least 4 and no more 100 characters long.'
	),
	
	'items_per_page' => array
	(
		'required'		=> 'The items per page (Frontend) field is required.',
		'between' => 'The items per page (Frontend) field does not appear to contain a valid value?'
	),
	
	'items_per_page_admin' => array
	(
		'required'		=> 'The items per page (Admin) field is required.',
		'between' => 'The items per page (Admin) field does not appear to contain a valid value?'
	),
	
	'allow_reports' => array
	(
		'required'		=> 'The allow reports field is required.',
		'between' => 'The allow reports field does not appear to contain a valid value?'
	),
	
	'allow_comments' => array
	(
		'required'		=> 'The allow comments field is required.',
		'between' => 'The allow comments field does not appear to contain a valid value?'
	),
	
	'allow_stat_sharing' => array
	(
		'required'		=> 'The stat sharing field is required.',
		'between' => 'The stat sharing field does not appear to contain a valid value?'
	),
	
	'allow_feed' => array
	(
		'required'		=> 'The include feed field is required.',
		'between' => 'The include feed field does not appear to contain a valid value?'
	),
	
	'sms_no1' => array
	(
		'numeric'		=> 'The phone 1 field should contain numbers only.',
		'length' => 'The phone 1 field does not appear to contain a valid value?'
	),
	
	'sms_no2' => array
	(
		'numeric'		=> 'The phone 2 field should contain numbers only.',
		'length' => 'The phone 2 field is too long'
	),
	
	'sms_no3' => array
	(
		'numeric'		=> 'The phone 3 field should contain numbers only.',
		'length' => 'The phone 3 field is too long'
	),
	
	'clickatell_api' => array
	(
		'required'		=> 'The Clickatell API number field is required.',
		'length'		=> 'The Clickatell API number field must be no more 20 characters long.',
	),
	
	'clickatell_username' => array
	(
		'required'		=> 'The Clickatell Username field is required.',
		'length'		=> 'The Clickatell Username field must be no more 50 characters long.',
	),
	
	'clickatell_password' => array
	(
		'required'		=> 'The Clickatell Password field is required.',
		'length'		=> 'The Clickatell Password field must be at least 5 and no more 50 characters long.',
	),

	'google_analytics' => array
	(
		'length'		=> 'The Google Analytics field must contain a valid Web Property ID in the format UA-XXXXX-XX.',
	)		
	
);