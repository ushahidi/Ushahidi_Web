<?php
	$lang = array(
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
		)
	);

?>