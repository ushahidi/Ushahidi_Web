<?php
	$lang = array(
	'email' => array(
		'email' => 'The email you entered is not a valid email address.',
		'exists' => 'Sorry, a user account already exists for this email address.',
		'length' => 'The email field must be at least 4 and no more 64 characters long.',
		'required' => 'The email field is required.',
	),
	'name' => array(
		'length' => 'The name field must be at least 3 and no more 100 characters long.',
		'required' => 'The name field is required.',
		'standard_text' => 'The username field contains disallowed characters.',
	),
	'current_password' => array(
		'length' => 'The password field must be at least 8 characters long.',
		'login error' => 'Please check that you entered the correct email and password.',
		'matches' => 'Please enter the same password in the two password fields.',
		'required' => 'The password field is required.',
		'alpha_dash' => 'The password field must have alphabetical characters, the # and @ symbols, numbers, underscores and dashes only',
		'incorrect' => 'The current password you entered for your account is incorrect. Please try again.',
	),
	'new_password' => array(
		'length' => 'The password field must be at least 8 characters long.',
		'login error' => 'Please check that you entered the correct email and password.',
		'matches' => 'Please enter the same password in the two password fields.',
		'required' => 'The password field is required.',
		'alpha_dash' => 'The password field must have alphabetical characters, the # and @ symbols, numbers, underscores and dashes only',
	),
	'password' => array(
		'default' => 'There has been an error attempting to log you in.',
		'length' => 'The password field must be at least 8 characters long.',
		'login error' => 'Please check that you entered the correct email and password.',
		'matches' => 'Please enter the same password in the two password fields.',
		'required' => 'The password field is required.',
		'riverid server down' => 'The authentication server is down. Please try again later.',
		'alpha_dash' => 'The password field must have alphabetical characters, the # and @ symbols, numbers, underscores and dashes only',
	),
	'password_confirm' => array(
		'matches' => 'The password confirmation field must match the password field.',
	),
	'resetemail' => array(
		'email' => 'The email you entered is not a valid email address.',
		'invalid' => 'Sorry, we don\'t have your email address',
		'required' => 'The email field is required.',
	),
	'role' => array(
		'superadmin_modify' => 'Only a superadmin may modify a superadmin or upgrade a user to admin.',
	),
	'roles' => array(
		'alpha_numeric' => 'Invalid role format.',
		'length' => 'The role field must be at least 5 and no more than 30 characters long.',
		'required' => 'You must define at least one role.',
		'values' => 'You must select either ADMIN or USER role.',
	),
	'username' => array(
		'admin' => 'The admin user role cannot be modified.',
		'alpha_numeric' => 'The username field must only contain numbers and letters.',
		'exists' => 'Sorry, this username is already in use.',
		'length' => 'The username field must be at least 2 and no more 100 characters long.',
		'login error' => 'Please check that you entered the correct username.',
		'required' => 'The username field is required.',
		'superadmin' => 'The super admin role cannot be modified.',
	));
?>
