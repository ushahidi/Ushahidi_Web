<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Settings
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

class Settings_Model extends ORM {

	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'settings';
	
	// Prevents cached items from being reloaded
	protected $reload_on_wakeup   = FALSE;

	/**
	 * Check if settings table is still using old schema
	 * @return bool
	 */
	protected static function new_schema($force = FALSE)
	{
		return array_key_exists('key', ORM::factory('settings')->reload_columns($force)->table_columns);
	}
	
	/**
	 * Given the setting identifier, returns its value. If the identifier
	 * is non-existed, a NULL value is returned
	 *
	 * @param string $key UniqueID of the settings item
	 *
	 * @return string
	 */
	public static function get_setting($key)
	{
		if (self::new_schema())
		{
			$setting = ORM::factory('settings')->where('key', $key)->find();
			return ($setting->loaded) ? $setting->value : NULL;
		}
		else
		{
			$setting = Database::instance()->query('SELECT * FROM `'.Kohana::config('database.default.table_prefix').'settings` LIMIT 1', $key)->current();
			return isset($setting->$key) ? $setting->$key : NULL;
		}
	}

	/**
	 * Convenience method for the settings ORM when not loaded
	 * with a specific settings value
	 * @return string
	 */
	public function get($key)
	{
		return self::get_setting($key);
	}

	/**
	 * Convenience method to save a single setting value
	 *
	 * @param string key Unique ID of the setting
	 * @param string value Value for the setting item
	 */
	public static function save_setting($key, $value)
	{
		if (self::new_schema())
		{
			$setting = ORM::factory('settings')->where('key', $key)->find();
			
			$setting->key = $key;
			$setting->value = $value;
			$setting->save();
		}
		else
		{
			$settings = ORM::factory('settings', 1);
			$settings->$key = $value;
			$settings->save();
		}
	}

	/**
	 * Returns a key=>value array of the unique setting identifier
	 * and its corresponding value
	 *
	 * @return array
	 */
	public static function get_array()
	{
		if (self::new_schema())
		{
			$all_settings = ORM::factory('settings')->find_all();
			$settings = array();
			foreach ($all_settings as $setting)
			{
				$settings[$setting->key] = $setting->value;
			}
		}
		else
		{
			$settings = ORM::factory('settings', 1)->as_array();
		}

		return $settings;
	}

	/**
	 * Given a validation object, updates the settings table
	 * with the values assigned to its properties
	 *
	 * @param Validation $settings Validation object
	 */
	public static function save_all(Validation $settings)
	{
		// For old schema throw error
		if (! self::new_schema())
			throw new Kohana_User_Exception('Settings database schema out of date');
		
		// Get all the settings
		$all_settings = self::get_array();

		// Settings update query
		$query = sprintf("UPDATE `%ssettings` SET `value` = CASE `key` ", 
		    Kohana::config('database.default.table_prefix'));

		// Used for building the query clauses for the final query
		$values = array();
		$keys = array();
		
		// Modification date
		$settings['date_modify'] = date("Y-m-d H:i:s",time());
		
		// List of value to skip
		$skip = array('api_live');
		$value_expr = new Database_Expression("WHEN :key THEN :value ");
		foreach ($settings as $key => $value)
		{
			// If an item has been marked for skipping or is a 
			// non-existent setting, skip current iteration
			if (in_array($key, $skip) OR empty($key) OR ! array_key_exists($key, $all_settings))
				continue;

			// Check for the timezone
			if ($key === 'timezone' AND $value == 0)
			{
				$value = NULL;
			}
			
			$value_expr->param(':key', $key);
			$value_expr->param(':value', $value);

			$keys[] = $key;
			$values[] = $value_expr->compile();
		}
		
		// Construct the final query
		$query .= implode(" ", $values)."END WHERE `key` IN :keys";

		// Performa batch update
		Database::instance()->query($query, array(':keys' => $keys));
	}


	/**
	 * Given an array of settings identifiers (unique values in the 'key' column),
	 * returns a key => value array of the identifiers with their corresponding values
	 * An exception is thrown if the parameter is not an array or is an empty
	 * array
	 *
	 * @param    array $keys 
	 * @return   array
	 */
	public function get_settings($keys)
	{
		// For old schema just return everything
		if (! self::new_schema())
			return Settings::get_array();
		
		if ( ! is_array($keys) OR empty($keys))
			throw new Kohana_Exception("Invalid parameters");

		$selected_settings = ORM::factory('settings')
			->in('key', $keys)
			->find_all();

		$settings = array();
		foreach ($selected_settings as $setting)
		{
			$settings[$setting->key] = $setting->value;
		}

		return $settings;

	}
}
