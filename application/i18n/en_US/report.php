<?php
$lang = array(
	'comments_form_error' => 'error!',
	'country_name' => array(
		'single_country' => 'This deployment spans within one country only. Please make sure the report location is within the country %s.',
	) ,
	'custom_field' => array(
		'values' => 'Please enter a valid value for the "%s" field.',
		'numeric' => 'The "%s" field must be numeric.',
		'required' => 'The "%s" field is required.',
		'not_exist' => 'The "%s" field is does not exist.',
		'permission' => 'The "%s" field cannot be edited by your account.',
		'email' => 'The "%s" field must contain a valid email address.',
		'phone' => 'The "%s" field must contain a valid phone number.',
		'date_mmddyyyy' => 'The "%s" field must contain a valid date (MM/DD/YYYY).',
		'date_ddmmyyyy' => 'The "%s" field must contain a valid date (DD/MM/YYYY).',
	) ,
	'data_include' => array(
		'between' => 'Please select a valid item to include in the download.',
		'numeric' => 'Please select a valid item to include in the download.',
	) ,
	'data_active' => array(
		'between' => 'Please select "Reports Awaiting Approval" or "Approved Reports" or both.',
		'numeric' => 'Please select "Reports Awaiting Approval" or "Approved Reports" or both.',
		'required' => 'Please select "Reports Awaiting Approval" or "Approved Reports" or both.',
	) ,
	'data_verified' => array(
		'between' => 'Please select "Reports Awaiting Verification" or "Verified Reports" or both.',
		'numeric' => 'Please select "Reports Awaiting Verification" or "Verified Reports" or both.',
		'required' => 'Please select "Reports Awaiting Verification" or "Verified Reports" or both.',
	) ,
	'data_point' => array(
		'between' => 'Please select a valid type of report to download.',
		'numeric' => 'Please select a valid type of report to download.',
		'required' => 'Please select a valid type of report to download.',
	) ,
	'format' => array(
		'required' => 'You must select a download format. Select either CSV or XML.',
		'valid' => 'Please select a valid format to download your reports in',
	),
	'from_date' => array(
		'date_mmddyyyy' => 'The FROM date field does not appear to contain a valid date.',
		'range' => 'Please enter a valid FROM date. It cannot be greater than today.',
	) ,
	'incident_active' => array(
		'between' => 'Please enter a valid value for Approve This Report',
		'required' => 'Please enter a valid value for Approve This Report',
	) ,
	'incident_ampm' => array(
		'validvalues' => 'The am/pm field does not appear to contain a valid value.',
	) ,
	'incident_category' => array(
		'numeric' => 'The "Categories" field does not appear to contain a valid category.',
		'required' => 'The "Categories" field is required.',
	) ,
	'incident_date' => array(
		'date_ddmmyyyy' => 'The date field does not appear to contain a valid date.',
		'date_mmddyyyy' => 'The date field does not appear to contain a valid date.',
		'required' => 'The date field is required.',
	) ,
	'incident_description' => array(
		'required' => 'The "Description" field is required.',
	) ,
	'incident_hour' => array(
		'between' => 'The hour field does not appear to contain a valid hour.',
		'required' => 'The hour field is required.',
	) ,
	'incident_information' => array(
		'alpha' => 'Please enter a valid value for Information Probability.',
		'length' => 'Please enter a valid value for Information Probability.',
	) ,
	'incident_minute' => array(
		'between' => 'The minute field does not appear to contain a valid value.',
		'required' => 'The minute field is required.',
	) ,
	'incident_news' => array(
		'url' => 'The news source links field does not appear to contain a valid URL.',
	) ,
	'incident_photo' => array(
		'size' => 'Please ensure that photo upload sizes are limited to 2MB.',
		'type' => 'The Upload Photos field does not appear to contain a valid image. The only accepted formats are .JPG, .PNG and .GIF.',
		'valid' => 'The Upload Photos field does not appear to contain a valid file.',
	) ,
	'incident_source' => array(
		'alpha' => 'Please enter a valid value for Source Reliability.',
		'length' => 'Please enter a valid value for Source Reliability.',
	) ,
	'incident_title' => array(
		'length' => 'The "Report Title" field must be at least 3 and no more 200 characters long.',
		'required' => 'The "Report Title" field is required.',
		'csrf' => 'Possible CSRF attack. Did you really mean to create/edit a report?',
	) ,
	'incident_verified' => array(
		'between' => 'Please enter a valid value for Verify This Report.',
		'required' => 'Please enter a valid value for Verify This Report.',
	) ,
	'incident_video' => array(
		'url' => 'The video links field does not appear to contain a valid URL.',
	) ,
	'latitude' => array(
		'between' => 'The latitude field does not appear to contain a valid latitude.',
		'required' => 'The latitude field is required. Please click on the map to pinpoint a location.',
	) ,
	'locale' => array(
		'alpha_dash' => 'The locale field has an incorrect value. ',
		'exists' => 'This report already has a translation for this language.',
		'length' => 'The locale field has an incorrect value. ',
		'locale' => 'The Original Report and the Translation have the same locale (language).',
		'required' => 'The locale is required.',
	) ,
	'location_name' => array(
		'length' => 'The "Location Name" field must be at least 3 and no more 200 characters long.',
		'required' => 'The "Location Name" field is required.',
	) ,
	'longitude' => array(
		'between' => 'The longitude field does not appear to contain a valid longitude.',
		'required' => 'The longitude field is required. Please click on the map to pinpoint a location.',
	) ,
	'person_email' => array(
		'email' => 'The email field does not appear to contain a valid email address.',
		'length' => 'The email field must be at least 4 and no more 64 characters long.',
	) ,
	'person_first' => array(
		'length' => 'The first name field must be at least 3 and no more 100 characters long.',
	) ,
	'person_last' => array(
		'length' => 'The last name field must be at least 2 and no more 100 characters long.',
	) ,
	'to_date' => array(
		'date_mmddyyyy' => 'The TO date field does not appear to contain a valid date.',
		'range' => 'Please enter a valid TO date. It cannot be greater than today.',
		'range_greater' => 'Your FROM date cannot be greater than your TO date.',
	)
);
?>
