<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Checkins
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Checkin_Model extends ORM
{	
	protected $belongs_to = array('user');
	protected $has_many = array('media');
	protected $has_one = array('location','user');

	// Database table name
	protected $table_name = 'checkin';
}
