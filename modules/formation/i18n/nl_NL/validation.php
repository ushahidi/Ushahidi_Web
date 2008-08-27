<?php defined('SYSPATH') or die('No direct access allowed.');

$lang = array
(
	// Class errors
	'error_format'  => 'Je foutbericht string moet de string {message} bevatten.',

	// Algemene errors
	// {name} fieldname
	// {value} fieldvalue
	
	'unknown_error' 	=> 'Onbekende validatiefout bij het valideren van het %s veld.',
	'rule_required'     => 'Het {name} veld is verplicht.',
	'rule_min_length'   => 'Het {name} veld moet minstens {min_length} karakters lang zijn.',
	'rule_max_length'   => 'Het {name} veld mag maximum {max_length} karakters lang zijn.',
	'rule_exact_length' => 'Het {name} veld moet exact {length} karakters lang zijn.',
	'rule_array'    	=> 'Het {name} veld moet geselecteerd worden uit de gegeven opties.',
	'rule_matches'  	=> 'Het {name} veld moet overeenkomen met het {match_field} veld.',
	'rule_url'      	=> 'Het {name} veld moet een geldige URL zijn.',
	'rule_email'    	=> 'Het {name} veld moet een geldig e-mailadres zijn.',
	'rule_ip'      		=> 'Het {name} veld moet een geldig IP-adres zijn.',
	'rule_range'   		=> 'Het {name} veld moet tussen bepaalde waardes liggen.',
	'rule_regex'       	=> 'Het {name} veld valideert niet als geldige invoer.',
	'rule_depends_on'  	=> 'Het {name} veld is afhankelijk van het {depends_on_field} veld.',
	'rule_length'		=> 'Het {name} veld moet tussen de {min_length} en de {max_length} karakters bevatten.',
	'rule_numeric'		=> 'Het {name} veld mag alleen maar getallen bevatten.',
	'rule_alpha_numeric'=> 'Het {name} veld mag alleen maar alfanumerieke karakters of getallen bevatten.',
	'rule_alpha'		=> 'Het {name} veld mag alleen maar alfanumerieke karakters bevatten.',

	// Upload errors 
	// {filename} - filename
	// {type}     - filetype as given by browser (unsafe)
	// {tmp_name}
	// {error} 	  - error code http://nl.php.net/manual/en/features.file-upload.errors.php
	// {size} 
	// {max_bytes} - size in bytes, only rule_upload_size
	// {max_size}
	// {mimetype} - rule_upload_allowed
	'rule_upload_allowed'  	=> 'Het bestandstype van het {filename} bestand is niet toegestaan.',
	'rule_upload_size'      => 'Het {filename} bestand dat je wilde uploaden is te groot. De maximum toegelaten grootte is {max_bytes} bytes.',

	//Not yet implemented
	'max_width'     => 'Het %s upgeloade bestand is te groot: maximum toegelaten breedte is %spx.',
	'max_height'    => 'Het %s upgeloade bestand is te groot: maximum toegelaten hoogte is %spx.',
	'min_width'     => 'Het %s upgeloade bestand is te klein: minimum toegelaten breedte is %spx.',
	'min_height'    => 'Het %s upgeloade bestand is te klein: minimum toegelaten breedte is %spx.',
);