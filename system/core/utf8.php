<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * A port of phputf8 to a unified file/class. Checks PHP status to ensure that
 * UTF-8 support is available and normalize global variables to UTF-8. It also
 * provides multi-byte aware replacement string functions.
 *
 * This file is licensed differently from the rest of Kohana. As a port of
 * phputf8, which is LGPL software, this file is released under the LGPL.
 *
 * PCRE needs to be compiled with UTF-8 support (--enable-utf8).
 * Support for Unicode properties is highly recommended (--enable-unicode-properties).
 * @see http://php.net/manual/reference.pcre.pattern.modifiers.php
 *
 * UTF-8 conversion will be much more reliable if the iconv extension is loaded.
 * @see http://php.net/iconv
 *
 * The mbstring extension is highly recommended, but must not be overloading
 * string functions.
 * @see http://php.net/mbstring
 *
 * $Id: utf8.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */

if ( ! preg_match('/^.$/u', 'ñ'))
{
	trigger_error
	(
		'<a href="http://php.net/pcre">PCRE</a> has not been compiled with UTF-8 support. '.
		'See <a href="http://php.net/manual/reference.pcre.pattern.modifiers.php">PCRE Pattern Modifiers</a> '.
		'for more information. This application cannot be run without UTF-8 support.',
		E_USER_ERROR
	);
}

if ( ! extension_loaded('iconv'))
{
	trigger_error
	(
		'The <a href="http://php.net/iconv">iconv</a> extension is not loaded. '.
		'Without iconv, strings cannot be properly translated to UTF-8 from user input. '.
		'This application cannot be run without UTF-8 support.',
		E_USER_ERROR
	);
}

if (extension_loaded('mbstring') AND (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING))
{
	trigger_error
	(
		'The <a href="http://php.net/mbstring">mbstring</a> extension is overloading PHP\'s native string functions. '.
		'Disable this by setting mbstring.func_overload to 0, 1, 4 or 5 in php.ini or a .htaccess file.'.
		'This application cannot be run without UTF-8 support.',
		E_USER_ERROR
	);
}

// Check PCRE support for Unicode properties such as \p and \X.
$ER = error_reporting(0);
define('PCRE_UNICODE_PROPERTIES', (bool) preg_match('/^\pL$/u', 'ñ'));
error_reporting($ER);

// SERVER_UTF8 ? use mb_* functions : use non-native functions
if (extension_loaded('mbstring'))
{
	mb_internal_encoding('UTF-8');
	define('SERVER_UTF8', TRUE);
}
else
{
	define('SERVER_UTF8', FALSE);
}

// Convert all global variables to UTF-8.
$_GET    = utf8::clean($_GET);
$_POST   = utf8::clean($_POST);
$_COOKIE = utf8::clean($_COOKIE);
$_SERVER = utf8::clean($_SERVER);

if (PHP_SAPI == 'cli')
{
	// Convert command line arguments
	$_SERVER['argv'] = utf8::clean($_SERVER['argv']);
}

final class utf8 {

	// Called methods
	static $called = array();

	/**
	 * Recursively cleans arrays, objects, and strings. Removes ASCII control
	 * codes and converts to UTF-8 while silently discarding incompatible
	 * UTF-8 characters.
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function clean($str)
	{
		if (is_array($str) OR is_object($str))
		{
			foreach ($str as $key => $val)
			{
				// Recursion!
				$str[self::clean($key)] = self::clean($val);
			}
		}
		elseif (is_string($str) AND $str !== '')
		{
			// Remove control characters
			$str = self::strip_ascii_ctrl($str);

			if ( ! self::is_ascii($str))
			{
				// Disable notices
				$ER = error_reporting(~E_NOTICE);

				// iconv is expensive, so it is only used when needed
				$str = iconv('UTF-8', 'UTF-8//IGNORE', $str);

				// Turn notices back on
				error_reporting($ER);
			}
		}

		return $str;
	}

	/**
	 * Tests whether a string contains only 7bit ASCII bytes. This is used to
	 * determine when to use native functions or UTF-8 functions.
	 *
	 * @param   string  string to check
	 * @return  bool
	 */
	public static function is_ascii($str)
	{
		return ! preg_match('/[^\x00-\x7F]/S', $str);
	}

