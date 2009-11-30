<?php
/**
 * FORMS INTERNATIONALIZATION
 * Strings associated with form field errors
 */

$lang = array
(
	/**** ALERTS ****/
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
	
	
	/**** AUTHORIZATION / USER / LOGIN ****/
	'name' => array
	(
		'required'		=> 'The full name field is required.',
		'length'		=> 'The full name field must be at least 3 and no more 100 characters long.',
		'standard_text' => 'The username field contains disallowed characters.',
		'login error'	=> 'Please check that you entered the correct name.'
	),
	'email' => array
	(
		'required'	  => 'The email field is required.',
		'email'		  => 'The email field does not appear to contain a valid email address?',
		'length'	  => 'The email field must be at least 4 and no more 64 characters long.',
		'exists'	  => 'Sorry, a user account already exists for this email address.',
		'login error' => 'Please check that you entered the correct email address.'
	),
	'username' => array
	(
		'required'		=> 'The username field is required.',
		'length'		=> 'The username field must be at least 2 and no more 16 characters long.',
		'standard_text' => 'The username field contains disallowed characters.',
		'admin' 		=> 'The admin user role cannot be modified.',
		'superadmin'	=> 'The super admin role cannot boe modified.',
		'exists'		=> 'Sorry, this username is already in use.',
		'login error'	=> 'Please check that you entered the correct username.'
	),
	'password' => array
	(
		'required'		=> 'The password field is required.',
		'length'		=> 'The password field must be at least 5 and no more 16 characters long.',
		'standard_text' => 'The password field contains disallowed characters.',
		'login error'	=> 'Please check that you entered the correct password.'
	),
	'password_confirm' => array
	(
		'matches' => 'The password confirmation field must match the password field.'
	),
	'roles' => array
	(
		'required' => 'You must define at least one role.',
		'values' => 'You must select either ADMIN or USER role.'
	),
	'resetemail' => array
	(
		'required' => 'The email field is required.',
		'invalid' => 'Sorry, we don\'t have your email address',
		'email'  => 'The email field does not appear to contain a valid email address?',
	),
	
	
	
	/**** CATEGORIES ****/
	'parent_id' => array
	(
		'required'		=> 'The parent category field is required.',
		'numeric'		=> 'The parent category field must be numeric.',
		'exists'		=> 'The parent category does not exist.',
		'same'			=> 'The category and the parent category cannot be the same.',
	),
	'category_title' => array
	(
		'required'		=> 'The title field is required.',
		'length'		=> 'The title field must be at least 3 and no more 80 characters long.',
	),
	'category_description' => array
	(
		'required'		=> 'The description field is required.'
	),
	'category_color' => array
	(
		'required'		=> 'The color field is required.',
		'length'		=> 'The color field must be 6 characters long.',
	),
	'category_image' => array
	(
		'valid'		=> 'The image field does not appear to contain a valid file',
		'type'		=> 'The image field does not appear to contain a valid image. The only accepted formats are .JPG, .PNG and .GIF.',
		'size'		=> 'Please ensure that image uploads sizes are limited to 50KB.'
	),
	
	
	
	/**** COMMENTS ****/
	'comment_author' => array
	(
		'required'		=> 'The name field is required.',
		'length'        => 'The name field must be at least 3 characters long.'
	),
	
	'comment_description' => array
	(
		'required'        => 'The comments field is required.'
	),
	
	'comment_email' => array
	(
		'required'    => 'The Email field is required if the checkbox is checked.',
		'email'		  => 'The Email field does not appear to contain a valid email address?',
		'length'	  => 'The Email field must be at least 4 and no more 64 characters long.'
	),
	
	'captcha' => array
	(
		'required' => 'Please enter the security code', 
		'default' => 'Please enter a valid security code'
	),
	
	
	
	/**** CONTACT US ****/
	'contact_name' => array
	(
		'required'		=> 'The name field is required.',
		'length'        => 'The name field must be at least 3 characters long.'
	),
	'contact_subject' => array
	(
		'required'		=> 'The subject field is required.',
		'length'        => 'The subject field must be at least 3 characters long.'
	),
	'contact_message' => array
	(
		'required'        => 'The message field is required.'
	),
	'contact_email' => array
	(
		'required'    => 'The Email field is required if the checkbox is checked.',
		'email'		  => 'The Email field does not appear to contain a valid email address?',
		'length'	  => 'The Email field must be at least 4 and no more 64 characters long.'
	),
	'captcha' => array
	(
		'required' => 'Please enter the security code', 
		'default' => 'Please enter a valid security code'
	),
	
	
	/**** FEEDBACK ****/
	'feedback_title' => array
	(
		'required'		=> 'A title is required.',
		'length'		=> 'The title field must be at least 3 and no more 100 characters long.'
	),
	'feedback_message' => array
	(
		'required' => 'Please enter some information for the reply.',
	),
	'person_name' => array
	(
		'required' => 'Please enter your full name.',
		
	),
	'person_email' => array
	(
		'required' => 'Please enter your email address',
		'email'	=> 'The email field does not appear to contain a valid email address?',
	),
	'feedback_captcha' => array
	(
		'required' => 'Please enter the Security Code.',
		'valid' => 'Please enter a valid security code'
	),
	
	
	/**** CUSTOM FORMS ****/
	'feed_name' => array
	(
		'required' => 'Please enter the name of the feed.',
		'length'   => 'The feed name field must be at least 3 and no more 
			70 characters long.'
	),
	
	'feed_url' => array
	(
		'required' => 'Please enter feed\'s URL.',
		'url' => 'Please enter a valid URL. Eg. http://www.ushahidi.com'
	),
	
	
	/**** ORGANIZATIONS ****/
	'organization_name' => array
	(
		'required'		=> 'The organization name field is required.',
		'length'		=> 'The organization name field must be at least 3 
			and no more 70 characters long.',
		'standard_text' => 'The username field contains disallowed 
			characters.',
	),
	'organization_website' => array
	(
		'required' => 'Please provide the organization\'s website.',
		'url' => 'Please enter a valid URL. Eg. http://www.ushahidi.com'
	),
	'organization_description' => array
	(
		'required' => 'Please provide a little description about the 
			organization.'
	),
	'organization_email' => array
	(
		'email'		  => 'The organization email field does not appear to contain a valid email address?',
		'length'	  => 'The organization email field must be at least 4 and no more 100 characters long.'
	),
	'organization_phone1' => array
	(
		'length'		=> 'The organization phone 1 field must be at least 3 and no more 50 characters long.'
	),
	'organization_phone2' => array
	(
		'length'		=> 'The organization phone 1 field must be at least 3 and no more 50 characters long.'
	),
	
	
	/**** REPORTS / INCIDENTS ****/
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
	),
	
	
	
	/**** SETTINGS ****/
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
	'default_map_all' => array
	(
		'required'		=> 'The color field is required.',
		'alpha_numeric'		=> 'The color feed field does not appear to contain a valid value?',
		'length'		=> 'The color field must be no more 6 characters long.',
	),
	'api_akismet' => array
	(
		'alpha_numeric'		=> 'The Akismet field does not appear to contain a valid value?',
		'length'		=> 'The Akismet field does not appear to contain a valid value?'
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
	),
	'email_username' => array
	(
		'required'		=> 'The Mail Server Username field is required.',
		'length'		=> 'The Mail Server Username field must be no more 50 characters long.',
	),
	'email_password' => array
	(
		'required'		=> 'The Mail Server Password field is required.',
		'length'		=> 'The Mail Server Password field must be at least 5 and no more 50 characters long.',
	),
	'email_port' => array
	(
		'numeric'		=> 'The Mail server port field should contain numbers only.',
		'length' 		=> 'The Mail server port field is too long'
	),
	'email_host' => array
	(
		'numeric'		=> 'The Mail server port field should contain numbers only.',
		'length' 		=> 'The Mail server port field is too long'
	),
	'email_servertype' => array
	(
		'required'		=> 'The Mail Server Type field is required.',
		'length' 		=> 'The Mail server port field is too long'
	),
	
		
);
