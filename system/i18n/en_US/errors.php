<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Framework Error',   'Please check the Kohana documentation for information about the following error.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Page Not Found',    'The requested page was not found. It may have moved, been deleted, or archived.'),
	E_DATABASE_ERROR     => array( 1, 'Database Error',    'A database error occurred while performing the requested procedure. Please review the database error below for more information.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Recoverable Error', 'An error was detected which prevented the loading of this page. If this problem persists, please contact the website administrator.'),
	E_ERROR              => array( 1, 'Fatal Error',       ''),
	E_USER_ERROR         => array( 1, 'Fatal Error',       ''),
	E_PARSE              => array( 1, 'Syntax Error',      ''),
	E_WARNING            => array( 1, 'Warning Message',   ''),
	E_USER_WARNING       => array( 1, 'Warning Message',   ''),
	E_STRICT             => array( 2, 'Strict Mode Error', ''),
	E_NOTICE             => array( 2, 'Runtime Message',   ''),
);
