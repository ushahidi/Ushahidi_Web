<?php defined('SYSPATH') or die('No direct script access');
/**
 * CSRF token generation and validation helper library
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://github.com/ushahidi/Ushahidi_Web
 * @category   Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class csrf_Core {

	/**
	 * Session key for the CSRF token
	 * @var string
	 */
	private static $_csrf_session_key = 'csrf-token';

	/**
	 * Generates an returns a randon token for CSRF
	 * prevention
	 *
	 * @param bool $replace Whether to replace the current token
	 * @return string
	 */	
	public static function token($replace = FALSE)
	{
		$token = Session::instance()->get(self::$_csrf_session_key);

		if ( ! $token OR $replace)
		{
			// Generates a hash of variable length random alpha-numeric string
			$token = hash('sha256', text::random('alnum', rand(25, 32)));
			Session::instance()->set('csrf-token', $token);
			Kohana::log('debug', 'Regenerated CSRF token: '.$token);
		}

		return $token;
	}

	/**
	 * Validates the specified token against the current
	 * session value
	 *
	 * @return bool TRUE if match, FALSE otherwise
	 */
	public static function valid($token)
	{
		// Get the current token and destroy the session value
		$current_token = self::token();

		return $token === $current_token;
	}
}

?>
