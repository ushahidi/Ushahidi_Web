<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'getimagesize_missing'    => 'The Image library requires the getimagesize() PHP function, which is not available in your installation.',
	'unsupported_method'      => 'Your configured driver does not support the %s image transformation.',
	'file_not_found'          => 'The specified image, %s, was not found. Please verify that images exist by using file_exists() before manipulating them.',
	'type_not_allowed'        => 'The specified image, %s, is not an allowed image type.',
	'invalid_width'           => 'The width you specified, %s, is not valid.',
	'invalid_height'          => 'The height you specified, %s, is not valid.',
	'invalid_dimensions'      => 'The dimensions specified for %s are not valid.',
	'invalid_master'          => 'The master dimension specified is not valid.',
	'invalid_flip'            => 'The flip direction specified is not valid.',
	'directory_unwritable'    => 'The specified directory, %s, is not writable.',

	// ImageMagick specific messages
	'imagemagick' => array
	(
		'not_found' => 'The ImageMagick directory specified does not contain a required program, %s.',
	),
	
	// GraphicsMagick specific messages
	'graphicsmagick' => array
	(
		'not_found' => 'The GraphicsMagick directory specified does not contain a required program, %s.',
	),
	
	// GD specific messages
	'gd' => array
	(
		'requires_v2' => 'The Image library requires GD2. Please see http://php.net/gd_info for more information.',
	),
);
