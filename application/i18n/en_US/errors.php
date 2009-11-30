<?php
/**
 * ERRORS INTERNATIONALIZATION
 * Error strings not covered by the Forms i18n file
 */
$lang = array
(	
	/**** ALERTS ERRORS ****/
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
	'email_alert_request_created' => 'Your Email Alert request has been created and verification message has been sent to ',
	'email_ok_head' =>'Your Email Alert Request Has Been Saved!',
	'email_error_head' => 'Your Email Alert Request Has NOT Been Saved!',
	'create_more_alerts' => 'Return to the Alerts page to create more alerts',
	'unsubscribe' => 'You have received this email because you subscribed to receive alerts. If you do not wish to receive future alerts go to ',
	'verification_email_subject' => 'alerts - verification',
	'confirm_request' => 'To confirm your alert request, please go to ',
	'unsubscribed' => 'You will no longer receive alerts from ',
	'unsubscribe_failed' => 'We were not able to unsubscribe you. Please confirm that you have the correct URL.',
	
	
	/**** AKISMET SETUP ERRORS ****/
	'api_key'   		=> 'Theres a problem with the api key',
	'server_failed'    	=> 'Looks like the servers not responding',
	'server_not_found'	=> 'Wheres the server gone?',
	
	
	/**** DATABASE SETUP ERRORS ****/
	'table_not_found' => 'Table "%s" cannot be found in the database. Please make sure you are using the latest version of the database for this version of Ushahidi',
	'error'           => 'Database error: %s',
	
	
	/**** MISC SETUP ERRORS ****/
	'cURL_not_installed' => 'php5-curl is not installed on this system',
	
	
	/**** IMAP/POP3 ERRORS ****/
	'unsupported_service'   => 'The email service is not supported',
	'imap_stream_not_opened'    => 'Could not open IMAP stream',
	
	
	
);