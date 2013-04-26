<?php

/**
 * PHP handler for jWYSIWYG file uploader.
 *
 * By Alec Gorge <alecgorge@gmail.com>
 */

define('DEBUG', true);

if (DEBUG) {
	error_reporting(E_ALL | E_NOTICE | E_STRICT);
	ini_set('display_errors', 'On');
	ini_set('display_startup_errors', 'On');

	$error_log_file = dirname(__FILE__) . '/errors.log';

	// Test if error log is writable.
	$error = '';
	if (file_exists($error_log_file) && !is_writable($error_log_file)) {
		$error = 'Make file "' . $error_log_file . '" writable or set debug to false.';
	} else if (!file_exists($error_log_file) && !is_writable(dirname($error_log_file))) {
		$error = 'Make dir "' . dirname($error_log_file) . '" writable or set debug to false.';
	}
	if ($error) {
		header('Content-type: text/html; charset=UTF-8');
		print('{"error":"file-manager.php: ' . htmlentities($error) . '","success":false}');
		exit();
	}

	ini_set('error_log', $error_log_file);
	ini_set('log_errors', 'On');
}

if (extension_loaded('mbstring')) {
	mb_internal_encoding('UTF-8');
	mb_regex_encoding('UTF-8');
}

require_once 'common.php';
require_once 'handlers.php';

ResponseRouter::getInstance()->run();
