<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Performs install/uninstall methods for the Clickatell Plugin
 *
 * @package    Ushahidi
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Clickatell_Install {
	
	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db =  new Database();
	}

	/**
	 * Creates the required columns for the Clickatell Plugin
	 */
	public function run_install()
	{
		
		// ****************************************
		// DATABASE STUFF
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."clickatell`
			(
				id int(11) unsigned NOT NULL AUTO_INCREMENT,
				clickatell_key varchar(100) DEFAULT NULL,
				clickatell_api varchar(100) DEFAULT NULL,
				clickatell_username varchar(100) DEFAULT NULL,
				clickatell_password varchar(100) DEFAULT NULL,
				PRIMARY KEY (`id`)
			);
		");
		// ****************************************
	}

	/**
	 * Drops the Clickatell Tables
	 */
	public function uninstall()
	{
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."clickatell;
			");
	}
}