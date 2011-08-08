<?php
/**
 * Performs install/uninstall methods for the Backup plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Backup Installer
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Backup_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db = Database::instance();
	}

	/**
	 * Creates the required database tables for the Growl plugin
	 */
	public function run_install()
	{
		// Create the database tables.
		// Also include table_prefix in name
		$this->db->query('DROP TABLE IF EXISTS `'.Kohana::config('database.default.table_prefix').'backup`');
		
		$this->db->query('CREATE TABLE `'.Kohana::config('database.default.table_prefix').'backup` (
							  `id` tinyint(4) NOT NULL,
							  `email` varchar(100) NOT NULL,
							  `password` varchar(128) NOT NULL,
							  `key` varchar(50) NOT NULL,
							  PRIMARY KEY (`id`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
							
		
		// Create settings row
		$this->db->query('INSERT INTO `'.Kohana::config('database.default.table_prefix').'backup` (`id`) VALUES (1)');
		
		// Set up scheduler
		$this->db->query('INSERT INTO `'.Kohana::config('database.default.table_prefix').'scheduler` (`scheduler_name`,`scheduler_controller`,`scheduler_hour`,`scheduler_active`) VALUES (\'Backup\',\'s_backup\',\'0\',\'1\')');
	}

	/**
	 * Deletes the database tables for the Growl module
	 */
	public function uninstall()
	{
		// Drop backup table
		$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'backup`');
		
		// Remove from scheduler
		$this->db->query('DELETE FROM `'.Kohana::config('database.default.table_prefix').'scheduler` WHERE `scheduler_controller` = \'s_backup\'');
	}
}