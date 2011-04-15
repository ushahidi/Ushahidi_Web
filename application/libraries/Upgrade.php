<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Upgrading  Library
 * Provides the necessary functions to do the automatic upgrade
 * 
 * @package	   Upgrade
 * @author	   Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
 */
 
 class Upgrade 
{
	
	public $notices;
	public $errors;
	public $success;
	public $error_level;
	public $session;
	public $ftp;
	public $ftp_server;
	public $ftp_user_name;
	public $ftp_user_pass;
	
	public function __construct() 
	{
		$this->log = array();
		$this->errors = array();
		$this->error_level = ini_get('error_reporting');
		$this->session = Session::instance();
		
		if ( ! $this->session->get('upgrade_session'))
		{
			$this->session->set('upgrade_session', date("Y_m_d-H_i_s"));
		}
	}
	
	/**
	 * Fetches ushahidi from download.ushahidi.com
	 * 
	 * @param String url-- download URL
	 */
	public function download_ushahidi($url) {
		$snoopy = new Snoopy();
		$snoopy->agent = Kohana::lang('libraries.upgrade_title');
		$snoopy->read_timeout = 30;
		$snoopy->gzip = false;
		$snoopy->fetch($url);
		$this->log[] = "Starting to download the latest ushahidi build...";
		
		if ( $snoopy->status == '200' ) 
		{
			$this->log[] = "Download of latest ushahidi went successful.";
			$this->success = true;		  
			return $snoopy->results;
		} 
		
		else 
		{			
			$this->errors[] = sprintf(Kohana::lang('libraries.upgrade_failed').": %d", $snoopy->status);	
			$this->success = false;
			return $snoopy;
		}
			
	}
	
	
	/**
	 * FTP files recursively. 
	 * 
	 * @param String source-- the source directory.
	 * @param String dest -- the destination directory.
	 * @param $options //folderPermission,filePermission
	 * @return boolean 
	 */
	function ftp_recursively($source, $dest, $options=array('folderPermission'=>0775,'filePermission'=>0664))
	{		
		if ( ! $this->ftp_connect() )
		{
			$this->success = false;
			return false;
		}
		
		if ( ! $ftp_base_path = $this->ftp_base_path())
		{
			$this->success = false;
			return false;
		}
		
		$this->ftp->chdir($ftp_base_path);
		
		if (is_file($source))
		{	
			$__dest=$dest;
			
			// Turn off error reporting temporarily
			error_reporting(0);
			$result = $this->ftp_copy($source, $__dest, $options);
			if ($result)
			{
				$this->success = true;
				$this->logger("Copied to ".$__dest);
				//Turn on error reporting again
				error_reporting($this->error_level);
			}
			else
			{
				$this->success = false;
				$this->logger("** Failed writing ".$__dest);
				//Turn on error reporting again
				error_reporting($this->error_level);
				return false;
			}

		}
		elseif(is_dir($source))
		{
			if ($dest[strlen($dest)-1] == '/')
			{
				if ($source[strlen($source)-1] == '/')
				{
					//Copy only contents
				}
				else
				{
					//Change parent itself and its contents
					$dest = $dest.basename($source);
					if ( ! $this->ftp->mkdir(str_replace(DOCROOT,"",$dest)))
					{
						$this->logger("** Failed creating directory ".$dest.". It might already exist.");
					}
					else
					{
						$this->logger("Created directory ".$dest);
					}
					$this->ftp->chmod(str_replace(DOCROOT,"",$dest),$options['folderPermission']);
				}
			}
			else
			{	
				if ($source[strlen($source)-1] == '/')
				{
					//Copy parent directory with new name and all its content
					if ( ! $this->ftp->mkdir(str_replace(DOCROOT,"",$dest)))
					{
						$this->logger("** Failed creating directory ".$dest.". It might already exist.");
					}
					else
					{
						$this->logger("Created directory ".$dest);
					}
					$this->ftp->chmod(str_replace(DOCROOT,"",$dest),$options['folderPermission']);
				}
				else
				{
					//Copy parent directory with new name and all its content
					if ( ! $this->ftp->mkdir(str_replace(DOCROOT,"",$dest)))
					{
						$this->logger("** Failed creating directory ".$dest.". It might already exist.");
					}
					else
					{
						$this->logger("Created directory ".$dest);
					}
					$this->ftp->chmod(str_replace(DOCROOT,"",$dest),$options['folderPermission']);
				}
			}
			
			
			$dirHandle=opendir($source);
			while($file=readdir($dirHandle))
			{
				if($file != "." AND $file != ".." AND substr($file, 0, 1) != '.')
				{
					if( ! is_dir($source."/".$file))
					{
						$__dest=$dest."/".$file;
						$__dest=str_replace("//", "/", $__dest);
					}
					else
					{
						$__dest=$dest."/".$file;
						$__dest=str_replace("//", "/", $__dest);
					}
					$source_file = $source."/".$file;
					$source_file = str_replace("//", "/", $source_file);
					$result = $this->ftp_recursively($source_file, $__dest, $options);
				}
			}
			closedir($dirHandle);
		}
	}
	
	
	function ftp_connect()
	{
		//** temporary
		$this->ftp_server = $this->session->get('ftp_server');
		$this->ftp_user_name = $this->session->get('ftp_user_name');
		$this->ftp_user_pass = $this->session->get('ftp_user_pass');
		
		// Turn off error reporting temporarily
		//error_reporting(0);
		
		$ftp_init = new PemFtp();
		$this->ftp = new ftp();
		
		if ( ! $this->ftp_server OR
			 ! $this->ftp_user_name OR
			 ! $this->ftp_user_pass)
		{
			// Failed Connecting
			$this->logger("** Can't connect to FTP Server. Server, Username and/or Password not specified.");
			error_reporting($this->error_level);
			return false;
		}

		// Set FTP Server
		if ( ! $this->ftp->SetServer($this->ftp_server))
		{
			// Failed Connecting
			$this->logger("** Can't connect to FTP Server");
			error_reporting($this->error_level);
			return false;
		}
		
		// Connect to FTP Server
		if ( ! $this->ftp->connect() )
		{
			// Failed Connecting
			$this->logger("** Can't connect to FTP Server");
			error_reporting($this->error_level);
			return false;
		}
		
		// Authenticate at FTP Server
		if ( ! $this->ftp->login($this->ftp_user_name, $this->ftp_user_pass) )
		{
			// Failed Connecting
			$this->logger("** Can't connect to FTP Server - Incorrect Username or Password");
			error_reporting($this->error_level);
			return false;
		}
		
		$this->ftp->setTimeout(30);
		$this->ftp->SetType(-1);
		$this->ftp->Passive(true);
		
		error_reporting($this->error_level);
		
		return true;
	}
	
