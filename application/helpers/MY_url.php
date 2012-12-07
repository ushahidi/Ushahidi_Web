<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * URL helper class
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class url extends url_Core {

	/**
	 * Returns the base URL of the CDN or the standard base URL if no CDN is configured.
	 *
	 * @param   boolean  type of cdn url. Acceptable values are img, css, js
	 * @return  string
	 */
	public static function file_loc($type)
	{
		// Pull CDN URL from the cdn settings if it's set. Otherwise use the base URL by default
		switch($type){
			case 'img':
				$url = (Kohana::config("cdn.cdn_img")) ? Kohana::config("cdn.cdn_img") : url::base();
				break;
			case 'css':
				$url = (Kohana::config("cdn.cdn_css")) ? Kohana::config("cdn.cdn_css") : url::base();
				break;
			case 'js':
				$url = (Kohana::config("cdn.cdn_js")) ? Kohana::config("cdn.cdn_js") : url::base();
				break;
			default:
				$url = url::base();
		}

		// Force a slash on the end of the URL
		return rtrim($url, '/').'/';
	}

	/**
	 * Converts a file location to an absolute URL or returns the absolute URL if absolute URL
	 * is passed. This function is for uploaded files since it uses the configured upload dir
	 *
	 * @param   string  file location or full URL
	 * @return  string
	 */
	public static function convert_uploaded_to_abs($file)
	{
		if(valid::url($file) == true){
			return $file;
		}

		return url::base().Kohana::config('upload.relative_directory').'/'.$file;
	}

} // End url