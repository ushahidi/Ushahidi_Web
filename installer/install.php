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


	public function __construct()
	{
		$this->index();
	}

	public function index()
	{
	   session_start();
	}

	/**
	 * validate the form fields and does the necessary processing.
	 */
	public function install( $username, $password, $host, $select_db_type,
	    $db_name, $table_prefix, $base_path )
    {
	    global $form;
	    $install_directory = dirname(dirname(__FILE__));

	    //check for empty fields
	    if(!$username || strlen($username = trim($username)) == 0 ){
	        $form->set_error("username", "Please enter the username of the
	            database server.");
	    }

	    if( !$host || strlen($host = trim($host)) == 0 ){
	        $form->set_error("host","Please enter the host of the
	            database server." );
	    }

	    if( !$db_name || strlen($db_name = trim($db_name)) == 0 ){
	        $form->set_error("db_name","Please enter a new name for the
	            database to be created.");
	    }

	    // load database.template.php and work from it.
		if(!file_exists('../application/config/database.template.php')){
		    $form->set_error("load_db_tpl","Sorry, I need a database.template.php file to work
            from. Please re-upload this file from your Ushahidi installation.");
		}

		if( !is_writable('../application/config')) {
		    $form->set_error('permission',"Sorry, can't write to the directory.
		    You'll have to either change the permissions on your Ushahidi
		    directory with this command <blockquote>chmod a+w
		    $install_directory/application/config</blockquote> or
		    create your database.php manually.");
		}

		if(!$this->make_connection($username, $password, $host)){
		    $form->set_error("connection","Sorry, couldn't make connection to
		    the database server with the credentials given. Could you double
		    check if they are correct.'");
		}

	    /**
	     * error exists, have user correct them.
	     */
	   if( $form->num_errors > 0 ) {
	        return 1;

	   } else {

	        $this->add_config_details($base_path);

		    $this->add_db_details( $username, $password, $host, $select_db_type,
		       $db_name, $table_prefix );

		    $this->import_sql($username, $password, $host,$db_name);
		    $this->chmod_folders();
	        return 0;
	   }
	}

	/**
	 * adds the database details to the config/database.php file.
	 */
	private function add_db_details( $username, $password, $host,
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
	private function add_config_details( $base_path )
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
	 * Imports sql file to the database.
	 */
	private function import_sql($username, $password, $host,$db_name)
	{
	    $connection = @mysql_connect("$host", "$username", "$password");
	    $db_schema = file_get_contents('../sql/ushahidi.sql');

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
	private function make_connection($username, $password, $host)
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
	 * Change permissions on the cache, logs, and upload folders.
	 */
	private function chmod_folders()
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
}
$install = new Install();
$form = new Form();
?>
