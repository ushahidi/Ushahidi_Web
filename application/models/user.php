<?php
/**
 * Model for users for the Auth Module
 *
 * $Id: user.php 3352 2008-08-18 09:43:56BST atomless $
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

class User_Model extends Auth_User_Model {

	protected $has_many = array('alert', 'comment', 'openid', 'private_message', 'user');

	/**
	 * Gets the email address of a user
	 * @return string
	 */
	public static function get_email($user_id)
	{
		$user = ORM::factory('user')->find($user_id);
		return $user->email;
	}

	/**
	 * Returns data for a user based on username
	 * @return object
	 */
	public static function get_user_by_username($username)
	{
		$user = ORM::factory('user')->where(array('username'=>$username))->find();
		return $user;
	}

	/**
	 * Returns all users with public profiles
	 * @return object
	 */
	public static function get_public_users()
	{
		$users = ORM::factory('user')->where(array('public_profile'=>1))->find_all();
		return $users;
	}

} // End User_Model
