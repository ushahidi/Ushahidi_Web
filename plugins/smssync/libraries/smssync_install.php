<?php
/**
 * Performs install/uninstall methods for the SMSSync plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   SMSSync Installer
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Smssync_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db = Database::instance();
	}

	/**
	 * Creates the required database tables for the smssync plugin
	 */
	public function run_install()
	{
		// Create the database tables.
		// Also include table_prefix in name
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."smssync_settings` (
				id int(11) unsigned NOT NULL AUTO_INCREMENT,
				smssync_secret varchar(100) DEFAULT NULL,
				PRIMARY KEY (`id`)
			);
		");
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."smssync_message` (
				id int(11) unsigned NOT NULL AUTO_INCREMENT,
				smssync_to varchar(100) DEFAULT NULL,
				smssync_from varchar(100) DEFAULT NULL,
				smssync_message text,
				smssync_message_date datetime DEFAULT NULL,
				smssync_sent tinyint(4) NOT NULL DEFAULT '0',
				smssync_sent_date datetime DEFAULT NULL,
				PRIMARY KEY (id)
			);
		");
	}

	/**
	 * Deletes the database tables for the actionable module
	 */
	public function uninstall()
	{
		$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'smssync_settings`');
		$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'smssync_message`');
	}
}