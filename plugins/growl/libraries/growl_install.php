<?php
/**
 * Performs install/uninstall methods for the Growl plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Growl Installer
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Growl_Install {

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
		$this->db->query('CREATE TABLE `'.Kohana::config('database.default.table_prefix').'growl` (
								`id` tinyint(4) NOT NULL,
								`ips` text CHARACTER SET latin1 NOT NULL,
								`passwords` text CHARACTER SET latin1 NOT NULL,
								PRIMARY KEY (`id`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
		
		// Create settings row
		$this->db->query('INSERT INTO `'.Kohana::config('database.default.table_prefix').'growl` (`id`) VALUES (1)');
	}

	/**
	 * Deletes the database tables for the Growl module
	 */
	public function uninstall()
	{
		$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'growl`');
	}
}