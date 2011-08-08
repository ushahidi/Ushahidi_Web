<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Backup Hook
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class backup {
	
	private $zipdir;
	private $warehouse;
	private $time;
	private $filename;
	private $filepath;
	private $sendbackupto;
	private $key;
	private $email;
	private $password;
	private $getbackupurlfrom;
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		$this->zipdir = DOCROOT;
		$this->warehouse = PLUGINPATH.'backup/warehouse/';
		$this->time = time();
		$this->filename = 'backup_'.$this->time.'.zip';
		$this->filepath = $this->warehouse.$this->filename;
		$this->sendbackupto = 'http://backup.ushahidi.com/warehouse.php';
		$this->getbackupurlfrom = 'http://backup.ushahidi.com/api.php';
		
		$result = ORM::factory('backup')->find(1);
		$this->key = $result->key;
		$this->email = $result->email;
		$this->password = $result->password;
		
		
		// Hook into routing
		//Event::add('system.pre_controller', array($this, 'check'));
		//Event::add('ushahidi_action.main_footer', array($this, 'go'));
		//Event::add('ushahidi_action.main_footer', array($this, 'restore'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function go()
	{
		// Check if we even have a key before we do this
		if(strlen($this->key) <= 0)
		{
			// No Key so kill the backup
			return FALSE;
		}
		
		// Delete warehouse contents
		$this->rm_warehouse_files();
		
		// Backup Database
		$this->copydb();
		
		// Zip it
		$this->zipdir();
		
		// Send to server
		$this->send_backup();
		
		// Delete warehouse contents
		$this->rm_warehouse_files();
		
		return TRUE;
	}
	
	/*
	* Restore - This operation will overwrite the entire site with a backup
	*/
	public function restore()
	{
		// Get the backup URL
		$backup_url = $this->get_backup_url();
		
		if($backup_url == FALSE)
		{
			return FALSE;
		}
		
		// Delete warehouse contents
		$this->rm_warehouse_files();
		
		// Download file
		$this->download($backup_url);
		
		return TRUE;
	}
	
	public function download($url)
	{
		$filepath = $this->filepath;
		
		set_time_limit(0);
		
		$fp = fopen ($filepath, 'w+');//This is the file where we save the information
		$ch = curl_init($url);//Here is the file we are downloading
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);

	}
	
	public function get_backup_url()
	{
		$url = $this->getbackupurlfrom;
		
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
	    $post = array(
	        'email'=>base64_encode($this->email),
	        'password'=>base64_encode($this->password),
	        'key'=>base64_encode($this->key),
	        'task'=>base64_encode('backup_url')
	    );
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
	    $buffer = curl_exec($ch);
		curl_close($ch);
		
		$result = json_decode($buffer);
		
		if($result == '0')
		{
			return FALSE;
		}
		
		return $result->backup_url;
	}
	
	// Sends the backup to the server
	public function send_backup()
	{
		$url = $this->sendbackupto;
		$filepath = $this->filepath;
		
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
	    // same as <input type="file" name="file_box">
	    $post = array(
	        'file'=>'@'.$filepath,
	        'email'=>base64_encode($this->email),
	        'password'=>base64_encode($this->password),
	        'key'=>base64_encode($this->key)
	    );
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
	    $buffer = curl_exec($ch);
		curl_close($ch);
	}
	
	// Remove files in the warehouse following the rules in the function
	public function rm_warehouse_files()
	{
		$warehouse = $this->warehouse;
	
		$files = glob($warehouse.'backup_*.zip');
		foreach($files as $file) unlink($file);
		
		$files = glob($warehouse.'db_*.sql');
		foreach($files as $file) unlink($file);
	}
	
	// Copies the entire database to a sql file in the backup/warehouse directory
	// Function adapted from http://davidwalsh.name/backup-mysql-database-php
	public function copydb()
	{
		$savetodir = $this->warehouse;
		$time = $this->time;
		
		$db = new Database();
		
		if($time == '') $time = time();
		
		$tables = array();
		$result = $db->query('SHOW TABLES;');
		
		foreach ($result as $row)
		{
			$value = (array)$row;
			$key = key($value);
			$tables[] = $value[$key];
		}
		
		$return = '';
		
		//cycle through
		foreach($tables as $table)
		{
			$result = mysql_query('SELECT * FROM '.$table);
			
			$num_fields = mysql_num_fields($result);
			
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) 
			{
				while($row = mysql_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = str_ireplace("\n","\\n",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j<($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		
		//save file
		$handle = fopen($savetodir.'db_'.$time.'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
	}
	
	// Zips the whole deployment
	public function zipdir()
	{
		$archiveFile = $this->filepath;
		$directory = $this->zipdir;
		
		$ziph = new ZipArchive();
		if(file_exists($archiveFile))
		{
			if($ziph->open($archiveFile, ZIPARCHIVE::CHECKCONS) !== TRUE)
			{
				// echo "BACKUP PLUGIN ERROR: Unable to Open $archiveFile<br/>";
				return false;
			}
		}else{
			if($ziph->open($archiveFile, ZIPARCHIVE::CM_PKWARE_IMPLODE) !== TRUE)
			{
				// echo "BACKUP PLUGIN ERROR: Could not Create $archiveFile<br/>";
				return false;
			}
		}
		
		$dir_structure = $this->getdir(rtrim($directory,'/'));
		foreach($dir_structure as $d => $f_arr)
		{
			foreach($f_arr as $f)
			{
				$zip_this_file = $d.'/'.$f;
				$name_in_zip = str_ireplace(DOCROOT,'',$zip_this_file);
				if(!$ziph->addFile($zip_this_file,$name_in_zip))
				{
					// echo "BACKUP PLUGIN ERROR: Error archiving $zip_this_file in $archiveFile<br/>";
					return false;
				}
			}
		}

		$ziph->close();
		
		return true;
	}
	
	// Returns an array of the directory listing with dir as key and files as an array attached to the key (1 dimensional array)
	// Function adapted from conversations on http://codingforums.com/showthread.php?t=71882
	function getdir($path = '.')
	{
		$ignore = array( 'cgi-bin', '.', '..', '.git', 'cache', '.DS_Store', '.gitignore', 'logs' );
		$dirTree = array ();
		$dirTreeTemp = array ();
		$ignore[] = '.';
		$ignore[] = '..';
		
		$dh = @opendir($path);
		
		while (false !== ($file = readdir($dh)))
		{
			if (!in_array($file, $ignore))
			{
				if (!is_dir("$path/$file"))
				{
					$dirTree["$path"][] = $file;
				}else{
					$dirTreeTemp = $this->getdir("$path/$file");
					
					if (is_array($dirTreeTemp))
					{
						$dirTree = array_merge($dirTree, $dirTreeTemp);
					}
				}
			}
		}
		
		closedir($dh);
		
		return $dirTree;
	}
}

new backup;