<?php
/**
 * Upgrading  Library
 * Provides the necessary functions to do the automatic upgrade
 * 
 * @package    Sharing
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
 
 class Upgrade {
 	private $notices;
	private $errors;
 	
 	public function __construct() {
 		$this->notices = array();
 		$this->errors = array();
 	}
 	
 	/**
 	 * Fetches ushahidi from download.ushahidi.com
 	 * 
 	 * @param String url-- download URL
 	 */
 	public function download_ushahidi() {
 		
 	}
 	
 	/**
 	 * Copy files recursively. 
 	 * 
 	 * @param String srcdir-- the source directory.
 	 * @param String destdir -- the destination directory.
 	 */
 	public function copy_recursively() {
 		
 	}
 	
 	/**
 	 * Remove files recursively.
 	 * 
 	 * @param String dir-- the directory to delete.
 	 */
 	public function remove_recursively($dir) {
 		
 	}
 	
 	/**
 	 * Unzip the file.
 	 * 
 	 * @param String zip_file-- the zip file to be extracted.
 	 * @param String destdir-- destination directory
 	 */
 	
 	public function unzip_ushahidi($zip_file, $destdir) {
 		
 	} 
 	
 	/**
 	 * Write the zile file to a file.
 	 * 
 	 */
 	public function write_to_file() {
 		
 	}
 }
?>
