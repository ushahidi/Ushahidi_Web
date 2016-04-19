<?php defined('INSTALLER_DIR') or die('No direct script access');

/**
 * Installation utilities class
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @subpackage Installer
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Installer_Utils {

	/**
	 * Generates a random alpha-numeric string
	 *
	 * @return string
	 */
	public static function get_random_str($length = 24)
	{
		// Characters to be used for random string generatino
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+[]{};:,./?`~';
		
		// Split the pool into an array of characters
		$pool = str_split($pool, 1);
		
		$max = count($pool) - 1;
		
		$str = '';
		for ($i=0; $i < $length; $i++)
		{
			$str .= $pool[mt_rand(0, $max)];
		}
		
		// Ensure the string has at least one digit and one letter
		if (ctype_alpha($str))
		{
			// Add a random digit at a randomly chosen position
			$str[mt_rand(0, $length - 1)] = chr(mt_rand(23, 61));
		}
		elseif (ctype_digit($str))
		{
			// Add a random character at a randomly chosen position
			$str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
		}
		
		return $str;
	}
	
	/**
	 * Generates a salt pattern
	 *
	 * @return string
	 */
	public static function get_salt_pattern()
	{
		$salt_pattern = array();
		for ($i = 0; $i < 10; $i++)
		{
			// Generate an offset
			$offset = mt_rand(1, 40);
			
			// Ensure all the offsets are unique
			while (in_array($offset, $salt_pattern))
			{
				$offset = mt_rand(1, 40);
			}
			
			$salt_pattern[] = $offset;
		}
		
		// Sort the values in ascending order
		sort($salt_pattern, SORT_NUMERIC);
		
		return $salt_pattern;
	}

	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 *
	 * @param   string  plaintext password
	 * @param   string  salt for password hash
	 * @return  string  hashed password string
	 */
	public static function hash_password($password, $salt_pattern)
	{
		$salt = substr(self::hash(uniqid(NULL, TRUE)), 0, count($salt_pattern));
				
		// Password hash that the salt will be inserted into
		$hash = self::hash($salt.$password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
		$password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($salt_pattern as $offset)
		{
			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part.array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;
		}

		// Return the password, with the remaining hash appended
		return $password.$hash;
	}
	
	public static function hash($str)
	{
		return hash("sha1", $str);
	}
	
}
?>