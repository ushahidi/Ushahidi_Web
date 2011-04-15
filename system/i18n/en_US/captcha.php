<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'file_not_found' => 'The specified file, %s, was not found. Please verify that files exist by using file_exists() before using them.',
	'requires_GD2'   => 'The Captcha library requires GD2 with FreeType support. Please see http://php.net/gd_info for more information.',

	// Words of varying length for the Captcha_Word_Driver to pick from
	// Note: use only alphanumeric characters
	'words' => array
	(
		'cd', 'tv', 'it', 'to', 'be', 'or',
		'sun', 'car', 'dog', 'bed', 'kid', 'egg',
		'bike', 'tree', 'bath', 'roof', 'road', 'hair',
		'hello', 'world', 'earth', 'beard', 'chess', 'water',
		'barber', 'bakery', 'banana', 'market', 'purple', 'writer',
		'america', 'release', 'playing', 'working', 'foreign', 'general',
		'aircraft', 'computer', 'laughter', 'alphabet', 'kangaroo', 'spelling',
		'architect', 'president', 'cockroach', 'encounter', 'terrorism', 'cylinders',
	),

	// Riddles for the Captcha_Riddle_Driver to pick from
	// Note: use only alphanumeric characters
	'riddles' => array
	(
		array('Do you hate spam? (yes or no)', 'yes'),
		array('Are you a robot? (yes or no)', 'no'),
		array('Fire is... (hot or cold)', 'hot'),
		array('The season after fall is...', 'winter'),
		array('Which day of the week is it today?', strftime('%A')),
		array('Which month of the year are we in?', strftime('%B')),
	),
);
