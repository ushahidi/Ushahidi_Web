<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for MHI users
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Incident Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class mhi_user_Model extends ORM
{
	protected $table_name = 'mhi_users';
	
	protected $primary_key = 'id';
	
	protected $primary_val = 'email';
	
	// $a should be an assoc array including email, firstname, lastname and password (plain text)
	static function save_user($a)
	{
		// Check if MHI user already exists
		$count = ORM::factory('mhi_user')->where('email',$a['email'])->count_all();
		if($count != 0){
			throw new Kohana_User_Exception('DB Entry Error', "Email address for MHI user already exists. Pole sana.");
		}
		
		$salt = Kohana::config('auth.salt_pattern');
		
		$mhi_user = ORM::factory('mhi_user');
		$mhi_user->firstname = $a['firstname'];
		$mhi_user->lastname = $a['lastname'];
		$mhi_user->email = $a['email'];
		$mhi_user->password = sha1($a['password'].$salt);
		//$mhi_user->password = Auth::instance()->hash_password($a['password']);
		$mhi_user->save();
		
		$result = ORM::factory('mhi_user')->where('email',$a['email'])->find_all();
		$id = 0;
		foreach($result as $res){
			$id = $res->id;
		}
		
		return $id;
	}
}
