<?php defined('SYSPATH') or die('No direct access allowed.');

$lang = array
(
	// Class errors
	'error_format'  => 'Your error message string must contain the string {message} .',

	// General errors
	// {name} fieldname
	// {value} fieldvalue	
	'unknown_error' => 'Unknown validation error while validating the %s field.',
	'rule_required'      => 'The {name} field is required.',
	'rule_min_length'    => 'The {name} field must be at least {min_length} characters long.',
	'rule_max_length'    => 'The {name} field must be {max_length} characters or fewer.',
	'rule_exact_length'  => 'The {name} field must be exactly {length} characters.',
	'rule_in_array'      => 'The {name} field must be selected from the options listed.',
	'rule_matches'       => 'The {name} field must match the {match_field} field.',
	'rule_url'  		 => 'The {name} field must contain a valid URL.',
	'rule_email' 		 => 'The {name} field must contain a valid email address.',
	'rule_ip'     		 => 'The {name} field must contain a valid IP address.',
	'rule_type'    	     => 'The {name} field must only contain {depends_on_field} characters.',
	'rule_range'         => 'The {name} field must be between specified ranges.',
	'rule_regex'         => 'The {name} field does not match accepted input.',
	'rule_depends_on'    => 'The {name} field depends on the {depends_on_field} field.',
	'rule_length'		 => 'The {name} field must be contain between {min_length} amd {max_length} characters.',
	'rule_numeric'		 => 'The {name} field must only contain numeric characters.',
	'rule_alpha_numeric' => 'The {name} field must only contain alpha-numeric characters.',
	'rule_alpha'		 => 'The {name} field must only contain alpha characters.',

	// Upload errors 
	// {filename} - filename
	// {type}     - filetype as given by browser (unsafe)
	// {tmp_name}
	// {error} 	  - error code http://nl.php.net/manual/en/features.file-upload.errors.php
	// {size} 
	// {bytes} - size in bytes, only rule_upload_size
	// {mimetype} - rule_upload_allowed

	'rule_upload_allowed'  	=> 'The {filename} file is not an allowed file type.',
	'rule_upload_size'      => 'The {filename file you uploaded was too large. The maximum size allowed is {max_bytes}.',

	//Not yet implemented
	'max_width'     => 'The %s file you uploaded was too big. The maximum allowed width is %spx.',
	'max_height'    => 'The %s file you uploaded was too big. The maximum allowed height is %spx.',
	'min_width'     => 'The %s file you uploaded was too small. The minimum allowed width is %spx.',
	'min_height'    => 'The %s file you uploaded was too small. The minimum allowed height is %spx.',

);
