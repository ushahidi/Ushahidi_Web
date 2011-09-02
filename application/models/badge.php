<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Badge
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

class Badge_Model extends ORM
{
	// Database table name
	protected $table_name = 'badge';

	protected $primary_key = 'id';

	protected $has_one = array('media');

	protected $has_and_belongs_to_many = array('users');

	/**
	 * Returns all details for all badges, including images and user information
	 * @return array
	 */
	public static function badges()
	{
		$badges = ORM::factory('badge')->find_all();
		$arr = array();
		foreach($badges as $badge){
			$arr[$badge->id] = array('id'=>$badge->id,
									'name'=>$badge->name,
									'description'=>$badge->description,
									'img'=>$badge->media->media_link,
									'img_m'=>$badge->media->media_medium,
									'img_t'=>$badge->media->media_thumb,
									'users'=>array());
			foreach($badge->users as $user)
			{
				$arr[$badge->id]['users'][$user->id] = $user->username;
			}

			asort($arr[$badge->id]['users']);
		}

		return $arr;
	}

	/**
	 * Returns a simple array of badge names with badge id as the array key
	 * @return array
	 */
	public static function badge_names()
	{
		$badges = ORM::factory('badge')->select_list('id','name');
		return $badges;
	}

	/**
	 * Returns an array of badges for a specific user
	 * @return array
	 */
	public static function users_badges($user_id)
	{
		// Get assigned badge ids
		$assigned_badges = ORM::factory('badge_user')->where(array('user_id'=>$user_id))->find_all();
		$assigned = array();
		foreach($assigned_badges as $assigned_badge)
		{
			$assigned[] = $assigned_badge->badge_id;
		}
		
		$arr = array();
		if(count($assigned) > 0)
		{
			// Get badges with those ids
			$badges = ORM::factory('badge')->in('id', $assigned)->find_all();
			foreach($badges as $badge)
			{
				$arr[$badge->id] = array('id'=>$badge->id,
										'name'=>$badge->name,
										'description'=>$badge->description,
										'img'=>$badge->media->media_link,
										'img_m'=>$badge->media->media_medium,
										'img_t'=>$badge->media->media_thumb);
			}
		}

		return $arr;
	}
}
