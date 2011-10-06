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
	
	/**
	 * Custom validation for this model - complements the default validate()
	 *
	 * @return bool TRUE if validation succeeds, FALSE otherwise
	 */
	public static function custom_validate(array & $post)
	{
		// Initalize validation
		$post = Validation::factory($post)
				->pre_filter('trim', TRUE);
		
		$post->add_rules('username','required','length[3,16]', 'alpha_numeric');
		$post->add_rules('name','required','length[3,100]');
        $post->add_rules('email','required','email','length[4,64]');
		
		// If user id is not specified, check if the username already exists
		if (empty($post->user_id))
		{
			$post->add_callbacks('username', array('User_Model', 'unique_value_exists'));
			$post->add_callbacks('email', array('User_Model', 'unique_value_exists'));
		}
		
		// Only check for the password if the user id has been specified
		if ( ! empty($post->user_id))
		{
			$post->add_rules('password','required', 'length[5,50]','alpha_numeric');
		}
		
		// If Password field is not blank
        if ( ! empty($post->password))
        {
            $post->add_rules('password','required','length[5,50]', 'alpha_numeric', 'matches[password_again]');
        }
        
		$post->add_rules('role','required','length[3,30]', 'alpha_numeric');
		$post->add_rules('notify','between[0,1]');
		
		// Additional validation checks
		Event::run('ushahidi_action.user_submit_admin', $post);
				
		// Return
		return $post->validate();
	}
	

	/**
	 * Checks if the value in the specified field exists in database
	 */
	public static function unique_value_exists(Validation $post, $field)
	{
		$exists = (bool) ORM::factory('user')->where($field, $post[$field])->count_all();
		if ($exists)
		{
			$post->add_error($field, 'exists');
		}
	}
	
} // End User_Model
