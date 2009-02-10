<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model to record scheduler actions
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Scheduler Log Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Scheduler_Log_Model extends ORM
{
	protected $belongs_to = array('scheduler');
	
	// Database table name
	protected $table_name = 'scheduler_log';
}
