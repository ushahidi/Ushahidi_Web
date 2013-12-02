<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * HTML helper class.
 *
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     File Helper
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class html extends html_Core {
	
	/**
	 * Helper function for easy use of HTMLPurifier
	 * 
	 * @param string $input
	 * @return string
	 */
	public function clean($input)
	{
		require_once APPPATH.'libraries/htmlpurifier/HTMLPurifier.auto.php';

		$config = HTMLPurifier_Config::createDefault();
		// Defaults to UTF-8
		// $config->set('Core.Encoding', 'UTF-8');
		// $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
		$config->set('Core.EnableIDNA', TRUE);
		$config->set('Cache.SerializerPath', APPPATH.'cache');
		$config->set('HTML.Allowed', Kohana::config('config.allowed_html', FALSE, TRUE));
		// Allow some basic iframes
		$config->set('HTML.SafeIframe', true);
		$config->set('URI.SafeIframeRegexp', 
			Kohana::config('config.safe_iframe_regexp', FALSE, TRUE)
		);
		$config->set('Filter.YouTube', true);
		$purifier = new HTMLPurifier($config);
		$clean_html = $purifier->purify($input);

		return $clean_html;
	}
	
	/**
	 * Helper function to clean and escape plaintext before display
	 * 
	 * This should be used to strip tags and then escape html entities, etc.
	 * 
	 * @param string $input
	 * @param bool $encode Encode html entities?
	 * @return string
	 */
	public function strip_tags($input, $encode = TRUE)
	{
		require_once APPPATH.'libraries/htmlpurifier/HTMLPurifier.auto.php';

		$config = HTMLPurifier_Config::createDefault();
		// Defaults to UTF-8
		// $config->set('Core.Encoding', 'UTF-8');
		// $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
		$config->set('Core.EnableIDNA', TRUE);
		$config->set('Cache.SerializerPath', APPPATH.'cache');
		$config->set('HTML.Allowed', "");
		
		$purifier = new HTMLPurifier($config);
		$clean_html = $purifier->purify($input);

		return $encode ? self::escape($clean_html) : $clean_html;
	}
	
	/**
	 * Get info message about allowed html tags
	 * 
	 * @return string
	 **/
	public function allowed_html()
	{
		require_once APPPATH.'libraries/htmlpurifier/HTMLPurifier.auto.php';
		
		$def = new HTMLPurifier_HTMLDefinition();
		list($el, $attr) = $def->parseTinyMCEAllowedList(Kohana::config('config.allowed_html', FALSE, TRUE));
		$iframes = explode('|', str_replace(array('%^http://(',')%'), '', Kohana::config('config.safe_iframe_regexp', FALSE, TRUE)));
		
		$output = "";
		$output .= Kohana::lang('ui_main.allowed_tags', implode(', ', array_keys($el)));
		$output .= "<br/> ";
		$output .= Kohana::lang('ui_main.allowed_iframes', implode(', ', $iframes));
		return $output;
	}
	
	/**
	 * Helper function to escape plaintext before display
	 * 
	 * This should be used to escape html entities, etc.
	 * 
	 * @param string $input
	 * @param bool $double_encode
	 * @return string
	 */
	public function escape($input, $double_encode = FALSE)
	{
		// Ensure we have valid correctly encoded string..
		// http://stackoverflow.com/questions/1412239/why-call-mb-convert-encoding-to-sanitize-text
		$input = mb_convert_encoding($input, "UTF-8", "UTF-8");
		// why are we using html entities? this -> http://stackoverflow.com/a/110576/992171
		return htmlentities($input, ENT_QUOTES, 'UTF-8', $double_encode);
	}
	
}
	