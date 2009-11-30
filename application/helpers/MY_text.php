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

}
?>