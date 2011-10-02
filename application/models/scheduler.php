<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Scheduler
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

class Scheduler_Model extends ORM
{
	protected $has_many = array('scheduler_log');
	
	// Database table name
	protected $table_name = 'scheduler';
	
	/*
	* Get Scheduler JS for inclusion in footer
	*/
	static function get_javascript()
	{
		$tag = '<!-- Task Scheduler --><script type="text/javascript">$(document).ready(function(){$(\'#schedulerholder\').html(\'<img src="<?php echo url::base(); ?>scheduler" />\');});</script><div id="schedulerholder"></div><!-- End Task Scheduler -->';
		
		return $tag;
	}
}
