<?php 
/**
 * This class acts like a controller.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General
 * Public License (LGPL)
 */

require_once('form.php');

class Install
{

    private $database_file;

	private $install_directory;
	public function __construct()
	{
		global $form;
	    
	    $this->install_directory = dirname(dirname(__FILE__));
	    
		$this->_index();
	}

	public function _index()
	{
	   session_start();
	}

	/**
	 * validate the form fields and does the necessary processing.
	 */
	public function _install( $username, $password, $host, $select_db_type,
	    $db_name, $table_prefix, $base_path )
    {
	    global $form;
	    //check for empty fields
	    if(!$username || strlen($username = trim($username)) == 0 ){
	        $form->set_error("username", "Please make sure to " .
	        		"enter the <strong>username</strong> of the database server.");
	    }

	    if( !$host || strlen($host = trim($host)) == 0 ){
	        $form->set_error("host","Please enter the <strong>host</strong> of the
	            database server." );
	    }

	    if( !$db_name || strlen($db_name = trim($db_name)) == 0 ){
	        $form->set_error("db_name","Please enter the <strong>name</strong> of your database.");
	    }

	    // load database.template.php and work from it.
		if(!file_exists('../application/config/database.template.php')){
		    $form->set_error("load_db_tpl","<strong>Sorry</strong>, I need a " .
		    		"<config>database.template.php</config> file to work
            from. Please re-upload this file from the Ushahidi files.");
		}
		
		// load .htaccess file and work with it.
		if(!file_exists('../.htaccess')){
		    $form->set_error("load_htaccess_file","<strong>Sorry</strong>, I need a " .
		    		"<code>.htaccess</code> file to work
            with. Please re-upload this file from the Ushahidi files.");
		}

		if( !is_writable('../application/config')) {
		    $form->set_error('permission',
			"<strong>Oops!</strong> Ushahidi is trying to create and/or edit a file called \"" .
			"database.php\" and is unable to do so at the moment. This is probably due to the fact " .
			"that your permissions aren't set up properly for the <code>config</code> folder. " .
			"Please change the permissions of that folder to allow write access (666).  " .
			"More information on changing file permissions can be found at the following " .
			"links: <a href=\"http://www.washington.edu/computing/unix/permissions.html\">" .
			"Unix/Linux</a>, <a href=\"http://support.microsoft.com/kb/308419\">Windows.</a>");
		}

		if(!$this->_make_connection($username, $password, $host)){
		    $form->set_error("connection","<strong>Oops!</strong>, couldn't make connection to
		    the database server with the credentials given. Could you double
		    check if they are correct.");
		}

	    /**
	     * error exists, have user correct them.
	     */
	   if( $form->num_errors > 0 ) {
	        return 1;

	   } else {

	        $this->_add_config_details($base_path);
			
			$this->_add_htaccess_entry($base_path);
			
		    $this->_add_db_details( $username, $password, $host, $select_db_type,
		       $db_name, $table_prefix );

		    $this->_import_sql($username, $password, $host,$db_name);
		    $this->_chmod_folders();
	        
	        $sitename = $this->_get_url();
		    $url = $this->_get_url();
		    $configure_stats = $this->_configure_stats($sitename, $url, $host, $username, $password, $db_name);
	        
	        return 0;
	   }
	}
	
	/**
	 * gets the URL
	 */
	 private function _get_url()
	 {
	 	global $_SERVER;
	 	if ($_SERVER["SERVER_PORT"] != "80") {
			$url = $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		
		return 'http://'.substr($url,0,stripos($url,'/installer/'));
	 }

	/**
	 * adds the database details to the config/database.php file.
	 */
	private function _add_db_details( $username, $password, $host,
	    $select_db_type, $db_name, $table_prefix )
	{

	    $database_file = @file('../application/config/database.template.php');
	    $handle = @fopen('../application/config/database.php', 'w');
	    foreach( $database_file as $line_number => $line )
	    {   
	        switch( trim(substr( $line,0,14 )) ) {
	            case "'type'     =":
	                fwrite($handle, str_replace("'mysql'","'".
	                    $select_db_type."'",$line ));
	                break;

	            case "'user'     =":
	                fwrite($handle, str_replace("'username'","'".
	                    $username."'",$line ));
	                break;
	            case "'pass'     =":
	                fwrite($handle, str_replace("'password'","'".
	                    $password."'",$line));
	                break;

	            case "'host'     =":
	                fwrite($handle, str_replace("'localhost'","'".
	                    $host."'",$line));
	                break;

	            case "'database' =":
	                fwrite($handle, str_replace("'db'","'".
	                    $db_name."'",$line));
	                break;

	            case "'table_prefix":
	                fwrite($handle, str_replace("''","'".
	                    $table_prefix."'",$line));
	                break;

	            default:
	                fwrite($handle, $line);
	        }
	    }

	    fclose($handle);
	    //for security reasons change permission on the file to 666
	    chmod('../application/config/database.php',0666);
	}

	/**
	 * adds the site_name to the application/config/config.php file
	 */
	private function _add_config_details( $base_path )
	{
	    $config_file = @file('../application/config/config.template.php');
        $handle = @fopen('../application/config/config.php', 'w');
		
	    foreach( $config_file as $line_number => $line )
	    {
	        if( !empty( $base_path ) )
            {
	            switch( trim(substr( $line,0,23 )) ) {
	                case "\$config['site_domain']":
	                    fwrite($handle, str_replace("/","/".
	                    $base_path."/",$line ));
	                break;

	                default:
	                    fwrite($handle, $line);
	                }
	        }else {
	           fwrite($handle, $line);
	        }
	    }

	}
	
	/**
	 * Adds the right RewriteBase entry to the .htaccess file.
	 * 
	 * @param base_path - the base path.
	 */
	private function _add_htaccess_entry($base_path) {
		$htaccess_file = file('../.htaccess');
		$handle = fopen('../.htaccess','w');
			
		foreach($htaccess_file as $line_number => $line ) {
			if( !empty($base_path) ) {
				switch( trim( substr($line, 0, 12 ) ) ) {
					case "RewriteBase":
						fwrite($handle, str_replace("/","/".$base_path,$line));
						break;
						
					default:
						fwrite($handle,$line);
				}
			} else {
				fwrite($handle,$line);
			}	
		}	
		
	} 

	/**
	 * Imports sql file to the database.
	 */
	private function _import_sql($username, $password, $host,$db_name)
	{
	    $connection = @mysql_connect("$host", "$username", "$password");
	    $db_schema = @file_get_contents('../sql/ushahidi.sql');

	    $result = @mysql_query('CREATE DATABASE '.$db_name);
	    
	    // select newly created db
	    @mysql_select_db($db_name,$connection);
	    /**
	     * split by ; to get the sql statement for creating individual
	     * tables.
	     */
	    $tables = explode(';',$db_schema);
		
	    foreach($tables as $query) {
	   
	        $result = @mysql_query($query,$connection);
	    }

	    @mysql_close( $connection );

	}

	/**
	 * check if we can make connection to the db server with the credentials
	 * given.
	 */
	private function _make_connection($username, $password, $host)
	{
	    $connection = @mysql_connect("$host", "$username", "$password");
		if( $connection ) {
		    @mysql_close( $connection );
		    return TRUE;
		}else {
		    @mysql_close( $connection );
		    return FALSE;
		}
	}
	
	/**
	 * Set up stat tracking
	 */
	private function _configure_stats($sitename, $url, $host, $username, $password, $db_name)
	{
		$stat_url = 'http://tracker.ushahidi.com/px.php?task=cs&sitename='.urlencode($sitename).'&url='.urlencode($url);
		
		// FIXME: This method of extracting the stat_id will only work as 
		//        long as we are only returning the id and nothing else. It
		//        is just a quick and dirty implementation for now.
		$stat_id = trim(strip_tags($this->_curl_req($stat_url))); // Create site and get stat_id
		
		if($stat_id > 0){
			$connection = @mysql_connect("$host", "$username", "$password");
			@mysql_select_db($db_name,$connection);
			@mysql_query('UPDATE `settings` SET `stat_id` = \''.mysql_escape_string($stat_id).'\' WHERE `id` =1 LIMIT 1;');
			@mysql_close($connection);
			
			return $stat_id;
		}
		
		return false;
	}

	/**
	 * Change permissions on the cache, logs, and upload folders.
	 */
	private function _chmod_folders()
	{
	    @chmod('../application/cache',0777);
	    @chmod('../application/logs',0777);
	    @chmod('../media/uploads',0777);
	}

	/**
	 * check if ushahidi has been installed.
	 */
	public function is_ushahidi_installed()
	{
	    /**
	     * Check if config file exists.
	     */
	    $is_installed = true;
	    if( file_exists('../application/config/database.php') )
	    {

	        $database_file = file('../application/config/database.php');

			if( preg_match( "/username/",$database_file[22] ) &&
				preg_match( "/password/",$database_file[23] ) ){

				$is_installed = false;
			}

	    } else {
	        $is_installed = false;
	    }

	    return $is_installed;
	}
	
	/**
	 * Helper function to send a cURL request
	 * @param url - URL for cURL to hit
	 */
	public function _curl_req( $url )
	{
		// Make sure cURL is installed
		if (!function_exists('curl_exec')) {
			throw new Kohana_Exception('stats.cURL_not_installed');
			return false;
		}
		
		$curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,15); // Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); //Set curl to store data in variable instead of print
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		return $buffer;
	}
	
	/**
	 * Check if relevant directories are writable.
	 */
	public function _check_writable_dir() {
		global $form;
		
		if( !is_writable('../')) {
			$form->set_error('root_dir',"Sorry, can't write to the directory. You'll have to " .
					"change the permission on the directory <code>$this->install_directory/</code>. Make sure its writable by the webserver. <br />");
		}
		
		if( !is_writable('../application/config')) {
		    $form->set_error('config_dir',"Sorry, can't write to the directory.
		    You'll have to either change the permissions on your Ushahidi
		    directory with this command <code>chmod a+w
		    $this->install_directory/application/config</code> or
		    create your database.php manually.");
		}
		
		/**
	     * error exists, have user correct them.
	     */
	   if( $form->num_errors > 0 ) {
	        return 1;

	   } else {
	   		return 0;
	   }
			
	}
	
	public function _include_html_header() {
		//TODO make title tag configurable
		$header = <<<HTML
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>Database Connections / Ushahidi Web Installer</title>
				<link href="../media/css/admin/login.css" rel="stylesheet" type="text/css" />
			</head>
			<script src="../media/js/jquery.js" type="text/javascript" charset="utf-8"></script>
			<script src="../media/js/login.js" type="text/javascript" charset="utf-8"></script>
			</head>
HTML;
		return $header;

	}
	
	public function _get_base_path($request_uri) {
		return substr( substr($request_uri,0,stripos($request_uri,'/installer/')) ,1);
		
	}
}

$install = new Install();
$form = new Form();

?>
