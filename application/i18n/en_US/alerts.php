<?php

$lang = array
(
	'alert_mobile' => array
	(
		'required'		=> 'The Mobile Phone field is required if the checkbox is checked.',
		'numeric'		=> 'The Mobile Phone field does not appear to contain a valid phone. Please input numbers only including Country Code.',
		'one_required'	=> 'You must enter either your Mobile Phone Number or your Email Address.',
		'mobile_check'	=> 'That Mobile Phone Number has already been registered to receive alerts for that location',
		'length'		=> 'The Mobile Phone field does not seem to contain the right amount of digits.'
	),
	
	'alert_email' => array
	(
		'required'		=> 'The Email field is required if the checkbox is checked.',
		'email'		  => 'The Email field does not appear to contain a valid email address?',
		'length'	  => 'The Email field must be at least 4 and no more 64 characters long.',
		'email_check'	=> 'That Email address has already been registered to receive alerts for that location',
		'one_required' => ''
	),
	
	'alert_lat' => array
	(
		'required'		=> 'You have not selected a valid location on the map.',
		'between' => 'You have not selected a valid location on the map.'
	),
	
	'alert_lon' => array
	(
		'required'		=> 'You have not selected a valid location on the map.',
		'between' => 'You have not selected a valid location on the map.'
	),
	
	'alert_radius' => array
	(
		'required'		=> 'You have not set your radius on the map.',
		'in_array' => 'You have not set a valid radius on the map.'
	),

    'code_not_found' => 'This verification code was not found! Please confirm that you have the correct URL.',
    'code_already_verified' => 'This code has been verified before!',
    'code_verified' => ' Your code was verified correctly. You will now receive alerts about incidents as they happen.',
    'mobile_alert_request_created' => 'Your Mobile Alert request has been created and verification message has been sent to ',
	'verify_code' => 'You will not receive alerts on this location until you confirm your request.',
	'mobile_code' => 'Please enter the SMS confirmation code you received on your mobile phone below: ',
	'mobile_ok_head' =>'Your Mobile Alert Request Has Been Saved!',
	'mobile_error_head' => 'Your Mobile Alert Request Has NOT Been Saved!',
	'error' => 'The system was not able to process your confirmation request!',
	'settings_error' => 'This instance is not set up to correctly process alerts',
	'email_code' => 'Please enter the Email confirmation code you received below: ',
	'email_alert_request_created' => 'Your Email Alert request has been created and verification message has been sent to ',
	'email_ok_head' =>'Your Email Alert Request Has Been Saved!',
	'email_error_head' => 'Your Email Alert Request Has NOT Been Saved!',
    'create_more_alerts' => 'Return to the Alerts page to create more alerts',
	'unsubscribe' => 'You have received this email because you subscribed to receive alerts. If you do not wish to receive future alerts go to ',
	'verification_email_subject' => 'alerts - verification',
	'confirm_request' => 'To confirm your alert request, please go to ',
	'unsubscribed' => 'You will no longer receive alerts from ',
	'unsubscribe_failed' => 'We were not able to unsubscribe you. Please confirm that you have the correct URL.'
);