	/**
	 * Strips out device control codes in the ASCII range.
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function strip_ascii_ctrl($str)
	{
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}

	/**
	 * Strips out all non-7bit ASCII bytes.
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function strip_non_ascii($str)
	{
		return preg_replace('/[^\x00-\x7F]+/S', '', $str);
	}

	/**
	 * Replaces special/accented UTF-8 characters by ASCII-7 'equivalents'.
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   string to transliterate
	 * @param   integer  -1 lowercase only, +1 uppercase only, 0 both cases
	 * @return  string
	 */
	public static function transliterate_to_ascii($str, $case = 0)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _transliterate_to_ascii($str, $case);
	}

	/**
	 * Returns the length of the given string.
	 * @see http://php.net/strlen
	 *
	 * @param   string   string being measured for length
	 * @return  integer
	 */
	public static function strlen($str)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strlen($str);
	}

	/**
	 * Finds position of first occurrence of a UTF-8 string.
	 * @see http://php.net/strlen
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   haystack
	 * @param   string   needle
	 * @param   integer  offset from which character in haystack to start searching
	 * @return  integer  position of needle
	 * @return  boolean  FALSE if the needle is not found
	 */
	public static function strpos($str, $search, $offset = 0)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strpos($str, $search, $offset);
	}

	/**
	 * Finds position of last occurrence of a char in a UTF-8 string.
	 * @see http://php.net/strrpos
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   haystack
	 * @param   string   needle
	 * @param   integer  offset from which character in haystack to start searching
	 * @return  integer  position of needle
	 * @return  boolean  FALSE if the needle is not found
	 */
	public static function strrpos($str, $search, $offset = 0)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strrpos($str, $search, $offset);
	}

	/**
	 * Returns part of a UTF-8 string.
	 * @see http://php.net/substr
	 *
	 * @author  Chris Smith <chris@jalakai.co.uk>
	 *
	 * @param   string   input string
	 * @param   integer  offset
	 * @param   integer  length limit
	 * @return  string
	 */
	public static function substr($str, $offset, $length = NULL)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _substr($str, $offset, $length);
	}

	/**
	 * Replaces text within a portion of a UTF-8 string.
	 * @see http://php.net/substr_replace
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   string   replacement string
	 * @param   integer  offset
	 * @return  string
	 */
	public static function substr_replace($str, $replacement, $offset, $length = NULL)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _substr_replace($str, $replacement, $offset, $length);
	}

	/**
	 * Makes a UTF-8 string lowercase.
	 * @see http://php.net/strtolower
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function strtolower($str)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strtolower($str);
	}

	/**
	 * Makes a UTF-8 string uppercase.
	 * @see http://php.net/strtoupper
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function strtoupper($str)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strtoupper($str);
	}

	/**
	 * Makes a UTF-8 string's first character uppercase.
	 * @see http://php.net/ucfirst
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function ucfirst($str)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _ucfirst($str);
	}

	/**
	 * Makes the first character of every word in a UTF-8 string uppercase.
	 * @see http://php.net/ucwords
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function ucwords($str)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _ucwords($str);
	}

	/**
	 * Case-insensitive UTF-8 string comparison.
	 * @see http://php.net/strcasecmp
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   string to compare
	 * @param   string   string to compare
	 * @return  integer  less than 0 if str1 is less than str2
	 * @return  integer  greater than 0 if str1 is greater than str2
	 * @return  integer  0 if they are equal
	 */
	public static function strcasecmp($str1, $str2)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strcasecmp($str1, $str2);
	}

	/**
	 * Returns a string or an array with all occurrences of search in subject (ignoring case).
	 * replaced with the given replace value.
	 * @see     http://php.net/str_ireplace
	 *
	 * @note    It's not fast and gets slower if $search and/or $replace are arrays.
	 * @author  Harry Fuecks <hfuecks@gmail.com
	 *
	 * @param   string|array  text to replace
	 * @param   string|array  replacement text
	 * @param   string|array  subject text
	 * @param   integer       number of matched and replaced needles will be returned via this parameter which is passed by reference
	 * @return  string        if the input was a string
	 * @return  array         if the input was an array
	 */
	public static function str_ireplace($search, $replace, $str, & $count = NULL)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _str_ireplace($search, $replace, $str, $count);
	}

	/**
	 * Case-insenstive UTF-8 version of strstr. Returns all of input string
	 * from the first occurrence of needle to the end.
	 * @see http://php.net/stristr
	 *
	 * @author Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   string   needle
	 * @return  string   matched substring if found
	 * @return  boolean  FALSE if the substring was not found
	 */
	public static function stristr($str, $search)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _stristr($str, $search);
	}

	/**
	 * Finds the length of the initial segment matching mask.
	 * @see http://php.net/strspn
	 *
	 * @author Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   string   mask for search
	 * @param   integer  start position of the string to examine
	 * @param   integer  length of the string to examine
	 * @return  integer  length of the initial segment that contains characters in the mask
	 */
	public static function strspn($str, $mask, $offset = NULL, $length = NULL)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strspn($str, $mask, $offset, $length);
	}

	/**
	 * Finds the length of the initial segment not matching mask.
	 * @see http://php.net/strcspn
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   string   mask for search
	 * @param   integer  start position of the string to examine
	 * @param   integer  length of the string to examine
	 * @return  integer  length of the initial segment that contains characters not in the mask
	 */
	public static function strcspn($str, $mask, $offset = NULL, $length = NULL)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strcspn($str, $mask, $offset, $length);
	}

	/**
	 * Pads a UTF-8 string to a certain length with another string.
	 * @see http://php.net/str_pad
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   integer  desired string length after padding
	 * @param   string   string to use as padding
	 * @param   string   padding type: STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH
	 * @return  string
	 */
	public static function str_pad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _str_pad($str, $final_str_length, $pad_str, $pad_type);
	}

	/**
	 * Converts a UTF-8 string to an array.
	 * @see http://php.net/str_split
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   integer  maximum length of each chunk
	 * @return  array
	 */
	public static function str_split($str, $split_length = 1)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _str_split($str, $split_length);
	}

	/**
	 * Reverses a UTF-8 string.
	 * @see http://php.net/strrev
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   string to be reversed
	 * @return  string
	 */
	public static function strrev($str)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _strrev($str);
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the beginning and
	 * end of a string.
	 * @see http://php.net/trim
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   input string
	 * @param   string   string of characters to remove
	 * @return  string
	 */
	public static function trim($str, $charlist = NULL)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _trim($str, $charlist);
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the beginning of a string.
	 * @see http://php.net/ltrim
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   input string
	 * @param   string   string of characters to remove
	 * @return  string
	 */
	public static function ltrim($str, $charlist = NULL)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _ltrim($str, $charlist);
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the end of a string.
	 * @see http://php.net/rtrim
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   input string
	 * @param   string   string of characters to remove
	 * @return  string
	 */
	public static function rtrim($str, $charlist = NULL)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _rtrim($str, $charlist);
	}

	/**
	 * Returns the unicode ordinal for a character.
	 * @see http://php.net/ord
	 *
	 * @author Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   UTF-8 encoded character
	 * @return  integer
	 */
	public static function ord($chr)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _ord($chr);
	}

	/**
	 * Takes an UTF-8 string and returns an array of ints representing the Unicode characters.
	 * Astral planes are supported i.e. the ints in the output can be > 0xFFFF.
	 * Occurrances of the BOM are ignored. Surrogates are not allowed.
	 *
	 * The Original Code is Mozilla Communicator client code.
	 * The Initial Developer of the Original Code is Netscape Communications Corporation.
	 * Portions created by the Initial Developer are Copyright (C) 1998 the Initial Developer.
	 * Ported to PHP by Henri Sivonen <hsivonen@iki.fi>, see http://hsivonen.iki.fi/php-utf8/.
	 * Slight modifications to fit with phputf8 library by Harry Fuecks <hfuecks@gmail.com>.
	 *
	 * @param   string   UTF-8 encoded string
	 * @return  array    unicode code points
	 * @return  boolean  FALSE if the string is invalid
	 */
	public static function to_unicode($str)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _to_unicode($str);
	}

	/**
	 * Takes an array of ints representing the Unicode characters and returns a UTF-8 string.
	 * Astral planes are supported i.e. the ints in the input can be > 0xFFFF.
	 * Occurrances of the BOM are ignored. Surrogates are not allowed.
	 *
	 * The Original Code is Mozilla Communicator client code.
	 * The Initial Developer of the Original Code is Netscape Communications Corporation.
	 * Portions created by the Initial Developer are Copyright (C) 1998 the Initial Developer.
	 * Ported to PHP by Henri Sivonen <hsivonen@iki.fi>, see http://hsivonen.iki.fi/php-utf8/.
	 * Slight modifications to fit with phputf8 library by Harry Fuecks <hfuecks@gmail.com>.
	 *
	 * @param   array    unicode code points representing a string
	 * @return  string   utf8 string of characters
	 * @return  boolean  FALSE if a code point cannot be found
	 */
	public static function from_unicode($arr)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require SYSPATH.'core/utf8/'.__FUNCTION__.EXT;

			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}

		return _from_unicode($arr);
	}

} // End utf8