<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to handle jwysiwyg file manager 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class FileManager_Controller extends Admin_Controller {
	
	public function index()
	{
		define('DEBUG', true);
		
		// an array of file extensions to accept
		$accepted_extensions = array(
			"png", "jpg", "gif"
		);
		
		// http://your-web-site.domain/base/url
		$base_url = url::base() . Kohana::config('upload.relative_directory', TRUE)."jwysiwyg";
		
		// the root path of the upload directory on the server
		$uploads_dir = Kohana::config('upload.directory', TRUE)."jwysiwyg";
		
		// the root path that the files are available from the webserver
		$uploads_access_dir = Kohana::config('upload.directory', TRUE)."jwysiwyg";
		
		if (!file_exists($uploads_access_dir)) {
			mkdir($uploads_access_dir, 0775);
		}
		
		if (DEBUG) {
			if (!file_exists($uploads_access_dir)) {
				$error = 'Folder "' . $uploads_access_dir . '" doesn\'t exists.';
		
				header('Content-type: text/html; charset=UTF-8');
				print('{"error":"config.php: ' . htmlentities($error) . '","success":false}');
				exit();
			}
		}
		
		$capabilities = array(
			"move" => false,
			"rename" => true,
			"remove" => true,
			"mkdir" => false,
			"upload" => true
		);
		
		
		if (extension_loaded('mbstring')) {
			mb_internal_encoding('UTF-8');
			mb_regex_encoding('UTF-8');
		}
		
		require_once Kohana::find_file('libraries/jwysiwyg', 'common', TRUE);
		require_once Kohana::find_file('libraries/jwysiwyg', 'handlers', TRUE);
		
		ResponseRouter::getInstance()->run();
		
	}
	
}
	