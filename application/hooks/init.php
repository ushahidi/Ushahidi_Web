<?php defined('SYSPATH') or die('No direct script access.');
/**
* Initiate Instance. Verify Install
* If we can't find application/config/database.php, we assume Ushahidi
* is not installed so redirect user to installer
*/
if (!file_exists(DOCROOT."application/config/database.php"))
{
	// not elegant at the moment but  works. I know there is a shorter way of achieving this.
	$doc_root = DOCROOT;
	$folders = explode('/',$doc_root);
	$installer = end(array_filter($folders))."/installer/";
	
	url::redirect($installer);
	
}
