<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Text helper class.
 * Extends built-in helper class
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Text Helper
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class text extends text_Core {

	// Converts HTML to Text
	function html2txt($document)
	{
		$search = array(
			'@<script[^>]*?>.*?</script>@si',	// Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',			// Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',	// Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'			// Strip multi-line comments including CDATA
		);
		$text = preg_replace($search, '', $document);
		return $text;
	}

	// Generates a random string of characters
	// Pulled from PHP.net documentation comment: http://www.php.net/manual/en/function.rand.php#90773
	function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
	    // Length of character list

	    $chars_length = (strlen($chars) - 1);

	    // Start our string

	    $string = $chars{rand(0, $chars_length)};

	    // Generate random string

	    for ($i = 1; $i < $length; $i = strlen($string))
	    {
	        // Grab a random character from our list

	        $r = $chars{rand(0, $chars_length)};

	        // Make sure the same two characters don't appear next to each other

	        if ($r != $string{$i - 1}) $string .=  $r;
	    }

	    // Return the string

	    return $string;
	}

}
?>