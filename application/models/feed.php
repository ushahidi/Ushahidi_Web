<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for RSS Feeds
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

class Feed_Model extends ORM
{
	/**
	 * One-to-many relationship definition
	 * @var array
	 */
	protected $has_many = array('feed_item');
	
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'feed';
	
	/**
	 * Validates and optionally saves a new feed record from an array
	 *
	 * @param array $array Values to check
	 * @param bool $save Saves the record when validation succeeds
	 * @return bool
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Instantiate validation
		$array = Validation::factory($array)
				->pre_filter('trim', TRUE)
				->add_rules('feed_name','required', 'length[3,70]')
				->add_rules('feed_url','required', 'url');
		
		return parent::validate($array, $save);
	}
	
	/**
	 * Checks if the specified feed exists in the database
	 *
	 * @param int $feed_id Database record ID of the feed to check
	 * @return bool
	 */
	public static function is_valid_feed($feed_id)
	{
		return (intval($feed_id) > 0)
			? self::factory('feed', intval($feed_id))->loaded
			: FALSE;
	}
}
