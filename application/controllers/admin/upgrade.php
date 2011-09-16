<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Messages Controller.
 * View SMS Messages Received Via FrontlineSMS
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source .ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
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
		
		// Don't show auto-upgrader when disabled.
        if (Kohana::config('config.enable_auto_upgrader') == FALSE)
        {
			die(Kohana::lang('ui_main.disabled'));
        }
		
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
		  //	  settings.db_version');
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
				$this->upgrade->logger("STARTED UPGRADE\n~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~");
				$this->template->content = new View('admin/upgrade_status');
				$this->template->js = new View('admin/upgrade_status_js');
				$this->template->js->backup = $post->chk_db_backup_box;
				$this->template->content->title = Kohana::lang('ui_admin.upgrade_ushahidi_status');
				
				$this->session->set('ftp_server', $post->ftp_server);
				$this->session->set('ftp_user_name', $post->ftp_user_name);
				$this->session->set('ftp_user_pass', $post->ftp_user_pass);
				
				$settings = ORM::factory("settings")->find(1);
				$settings->ftp_server = $post->ftp_server;
				$settings->ftp_user_name = $post->ftp_user_name;
				$settings->save();
				
				// Log file location
				$this->template->js->log_file = url::site(). "admin/upgrade/logfile?f=".$this->session->get('upgrade_session').".txt";
			}
			 // No! We have validation errors, we need to show the form again, with the errors
			else
			{
				$this->template->js = new View('admin/upgrade_js');
				
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('upgrade'));
				$form_error = TRUE;
			}
		}
		else
		{
			$this->template->js = new View('admin/upgrade_js');
		}
		
		$settings = ORM::factory("settings")->find(1);
		$this->template->content->ftp_server = $settings->ftp_server;
		$this->template->content->ftp_user_name = $settings->ftp_user_name;
		
		$this->template->content->form_action = $form_action;
		$this->template->content->current_version = Kohana::config('settings.ushahidi_version');
		$this->template->content->current_db_version = ($this->release == true) ? $this->release->version_db : "";
		$this->template->content->environment = $this->_environment();
		$this->template->content->release_version = (is_object($this->release) == true) ? $this->release->version : "";
		$this->template->content->changelogs = (is_object($this->release) == true) ? $this->release->changelog : array();
		$this->template->content->download = (is_object($this->release) == true) ? $this->release->download : "";
		$this->template->content->critical = (is_object($this->release) == true) ? $this->release->critical : "";
	}
		
	public function status($step = 0) 
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		$url = $this->release->download;
		$working_dir = Kohana::config('upload.relative_directory')."/upgrade/";
		$zip_file = Kohana::config('upload.relative_directory')."/upgrade/ushahidi.zip";	
		
		if ($step == 0)
		{
			$this->upgrade->logger("Downloading latest version of ushahidi...");
			echo json_encode(array("status"=>"success", "message"=>"Downloading latest version of ushahidi..."));
		}
		
		if ($step == 1)
		{
			// Create the Directory if it doesn't exist
			if ( ! file_exists(DOCROOT."media/uploads/upgrade"))
			{
				mkdir(DOCROOT."media/uploads/upgrade");
				chmod(DOCROOT."media/uploads/upgrade",0777);
				$this->upgrade->logger("Creating Directory - ".DOCROOT."media/uploads/upgrade");
			}
			
			$latest_ushahidi = $this->upgrade->download_ushahidi($url);

			//download was successful
			if ($this->upgrade->success)
			{
				$this->upgrade->write_to_file($latest_ushahidi, $zip_file);
				$this->upgrade->logger("Successfully Downloaded. Unpacking ".$zip_file);
				echo json_encode(array("status"=>"success", "message"=>"Successfully Downloaded. Unpacking..."));
			}
			else
			{
				$this->upgrade->logger("** Failed downloading.\n\n");
				echo json_encode(array("status"=>"error", "message"=>"Failed downloading."));
			}
		}
		
		
		if ($step == 2)
		{
			//extract compressed file
			$this->upgrade->unzip_ushahidi($zip_file, $working_dir);
			
			//extraction was successful
			if ($this->upgrade->success)
			{
				$this->upgrade->logger("Successfully Unpacked. Copying files...");
				echo json_encode(array("status"=>"success", "message"=>"Successfully Unpacked. Copying files..."));
			}
			else
			{
				$this->upgrade->logger("** Failed unpacking.\n\n");
				echo json_encode(array("status"=>"error", "message"=>"Failed unpacking."));
			}
		}


		if ($step == 3)
		{
			//copy files
			$this->upgrade->ftp_recursively($working_dir."ushahidi/",DOCROOT);
			
			//copying was successful
			if ($this->upgrade->success)
			{
				$this->upgrade->logger("Successfully Copied. Upgrading Database...");
				echo json_encode(array("status"=>"success", "message"=>"Successfully Copied. Upgrading Database..."));
			}
			else
			{
				$this->upgrade->logger("** Failed copying files.\n\n");
				echo json_encode(array("status"=>"error", "message"=>"Failed copying files."));
			}
		}
		
		
		// Database BACKUP + UPGRADE
		if ($step == 4)
		{
			// backup database.
			// is gzip enabled ?
			$gzip = Kohana::config('config.output_compression');
			$error = $this->_do_db_backup( $gzip );
			
			if (empty($error))
			{
				if (file_exists($working_dir."/ushahidi/sql"))
				{
					$this->_process_db_upgrade($working_dir."ushahidi/sql/");
				}
				$this->upgrade->logger("Database backup and upgrade successful.");
				echo json_encode(array("status"=>"success", "message"=>"Database backup and upgrade successful."));
			}
			else
			{
				$this->upgrade->logger("** Failed backing up database.\n\n");
				echo json_encode(array("status"=>"error", "message"=>"Failed backing up database."));
			}
		}
		
		
		// Database UPGRADE ONLY
		if ($step == 5)
		{
			if (file_exists($working_dir."ushahidi/sql"))
			{
				//upgrade tables
				$this->_process_db_upgrade($working_dir."ushahidi/sql/");
				$this->upgrade->logger("Database upgrade successful.");
				echo json_encode(array("status"=>"success", "message"=>"Database upgrade successful."));
			}
			else
			{
				$this->upgrade->logger("Database upgrade successful.");
				echo json_encode(array("status"=>"success", "message"=>"Database upgrade successful."));
			}
		}
		
		// Delete downloaded files
		if ($step == 6)
		{
			$this->upgrade->logger("Deleting downloaded files...");
			echo json_encode(array("status"=>"success", "message"=>"Deleting downloaded files..."));
		}
		
		if ($step == 7)
		{
			$this->upgrade->remove_recursively($working_dir);
			$this->upgrade->logger("UPGRADE SUCCESSFUL");
			echo json_encode(array("status"=>"success", "message"=>"UPGRADE SUCCESSFUL. View <a href=\"".url::site(). "admin/upgrade/logfile?f=".$this->session->get('upgrade_session').".txt"."\" target=\"_blank\">Log File</a>"));
			
			$this->session->delete('upgrade_session');
		}
	}
	
	public function logfile() 
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		if (isset($_GET['f']) AND ! empty($_GET['f']))
		{
			$log_file = DOCROOT."application/logs/upgrade_".$_GET['f'];
			$log_file_ext = strrev(substr(strrev($log_file),0,3));
			if (file_exists($log_file) AND $log_file_ext == "txt")
			{
				$contents = file_get_contents($log_file);
				$contents = nl2br($contents);
				echo $contents;
			}
		}
	}
	
	public function check_current_version()
	{
		//This is an AJAX call, so none of this fancy templating, just render the data
		$this->template = "";
		$this->auto_render = FALSE;
		$view = View::factory('admin/current_version');
				
		
		$upgrade = new Upgrade;
		
		//fetch latest release of ushahidi
		$this->release = $upgrade->_fetch_core_release();		
		
		if(!empty($this->release) )
        {
		    $view->version = $this->_get_release_version();
            $view->critical = $this->release->critical;        
        }
     
        $view->render(TRUE);
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
		sort($files);
		foreach ( $files as $file )
		{
			// We're going to try and execute each of the sql files in order
			$file_ext = strrev(substr(strrev($file),0,4));
			if ($file_ext == ".sql")
			{
				$this->upgrade->logger("Database imported ".$dir_path.$file);
				$this->_execute_upgrade_script($dir_path.$file);
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
				
				if ( ! $paths['mysql'])
				{
					$paths['mysql'] = 'mysql';
				}
				
				if ( ! $paths['mysqldump'])
				{
					$paths['mysqldump'] = 'mysqldump';
				}
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
		
		$database = Kohana::config('database');
		
		$backup = array();
		$backup += $mysql_path;
		$backup['user'] = $database['default']['connection']['user'];
		$backup['password'] = $database['default']['connection']['pass'];
		$backup['host'] = $database['default']['connection']['host'];
		$backup['database'] = $database['default']['connection']['database'];
		$backup['date'] = time();
		$backup['filepath'] = preg_replace('/\//', '/', Kohana::config('upload.relative_directory'));
		$backup['filename'] = $backup['filepath'].'/backup_'.$this->session->get('upgrade_session').'.sql';
		
		if ($gzip)
		{
			$backup['filename'] = $backup['filename'].'.gz';
			$command = $mysql_path['mysqldump'].' --host="'.$backup['host'].'" --user="'.$backup['user'].'" --password="'.$backup['password'].'" --add-drop-table --skip-lock-tables '.$backup['database'].' | gzip > '.$backup['filename'];
		} 
		else
		{
			$backup['filename'] = $backup['filename'];
			$command = $mysql_path['mysqldump'].' --host="'.$backup['host'].'" --user="'.$backup['user'].'" --password="'.$backup['password'].'" --add-drop-table --skip-lock-tables '.$backup['database'].' > '.$backup['filename'];
		}
		
		$this->upgrade->logger("Backing up database to ".DOCROOT."media/uploads/".$backup['filename']);
		
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
			if (isset($remote_version[2]) AND (int) $remote_version[2] > 0)
			{
				if ( ! isset($local_version[2]))
				{
					return true;
				}
				elseif( (int) $remote_version[2] > (int) $local_version[2] )
				{
					return true;
				}
			}
		}

		return false;
	}
}
