<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Validation helper class.
 *
 * $Id: valid.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class valid_Core {

	/**
	 * Validate email, commonly used characters only
	 *
	 * @param   string   email address
	 * @return  boolean
	 */
	public static function email($email)
	{
		return (bool) preg_match('/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD', (string) $email);
	}

	/**
	 * Validate the domain of an email address by checking if the domain has a
	 * valid MX record.
	 *
	 * @param   string   email address
	 * @return  boolean
	 */
	public static function email_domain($email)
	{
		// If we can't prove the domain is invalid, consider it valid
		// Note: checkdnsrr() is not implemented on Windows platforms
		if ( ! function_exists('checkdnsrr'))
			return TRUE;

		// Check if the email domain has a valid MX record
		return (bool) checkdnsrr(preg_replace('/^[^@]+@/', '', $email), 'MX');
	}

	/**
	 * Validate email, RFC compliant version
	 * Note: This function is LESS strict than valid_email. Choose carefully.
	 *
	 * @see  Originally by Cal Henderson, modified to fit Kohana syntax standards:
	 * @see  http://www.iamcal.com/publish/articles/php/parsing_email/
	 * @see  http://www.w3.org/Protocols/rfc822/
	 *
	 * @param   string   email address
	 * @return  boolean
	 */
	public static function email_rfc($email)
	{
		$qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
		$dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
		$atom  = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
		$pair  = '\\x5c[\\x00-\\x7f]';

		$domain_literal = "\\x5b($dtext|$pair)*\\x5d";
		$quoted_string  = "\\x22($qtext|$pair)*\\x22";
		$sub_domain     = "($atom|$domain_literal)";
		$word           = "($atom|$quoted_string)";
		$domain         = "$sub_domain(\\x2e$sub_domain)*";
		$local_part     = "$word(\\x2e$word)*";
		$addr_spec      = "$local_part\\x40$domain";

		return (bool) preg_match('/^'.$addr_spec.'$/D', (string) $email);
	}

	/**
	 * Validate URL
	 *
	 * @param   string   URL
	 * @return  boolean
	 */
	public static function url($url)
	{
		return (bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
	}

	/**
	 * Validate IP
	 *
	 * @param   string   IP address
	 * @param   boolean  allow IPv6 addresses
	 * @param   boolean  allow private IP networks
	 * @return  boolean
	 */
	public static function ip($ip, $ipv6 = FALSE, $allow_private = TRUE)
	{
		// By default do not allow private and reserved range IPs
		$flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
		if ($allow_private === TRUE)
			$flags =  FILTER_FLAG_NO_RES_RANGE;

		if ($ipv6 === TRUE)
			return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flags);

		return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flags | FILTER_FLAG_IPV4);
	}

	/**
	 * Validates a credit card number using the Luhn (mod10) formula.
	 * @see http://en.wikipedia.org/wiki/Luhn_algorithm
	 *
	 * @param   integer       credit card number
	 * @param   string|array  card type, or an array of card types
	 * @return  boolean
	 */
	public static function credit_card($number, $type = NULL)
	{
		// Remove all non-digit characters from the number
		if (($number = preg_replace('/\D+/', '', $number)) === '')
			return FALSE;

		if ($type == NULL)
		{
			// Use the default type
			$type = 'default';
		}
		elseif (is_array($type))
		{
			foreach ($type as $t)
			{
				// Test each type for validity
				if (valid::credit_card($number, $t))
					return TRUE;
			}

			return FALSE;
		}

		$cards = Kohana::config('credit_cards');

		// Check card type
		$type = strtolower($type);

		if ( ! isset($cards[$type]))
			return FALSE;

		// Check card number length
		$length = strlen($number);

		// Validate the card length by the card type
		if ( ! in_array($length, preg_split('/\D+/', $cards[$type]['length'])))
			return FALSE;

		// Check card number prefix
		if ( ! preg_match('/^'.$cards[$type]['prefix'].'/', $number))
			return FALSE;

		// No Luhn check required
		if ($cards[$type]['luhn'] == FALSE)
			return TRUE;

		// Checksum of the card number
		$checksum = 0;

		for ($i = $length - 1; $i >= 0; $i -= 2)
		{
			// Add up every 2nd digit, starting from the right
			$checksum += substr($number, $i, 1);
		}

		for ($i = $length - 2; $i >= 0; $i -= 2)
		{
			// Add up every 2nd digit doubled, starting from the right
			$double = substr($number, $i, 1) * 2;

			// Subtract 9 from the double where value is greater than 10
			$checksum += ($double >= 10) ? $double - 9 : $double;
		}

		// If the checksum is a multiple of 10, the number is valid
		return ($checksum % 10 === 0);
	}

	/**
	 * Checks if a phone number is valid.
	 *
	 * @param   string   phone number to check
	 * @return  boolean
	 */
	public static function phone($number, $lengths = NULL)
	{
		if ( ! is_array($lengths))
		{
			$lengths = array(7,10,11);
		}

		// Remove all non-digit characters from the number
		$number = preg_replace('/\D+/', '', $number);

		// Check if the number is within range
		return in_array(strlen($number), $lengths);
	}

	/**
	 * Tests if a string is a valid date string.
	 * 
	 * @param   string   date to check
	 * @return  boolean
	 */
	public function date($str)
	{
		return (strtotime($str) !== FALSE);
	}

	/**
	 * Checks whether a string consists of alphabetical characters only.
	 *
	 * @param   string   input string
	 * @param   boolean  trigger UTF-8 compatibility
	 * @return  boolean
	 */
	public static function alpha($str, $utf8 = FALSE)
	{
		return ($utf8 === TRUE)
			? (bool) preg_match('/^\pL++$/uD', (string) $str)
			: ctype_alpha((string) $str);
	}

	/**
	 * Checks whether a string consists of alphabetical characters and numbers only.
	 *
	 * @param   string   input string
	 * @param   boolean  trigger UTF-8 compatibility
	 * @return  boolean
	 */
	public static function alpha_numeric($str, $utf8 = FALSE)
	{
		return ($utf8 === TRUE)
			? (bool) preg_match('/^[\pL\pN]++$/uD', (string) $str)
			: ctype_alnum((string) $str);
	}

	/**
	 * Checks whether a string consists of alphabetical characters, numbers, underscores and dashes only.
	 *
	 * @param   string   input string
	 * @param   boolean  trigger UTF-8 compatibility
	 * @return  boolean
	 */
	public static function alpha_dash($str, $utf8 = FALSE)
	{
		return ($utf8 === TRUE)
			? (bool) preg_match('/^[-\pL\pN_]++$/uD', (string) $str)
			: (bool) preg_match('/^[-a-z0-9_]++$/iD', (string) $str);
	}

	/**
	 * Checks whether a string consists of digits only (no dots or dashes).
	 *
	 * @param   string   input string
	 * @param   boolean  trigger UTF-8 compatibility
	 * @return  boolean
	 */
	public static function digit($str, $utf8 = FALSE)
	{
		return ($utf8 === TRUE)
			? (bool) preg_match('/^\pN++$/uD', (string) $str)
			: ctype_digit((string) $str);
	}

	/**
	 * Checks whether a string is a valid number (negative and decimal numbers allowed).
	 *
	 * @see Uses locale conversion to allow decimal point to be locale specific.
	 * @see http://www.php.net/manual/en/function.localeconv.php
	 * 
	 * @param   string   input string
	 * @return  boolean
	 */
	public static function numeric($str)
	{
		// Use localeconv to set the decimal_point value: Usually a comma or period.
		$locale = localeconv();
		return (preg_match('/^[-0-9'.$locale['decimal_point'].']++$/D', (string) $str));
	}

	/**
	 * Checks whether a string is a valid text. Letters, numbers, whitespace,
	 * dashes, periods, and underscores are allowed.
	 *
	 * @param   string   text to check
	 * @return  boolean
	 */
	public static function standard_text($str)
	{
		// pL matches letters
		// pN matches numbers
		// pZ matches whitespace
		// pPc matches underscores
		// pPd matches dashes
		// pPo matches normal puncuation
		return (bool) preg_match('/^[\pL\pN\pZ\p{Pc}\p{Pd}\p{Po}]++$/uD', (string) $str);
	}

	/**
	 * Checks if a string is a proper decimal format. The format array can be
	 * used to specify a decimal length, or a number and decimal length, eg:
	 * array(2) would force the number to have 2 decimal places, array(4,2)
	 * would force the number to have 4 digits and 2 decimal places.
	 *
	 * @param   string   input string
	 * @param   array    decimal format: y or x,y
	 * @return  boolean
	 */
	public static function decimal($str, $format = NULL)
	{
		// Create the pattern
		$pattern = '/^[0-9]%s\.[0-9]%s$/';

		if ( ! empty($format))
		{
			if (count($format) > 1)
			{
				// Use the format for number and decimal length
				$pattern = sprintf($pattern, '{'.$format[0].'}', '{'.$format[1].'}');
			}
			elseif (count($format) > 0)
			{
				// Use the format as decimal length
				$pattern = sprintf($pattern, '+', '{'.$format[0].'}');
			}
		}
		else
		{
			// No format
			$pattern = sprintf($pattern, '+', '+');
		}

		return (bool) preg_match($pattern, (string) $str);
	}
	
	/**
	 * Checks whether a string is a valid number (negative and decimal numbers allowed) and between a specified range.
	 *
	 * @param   string   input string
	 * @param   float (MIN/MAX)
	 * @return  boolean
	 */
	public static function between($str, $min_max = array(0,0))
	{
		$set_min = $min_max[0];
		$set_max = $min_max[1];
		$str = (float) $str;
		return (is_numeric($str) AND preg_match('/^[-0-9.]++$/D', (string) $str) AND ($str <= $set_max AND $str >= $set_min) );
	}
	
	/**
	 * Checks whether a string is a valid date (mm/dd/yyyy).
	 *
	 * @param   string   input string
	 * @return  boolean
	 */
	public static function date_mmddyyyy($str)
	{
		$str = str_replace(' ', '-', $str);
		$str = str_replace('/', '-', $str);
		$str = str_replace('--', '-', $str);
		preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $str, $xadBits);
		if (is_array($xadBits) && count($xadBits) > 3)
		{
			return checkdate($xadBits[1], $xadBits[2], $xadBits[3]);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks whether a string is a valid date (dd/mm/yyyy).
	 *
	 * @param   string   input string
	 * @return  boolean
	 */
	public static function date_ddmmyyyy($str)
	{
		$str = str_replace(' ', '-', $str);
		$str = str_replace('/', '-', $str);
		$str = str_replace('--', '-', $str);
		preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $str, $xadBits);
		if (is_array($xadBits) && count($xadBits) > 3)
		{
			return checkdate($xadBits[2], $xadBits[1], $xadBits[3]);
		}
		else
		{
			return false;
		}
	}	

} // End valid