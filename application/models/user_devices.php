<?php

/**
* Model for User Devices
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
 * $Id: $
 */
class User_Devices_Model extends ORM {

	protected $table_name = 'user_devices';
	
	public function device_registered($mobileid)
	{	
		if(ORM::factory('user_devices', $mobileid)->loaded) return TRUE;
		
		return FALSE;
	}
	
	public function register_device($mobileid,$userid=0)
	{	
		$device = ORM::factory('user_devices');
		$device->id = $mobileid;
		$device->user_id = $userid;
		$device->save();
	}
	
	public function device_owner($mobileid)
	{
		$device = ORM::factory('user_devices')->find($mobileid);
		return $device->user_id;
	}
}
