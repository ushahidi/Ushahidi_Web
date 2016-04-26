<?php defined('SYSPATH') or die('No direct script access.');
/**
* Model for Actionable
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Actionable Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Actionable_Model extends ORM
{
	protected $belongs_to = array('incident');
	
	// Database table name
	protected $table_name = 'actionable';
	
	public function status() {
		if ($this->actionable)
		{
			if ($this->action_taken)
			{
				return Kohana::lang('actionable.action_taken');
			}
			elseif ($this->actionable == 2)
			{
				return Kohana::lang('actionable.urgent');
			}
			else
			{
				return Kohana::lang('actionable.actionable');
			}
		}
		else
		{
			return Kohana::lang('actionable.not_actionable');
		}
	}
	
	public function color() {
		if ($this->actionable)
		{
			if ($this->action_taken)
			{
				return '33ff33';
			}
			elseif ($this->actionable == 2)
			{
				return 'ff0000';
			}
			else
			{
				return 'ffaa00';
			}
		}
		else
		{
			return 'aaaaaa';
		}
	}
}
