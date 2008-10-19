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
		)
	);

?>