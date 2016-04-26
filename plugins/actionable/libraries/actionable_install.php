<?php
/**
 * Performs install/uninstall methods for the actionable plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Actionable Installer
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Actionable_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db = Database::instance();
	}

	/**
	 * Creates the required database tables for the actionable plugin
	 */
	public function run_install()
	{
		// Create the database tables.
		// Also include table_prefix in name
		$this->db->query('
			CREATE TABLE IF NOT EXISTS `'.Kohana::config('database.default.table_prefix').'actionable` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `incident_id` int(11) NOT NULL COMMENT \'incident_id of the new report that is created\',
				  `actionable` tinyint(4) NOT NULL DEFAULT \'0\' COMMENT \'Is a report actionable? 0=Unactionable 1=Actionable 2=Actionable+Urgent\',
				  `action_taken` tinyint(4) NOT NULL DEFAULT \'0\' COMMENT \'Has an action been taken yet?\',
				  `action_summary` varchar(255) DEFAULT NULL COMMENT \'What action was taken\',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
	}

	/**
	 * Deletes the database tables for the actionable module
	 */
	public function uninstall()
	{
		$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'actionable`;');
	}
  
}