<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Cloudfiles
 *
 * Base class for all Cloud Files API Bindings
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

define("PHP_CF_VERSION", "1.7.10");
define("USER_AGENT", sprintf("PHP-CloudFiles/%s", PHP_CF_VERSION));
define("MAX_HEADER_NAME_LEN", 128);
define("MAX_HEADER_VALUE_LEN", 256);
define("ACCOUNT_CONTAINER_COUNT", "X-Account-Container-Count");
define("ACCOUNT_BYTES_USED", "X-Account-Bytes-Used");
define("CONTAINER_OBJ_COUNT", "X-Container-Object-Count");
define("CONTAINER_BYTES_USED", "X-Container-Bytes-Used");
define("MANIFEST_HEADER", "X-Object-Manifest");
define("METADATA_HEADER_PREFIX", "X-Object-Meta-");
define("CONTENT_HEADER_PREFIX", "Content-");
define("ACCESS_CONTROL_HEADER_PREFIX", "Access-Control-");
define("ORIGIN_HEADER", "Origin");
define("CDN_URI", "X-CDN-URI");
define("CDN_SSL_URI", "X-CDN-SSL-URI");
define("CDN_STREAMING_URI", "X-CDN-Streaming-URI");
define("CDN_ENABLED", "X-CDN-Enabled");
define("CDN_LOG_RETENTION", "X-Log-Retention");
define("CDN_ACL_USER_AGENT", "X-User-Agent-ACL");
define("CDN_ACL_REFERRER", "X-Referrer-ACL");
define("CDN_TTL", "X-TTL");
define("CDNM_URL", "X-CDN-Management-Url");
define("STORAGE_URL", "X-Storage-Url");
define("AUTH_TOKEN", "X-Auth-Token");
define("AUTH_USER_HEADER", "X-Auth-User");
define("AUTH_KEY_HEADER", "X-Auth-Key");
define("AUTH_USER_HEADER_LEGACY", "X-Storage-User");
define("AUTH_KEY_HEADER_LEGACY", "X-Storage-Pass");
define("AUTH_TOKEN_LEGACY", "X-Storage-Token");
define("CDN_EMAIL", "X-Purge-Email");
define("DESTINATION", "Destination");
define("ETAG_HEADER", "ETag");
define("LAST_MODIFIED_HEADER", "Last-Modified");
define("CONTENT_TYPE_HEADER", "Content-Type");
define("CONTENT_LENGTH_HEADER", "Content-Length");
define("USER_AGENT_HEADER", "User-Agent");

define("DEFAULT_CF_API_VERSION", 1);
define("MAX_CONTAINER_NAME_LEN", 256);
define("MAX_OBJECT_NAME_LEN", 1024);
define("MAX_OBJECT_SIZE", 5*1024*1024*1024+1);
define("US_AUTHURL", "https://auth.api.rackspacecloud.com");
define("UK_AUTHURL", "https://lon.auth.api.rackspacecloud.com");

class Cloudfiles {

	// Cloud Files Username, defined in cdn config file
	protected $username;

	// Rackspace API Key, defined in cdn config file
	protected $api_key;

	// Name of containter you want to store your files in, defined in cdn config file
	protected $container;

	// Connection used throughout the class
	protected $conn;

	public function __construct()
	{
		$this->username = Kohana::config("cdn.cdn_username");
		$this->api_key = Kohana::config("cdn.cdn_api_key");
		$this->container = Kohana::config("cdn.cdn_container");
	}

	public function authenticate()
	{
		if($this->conn) return;

		// Include CF libraries
		require_once Kohana::find_file('libraries/cloudfiles', 'CF_Authentication');
		require_once Kohana::find_file('libraries/cloudfiles', 'CF_Connection');
		require_once Kohana::find_file('libraries/cloudfiles', 'CF_Container');
		require_once Kohana::find_file('libraries/cloudfiles', 'CF_Http');
		require_once Kohana::find_file('libraries/cloudfiles', 'CF_Object');

		$auth = new CF_Authentication($this->username, $this->api_key);
		$auth->authenticate();
		$this->conn = new CF_Connection($auth);
	}

	// $file must be the absolute path to the file
	public function upload($filename)
	{
		$this->authenticate();

		$local_directory = Kohana::config('upload.directory', TRUE);
		$local_directory = rtrim($local_directory, '/').'/';

		$fullpath = $local_directory.$filename;

		// Put this in a special directory based on subdomain if subdomain is set
		$dir = $this->_special_dir();

		// Set container
		$container = $this->conn->create_container($this->container);

		// This creates a "fake" directory structure on the Cloud Files system
		$container->create_paths($dir.$filename);

		// Get the object ready for loading
		$file = $container->create_object($dir.$filename);

		// Finally, upload the file
		$file->load_from_filename($fullpath);

		$uri = $container->make_public();

		// Return the file path URL
		return $file->public_ssl_uri();
	}

	public function delete($url)
	{
		$this->authenticate();

		// Get the container that has the object
		$container = $this->conn->get_container($this->container);

		// Figure out object name based on the URL
		$url = str_ireplace('http://','',$url);
		$url = str_ireplace('https://','',$url);
		$url = explode('/',$url);
		unset($url[0]);
		$object = implode('/',$url);

		// Do the deed
		$container->delete_object($object);
	}

	public function _special_dir()
	{
		$dir = '';
		if(Kohana::config('settings.subdomain') != '') {
			$dir = Kohana::config('settings.subdomain');
			// Make sure there's a slash on the end
			$dir = rtrim($dir, '/').'/';
		}
		return $dir;
	}
}

?>
