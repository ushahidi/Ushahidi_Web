<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'invalid_rule'  => 'Invalid validation rule used: %s',
	'i18n_array'    => 'The %s i18n key must be an array to be used with the in_lang rule',
	'not_callable'  => 'Callback %s used for Validation is not callable',

	// General errors
	'unknown_error' => 'Unknown validation error while validating the %s field.',
	'required'      => 'The %s field is required.',
	'min_length'    => 'The %s field must be at least %d characters long.',
	'max_length'    => 'The %s field must be %d characters or fewer.',
	'exact_length'  => 'The %s field must be exactly %d characters.',
	'in_array'      => 'The %s field must be selected from the options listed.',
	'matches'       => 'The %s field must match the %s field.',
	'valid_url'     => 'The %s field must contain a valid URL.',
	'valid_email'   => 'The %s field must contain a valid email address.',
	'valid_ip'      => 'The %s field must contain a valid IP address.',
	'valid_type'    => 'The %s field must only contain %s characters.',
	'range'         => 'The %s field must be between specified ranges.',
	'regex'         => 'The %s field does not match accepted input.',
	'depends_on'    => 'The %s field depends on the %s field.',

	// Upload errors
	'user_aborted'  => 'The %s file was aborted during upload.',
	'invalid_type'  => 'The %s file is not an allowed file type.',
	'max_size'      => 'The %s file you uploaded was too large. The maximum size allowed is %s.',
	'max_width'     => 'The %s file you uploaded was too big. The maximum allowed width is %spx.',
	'max_height'    => 'The %s file you uploaded was too big. The maximum allowed height is %spx.',
	'min_width'     => 'The %s file you uploaded was too small. The minimum allowed width is %spx.',
	'min_height'    => 'The %s file you uploaded was too small. The minimum allowed height is %spx.',

	// Field types
	'alpha'         => 'alphabetical',
	'alpha_numeric' => 'alphabetical and numeric',
	'alpha_dash'    => 'alphabetical, dash, and underscore',
	'digit'         => 'digit',
	'numeric'       => 'numeric',
);