	function ftp_base_path()
	{
		$absolute_parts = explode('/', DOCROOT);
		$ushahidi_folder = end($absolute_parts);
		$ftp_parts = $this->ftp->nlist();
		
		// Are we already in the Ushahidi directory?
		if ($this->ftp->is_exists("application/config/config.php"))
		{
			return "./";
		}
		
		// We'll cycle through both to find out which
		// part of the DOCROOT we're in
		$ftp_base = "";
		foreach ($absolute_parts as $part)
		{
			foreach ($ftp_parts as $key => $value)
			{
				if ($value == $part)
				{
					$ftp_base .= $value."/";
					if ($this->ftp->is_exists($ftp_base."application/config/config.php"))
					{ // We've arrived at the right folder
						break;
					}
				}
			}
		}
		
		// Verify once again that we're in the right directory
		if ($this->ftp->is_exists($ftp_base."application/config/config.php"))
		{ // We've arrived at the right folder
			return $ftp_base;
		}
		else
		{
			return false;
		}
	}
	
	function ftp_copy($source, $dest, $options)
	{
		$dest = str_replace(DOCROOT,"",$dest);
		
		if ( ! $this->ftp->put($source, $dest) )
		{
			return false;			
		}
		
		$this->ftp->chmod($dest,$options['filePermission']);
		
		return true;
	}
	
	
	/**
	 * Copy files recursively. 
	 * 
	 * @param String source-- the source directory.
	 * @param String dest -- the destination directory.
	 * @param $options //folderPermission,filePermission
	 * @return boolean 
	 */
	function copy_recursively($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755))
	{				
		if (is_file($source)) {
			if ($dest[strlen($dest)-1]=='/')
			{
				if (!file_exists($dest))
				{
					cmfcDirectory::makeAll($dest,$options['folderPermission'],true);
				}
				$__dest = $dest."/".basename($source);
			}
			else
			{
				$__dest=$dest;
			}
			// Turn off error reporting temporarily
			error_reporting(0);
			$result = copy($source, $__dest);
			if ($result)
			{
				chmod($__dest,$options['filePermission']);
				$this->success = true;
				$this->logger("Copied to ".$__dest);
				//Turn on error reporting again
				error_reporting($this->error_level);
			}
			else
			{
				$this->success = false;
				$this->logger("** Failed writing ".$__dest);
				//Turn on error reporting again
				error_reporting($this->error_level);
				return false;
			}

		}
		elseif(is_dir($source))
		{
			if ($dest[strlen($dest)-1] == '/')
			{
				if ($source[strlen($source)-1] == '/')
				{
					//Copy only contents
				}
				else
				{
					//Change parent itself and its contents
					$dest = $dest.basename($source);
					if ( ! is_writable($dest))
					{
						$this->success = false;
						$this->logger("** Can't write to ".$dest);
						return false;
					}
					@mkdir($dest);
					chmod($dest,$options['filePermission']);
				}
			}
			else
			{
				if ( ! is_writable($dest))
				{
					$this->success = false;
					$this->logger("** Can't write to ".$dest);
					return false;
				}
				
				if ($source[strlen($source)-1] == '/')
				{
					//Copy parent directory with new name and all its content
					@mkdir($dest,$options['folderPermission']);
					chmod($dest,$options['filePermission']);
				}
				else
				{
					//Copy parent directory with new name and all its content
					@mkdir($dest,$options['folderPermission']);
					chmod($dest,$options['filePermission']);
				}
			}

			$dirHandle=opendir($source);
			while($file=readdir($dirHandle))
			{
				if($file!="." AND $file!=".." AND substr($file, 0, 1) != '.')
				{
					if(!is_dir($source."/".$file))
					{
						$__dest=$dest."/".$file;
					}
					else
					{
						$__dest=$dest."/".$file;
					}
					//echo "$source/$file ||| $__dest<br />";
					if ( ! is_writable($__dest))
					{
						$this->success = false;
						$this->logger("** Can't write to - ".$__dest);
						return false;
					}
					$result = $this->copy_recursively($source."/".$file, $__dest, $options);
				}
			}
			closedir($dirHandle);
		}
	}
	
	
	/**
	 * Remove files recursively.
	 * 
	 * @param String dir-- the directory to delete.
	 */
	public function remove_recursively($dir) 
	{
		if (empty($dir) || !is_dir($dir))
			return false;
		if (substr($dir,-1) != "/")
			$dir .= "/";
		if (($dh = opendir($dir)) !== false) {
			while (($entry = readdir($dh)) !== false) {
			if ($entry != "." && $entry != "..") {
				if ( is_file($dir . $entry) ) {
				if ( !@unlink($dir . $entry) ) {
					$this->errors[] = sprintf(Kohana::lang('libraries.upgrade_file_not_deleted'), $dir.$entry );
					$this->success = false;
				}
			} elseif (is_dir($dir . $entry)) {
				$this->remove_recursively($dir . $entry);
				$this->success = true;
			}
			}
		}
		closedir($dh);
		if ( !@rmdir($dir) ) {
			$this->errors[] = sprintf(Kohana::lang('libraries.upgrade_directory_not_deleted'), $dir.$entry);
			$this->success = false;
		}
			$this->success = true;
			return true;
		}
		return false;
		
	}
	
	/**
	 * Unzip the file.
	 * 
	 * @param String zip_file-- the zip file to be extracted.
	 * @param String destdir-- destination directory
	 */
	
	public function unzip_ushahidi($zip_file, $destdir) 
	{
		$archive = new Pclzip($zip_file);
		$this->log[] = sprintf("Unpacking %s ",$zip_file);
		
		if (@$archive->extract(PCLZIP_OPT_PATH, $destdir) == 0)
		{
			$this->errors[] = sprintf(Kohana::lang('libraries.upgrade_extracting_error'),$archive->errorInfo(true) ) ;
			return false;
		}
		
		$this->log[] = sprintf("Unpacking went successful");
		$this->success = true;
		return true;
	} 
	
	/**
	 * Write the zile file to a file.
	 * 
	 * @param String zip_file-- the zip file to be written.
	 * @param String dest_file-- the file to write.
	 */
	public function write_to_file($zip_file, $dest_file) 
	{
		$handler = fopen( $dest_file,'w');
		$fwritten = fwrite($handler,$zip_file);
		$this->log[] = sprintf("Writting to a file ");
		if( !$fwritten ) {
			$this->errors[] = sprintf(Kohana::lang('libraries.upgrade_zip_error'),$dest_file);
			$this->success = false;
			return false;
		}
		fclose($handler);
		$this->success = true;
		$this->log[] = sprintf("Zip file successfully written to a file ");
		return true;
	}

	/**
	 * Fetch latest ushahidi version from a remote instance then 
	 * compare it with local instance version number.
	 */
	public function _fetch_core_release() 
	{
		// Current Version
		$current = urlencode(Kohana::config('settings.ushahidi_version'));

		// Extra Stats
		$url = urlencode(preg_replace("/^https?:\/\/(.+)$/i","\\1", 
					url::base()));
		$ip_address = (isset($_SERVER['REMOTE_ADDR'])) ?
			urlencode($_SERVER['REMOTE_ADDR']) : "";

		$version_url = "http://version.ushahidi.com/2/?v=".$current.
			"&u=".$url."&ip=".$ip_address;		
		
		$version_json_string = @file_get_contents($version_url);
		
		// If we didn't get anything back...
		if ( ! $version_json_string )
		{
			 return "";
		}

		$version_details = json_decode($version_json_string);
		
		return $version_details;
	}
	
	/**
	 * Log Messages To File
	 */
	public function logger($message)
	{
		$message = date("Y-m-d H:i:s")." : ".$message;
		$message .= "\n";
		$logfile = DOCROOT."application/logs/upgrade_".$this->session->get('upgrade_session').".txt";
		$logfile = fopen($logfile, 'a+');
		fwrite($logfile, $message);
		fclose($logfile);
	}	
 }
?>
