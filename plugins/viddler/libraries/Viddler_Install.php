<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Performs install/uninstall methods for the Viddler Plugin
 *
 * @package    Ushahidi
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Viddler_Install {
	
	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db = new Database();
	}

	/**
	 * Creates the required tables
	 */
	public function run_install()
	{
		
		// ****************************************
		// DATABASE STUFF
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."viddler` (
			  `viddler_id` varchar(16) NOT NULL,
			  `incident_id` int(11) DEFAULT NULL,
			  `checkin_id` int(11) DEFAULT NULL,
			  `url` varchar(255) DEFAULT NULL,
			  `embed` text,
			  `embed_small` text,
			  PRIMARY KEY (`viddler_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
		// ****************************************
	}

	/**
	 * Drops the table
	 */
	public function uninstall()
	{
		$this->db->query("DROP TABLE `".Kohana::config('database.default.table_prefix')."viddler`;");
	}
}