<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Messages Controller.
 * View SMS Messages Received Via FrontlineSMS
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source .ushahididev.com
 * @module     Admin Messages Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Upgrade_Controller extends Admin_Controller {

    protected $db;
    protected $upgrade;
    protected $release;

    function __construct()
    {
        parent::__construct();
        
        $this->db = new Database();
        
        $this->template->this_page = 'upgrade';
        $this->upgrade = new Upgrade;
        $this->release = $this->upgrade->_fetch_core_release();
        
        $release_version = $this->_get_release_version(); 
        
        // limit access to only superadmin
        /*if ( ! $this->auth->logged_in('superadmin') OR 
                ( $release_version == "" ) )
        {
            url::redirect('admin/dashboard');
        }*/
    }

    /**
     * Upgrade page.
     *
     */
    public function index()
    {
        $this->template->content = new View('admin/upgrade');

        $form_action = "";
        
        $this->template->content->title = Kohana::lang('ui_admin.upgrade_ushahidi');

        //$this->template->content->db_version =  Kohana::config('
          //      settings.db_version');
        $this->template->content->db_version = Kohana::config('settings.db_version');

         //Setup and initialize form fields names
        $form = array 
        (
            'chk_db_backup_box' => ''
        );

        //check if form has been submitted
        if ( $_POST )
        {
            // For sanity sake, validate the data received from users.
            $post = Validation::factory(array_merge($_POST,$_FILES));

            // Add some filters
            $post->pre_filter('trim', TRUE);
            
            $post->add_rules('chk_db_backup_box', 'between[0,1]');
            
            if ($post->validate())
            {
                $this->template->content = new View('admin/upgrade_status');
                $this->template->content->title = Kohana::lang('ui_admin.upgrade_ushahidi_status');

                $url = $this->release->download;

                $working_dir = Kohana::config('upload.relative_directory')."/upgrade/";
                // Create the Directory if it doesn't exist
				if ( ! file_exists(DOCROOT."media/uploads/upgrade"))
				{
					mkdir(DOCROOT."media/uploads/upgrade");
					chmod(DOCROOT."media/uploads/upgrade",0777);
				}


                $zip_file = Kohana::config('upload.relative_directory')."/upgrade/ushahidi.zip";
        
                //download the latest ushahidi
                $this->upgrade->log[] = sprintf("Downloading latest ushahidi...");
                $latest_ushahidi = $this->upgrade->download_ushahidi($url);
                    
                //download went successful
                if ($this->upgrade->success)
                {
                    $this->upgrade->write_to_file($latest_ushahidi, $zip_file);
                }
            
                //extract compressed file
                if ($this->upgrade->success)
                {
                    $this->upgrade->unzip_ushahidi($zip_file, $working_dir);
                }
                
                if ($this->upgrade->success)
                {
                    $this->upgrade->log[] = sprintf("Copying files...");
                    $this->upgrade->copy_recursively($working_dir."/ushahidi",DOCROOT);
                    $this->upgrade->log[] = sprintf("Successfully copied files");
                }
        
                if ($this->upgrade->success)
                {
					if (file_exists($working_dir."/ushahidi/sql"))
					{
                    	if ($post->chk_db_backup_box == 1)
	                    {
                                        
	                        // backup database.
	                        //is gzip enabled ?
	                        $gzip = Kohana::config('config.output_compression');
	                        $error = $this->_do_db_backup( $gzip );
	                        $this->upgrade->log[] = sprintf("Database backup in progress.");       
                    
	                        if (empty($error))
	                        {
	                            $this->upgrade->log[] = sprintf("Database backup went successful.");
                                                    
	                            //uprade tables.
	                            $this->upgrade->log[] = sprintf("Upgrade table.");
	                            $this->_process_db_upgrade($working_dir."/ushahidi/sql/");
	                            $this->upgrade->log[] = sprintf("Table upgrade successful.");

	                        }
	                        else
	                        {
	                            $this->upgrade->errors[] = sprintf("Oops, database backup failed.");
	                            $this->template->content->errors = $this->upgrade->errors;
	                        }
	                    }
	                    else
	                    {                 
	                        //uprade tables.
	                        $this->upgrade->log[] = sprintf("Upgrade table.");
	                        $this->_process_db_upgrade($working_dir."/ushahidi/sql/");
	                        $this->upgrade->log[] = sprintf("Table upgrade successful.");

	                    }
					}
        
                    if ($this->upgrade->success)
                    {
                        $this->upgrade->remove_recursively( $working_dir);
                        $this->upgrade->log[] = sprintf( "Upgrade went successful." );
                    }
                }

            }
             // No! We have validation errors, we need to show the form again, with the errors
            else
            {
                
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('upgrade'));
                $form_error = TRUE;
            }

        }
        
        $this->template->content->form_action = $form_action;
        $this->template->content->current_version = Kohana::config('settings.ushahidi_version');
        $this->template->content->current_db_version = ($this->release == true) ? $this->release->version_db : "";
        $this->template->content->environment = $this->_environment();
        $this->template->content->release_version = (is_object($this->release) == true) ? $this->release->version : "";
        $this->template->content->changelogs = (is_object($this->release) == true) ? $this->release->changelog : array();
        $this->template->content->logs = $this->upgrade->log;
        $this->template->content->download = (is_object($this->release) == true) ? $this->release->download : "";

    }
        
    public function status() 
    {
        $this->template->content = new View('admin/upgrade_status');
        $this->template->cntent = Kohana::lang('upgrade.upgrade_status');

        if (count($upgrade->errors) == 0)
        {
            $this->template->content->title = Kohana::lang('ui_admin.upgrade_ushahidi_status');
            $this->template->content->logs = $upgrade->log;
        }
        else
        {
            $this->template->content->title = Kohana::lang('ui_admin.upgrade_ushahidi_status');
            $this->template->content->errors = $upgrade->errors;
        }
            
    }
           
    /**
     * Execute SQL statement to upgrade the necessary tables.
     *
     * @param string - upgrade_sql - upgrade sql file
     */
    private function _execute_upgrade_script($upgrade_sql) 
    {
        
        $upgrade_schema = @file_get_contents($upgrade_sql);

        // If a table prefix is specified, add it to sql
        $db_config = Kohana::config('database.default');
        $table_prefix = $db_config['table_prefix'];
        
        if ($table_prefix)
        {
            $find = array(
                'CREATE TABLE IF NOT EXISTS `',
                'INSERT INTO `',
                'ALTER TABLE `',
                'UPDATE `'
            );
            
            $replace = array(
                'CREATE TABLE IF NOT EXISTS `'.$table_prefix.'_',
                'INSERT INTO `'.$table_prefix.'_',
                'ALTER TABLE `'.$table_prefix.'_',
                'UPDATE `'.$table_prefix.'_'
            );
            
            $upgrade_schema = str_replace($find, $replace, $upgrade_schema);
        }

        // Split by ; to get the sql statement for creating individual tables.
        $queries = explode( ';',$upgrade_schema );
        
        // get the database object.
    
        foreach ($queries as $query)
        {
            $result = $this->db->query($query);
        }
            
        // Delete cache
        $cache = Cache::instance();
        $cache->delete(Kohana::config('settings.subdomain').'_settings');
        
    }
    
    /**
     * Get the available sql update scripts from the 
     * sql folder then upgrade necessary tables.
     */
    private function _process_db_upgrade($dir_path)
    {
    
        $upgrade_sql = '';

        $files = scandir($dir_path);
        foreach ( $files as $file )
        {
            $upgrade_sql = $this->_get_db_version();
            
            if ($upgrade_sql == $file)
            {
                $this->_execute_upgrade_script($dir_path.$upgrade_sql);
            } 
        }
        return "";
    }
    
    /**
     * Gets the current db version of the ushahidi deployment.
     * 
     * @return the db version.
     */
    private function _get_db_version() 
    {
            
       // get the db version from the settings page
        $this->db = new Database();
        $sql = 'SELECT db_version from '.Kohana::config('database.default.table_prefix').'settings';
        $settings = $this->db->query($sql);
        $version_in_db = $settings[0]->db_version;
        
        // Update DB
        $db_version = $version_in_db;

        $upgrade_to = $db_version + 1;
        
        return 'upgrade'.$db_version.'-'.$upgrade_to.'.sql';
        
    }
    
    /**
     * See if mysqldump exist, then detect its installed path.
     *
     * Most of the code here were borrowed from 
     * @return (array) $paths - include mysql and mysqldump application's path.
     */
    private function _detect_mysql()
    {
        
        $paths = array('mysql' => '', 'mysqldump' => '');
        
        //check for platform
        if (substr(PHP_OS,0,3) == 'WIN')
        {
            
            $result = mysql_query("SHOW VARIABLES LIKE 'basedir'");
            
            $mysql_install = mysql_fetch_array($result);

            if (is_array($mysql_install) AND sizeof($mysql_install)>0 )
            {
                $install_path = str_replace('\\', '/', $mysql_install[0]->Value);
                $paths['mysql'] = $install_path.'bin/mysql.exe';
                $paths['mysqldump'] = $install_path.'bin/mysqldump.exe';
            }
            else
            {
                $paths['mysql'] = 'mysql.exe';
                $paths['mysqldump'] = 'mysqldump.exe';
            }
        }
        else
        {
            if (function_exists('exec'))
            {
                $paths['mysql'] = @exec('which mysql');
                $paths['mysqldump'] = @exec('which mysqldump');
            }
            else
            {
                $paths['mysql'] = 'mysql';
                $paths['mysqldump'] = 'mysqldump';
            }
                
            return $paths; 
        }
        
    }

    /**
     * Backup database
     *
     * @param boolean - gzip - set to false by default 
     * 
     * @return void or error message
     */
    private function _do_db_backup( $gzip=FALSE ) 
    {
        $mysql_path = $this->_detect_mysql();

        $backup = array();
        $backup += $mysql_path;
        $backup['user'] = Kohana::config('database.default.connection.user');
        $backup['host'] = Kohana::config('database.default.connection.host');
        $backup['database'] = Kohana::config('database.default.connection.database');
        $backup['password'] = Kohana::config('database.default.connection.password');
        $backup['date'] = time();
        $backup['filepath'] = preg_replace('/\//', '/', Kohana::config('upload.relative_directory'));
        $backup['filename'] = $backup['filepath'].'/'.$backup['date'].'_-_'.'backup.sql';

        if ($gzip)
        {
            $backup['filename'] = $backup['filename'].'.gz';
            $command = $backup['mysqldump'].' --host="'.$backup['host'].'" --user="'.$backup['user'].'" --password="'.$backup['password'].'" --add-drop-table --skip-lock-tables '.$backup['database'].' | gzip > '.$backup['filename'];
        } 
        else
        {
            $backup['filename'] = $backup['filename'];
            $command = $backup['mysqldump'].' --host="'.$backup['host'].'" --user="'.$backup['user'].'" --password="'.$backup['password'].'" --add-drop-table --skip-lock-tables '.$backup['database'].' > '.$backup['filename'];
        }
                
        //Execute mysqldump command
        if (substr(PHP_OS, 0, 3) == 'WIN')
        {
            $writable_dir = $backup['filepath'];
            $tmpnam = $writable_dir.'/backup_script.sql';
            $fp = fopen($tmpnam, 'w');
            fwrite($fp, $command);
            fclose($fp);
            system($tmpnam.' > NUL', $error);
            unlink($tmpnam);
            
        }
        else
        {        
            passthru($command, $error);
        }
                   
        return $error;  
    }
    
    /**
     * Get the operating environment Ushahidi is on.
     *
     * @return string
     */
    private function _environment()
    {
        $environment = "";
        $environment .= str_replace("/", "&nbsp;", preg_replace("/ .*$/", "", $_SERVER["SERVER_SOFTWARE"]));
        $environment .= ", PHP&nbsp;".phpversion();
        
        return $environment;
    }

    /**
     * Fetches the latest ushahidi release version number
     *
     * @return int or string
     */
    private function _get_release_version()
    {
        if (is_object($this->release))
        {
            $release_version = $this->release->version;
		
            $version_ushahidi = Kohana::config('settings.ushahidi_version');
	    	
            if ($this->_new_or_not($release_version,$version_ushahidi))
            {
			    return $release_version;
		    } 
            else 
            {
			    return "";
		    }
        }
        else
        {
            return "";
        }
    }
    
    /**
     * Checks version sequence parts
     *
     * @param string release_version - The version released.
     * @param string version_ushahidi - The version of ushahidi installed.
     *
     * @return boolean
     */
    private function _new_or_not($release_version=NULL,
            $version_ushahidi=NULL )
    {
        if ($release_version AND $version_ushahidi)
	    {
		    // Split version numbers xx.xx.xx
		    $remote_version = explode(".", $release_version);
		    $local_version = explode(".", $version_ushahidi);

		    // Check first part .. if its the same, move on to next part
		    if (isset($remote_version[0]) AND isset($local_version[0])
			    AND (int) $remote_version[0] > (int) $local_version[0])
		    {
			    return true;
		    }

		    // Check second part .. if its the same, move on to next part
		    if (isset($remote_version[1]) AND isset($local_version[1])
			    AND (int) $remote_version[1] > (int) $local_version[1])
		    {
			    return true;
		    }

		    // Check third part
		    if (isset($remote_version[2]) AND isset($local_version[2])
			    AND (int) $remote_version[2] > (int) $local_version[2])
		    {
			    return true;
		    }

		}

        return false;
    }

}
