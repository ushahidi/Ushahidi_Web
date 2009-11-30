<?php

$lang = array
(
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
		'login error'	=> 'Please check that you entered the correct password.',
		'matches'		=> 'Please enter the same password in the two password fields.'
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

        'forgot_password' => 'Forgot password?',
);
