<?php defined('SYSPATH') or die('No direct script access.');
/**
* Initiate Instance. Verify Install
* If we can't find application/config/database.php, we assume Ushahidi
* is not installed so redirect user to installer
*/
if (!file_exists(DOCROOT."application/config/database.php"))
{
  url::redirect(DOCROOT."installer/");
}