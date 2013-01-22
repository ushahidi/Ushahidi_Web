<?php
/**
 * This class provides a simple interface for RiverID authentication
 * with the Ushahidi Platform
 */
class RiverID_Core {

	// RiverID Endpoint
	public $endpoint;

	// Endpoint Exists tells us if the server is there
	public $endpoint_exists;

	// Email
	public $email;

	// Password
	public $password;

	// New Password (Used when changing or setting a new password)
	public $new_password;

	// User_id (RiverID userid, maps to "riverid" in users table)
	public $user_id;

	// Session_id (RiverID sessionid)
	public $session_id;

	// Authenticated (bool, true if logged in, false if not)
	public $authenticated;

	// Token (used when performing some operations like setting a new password when forgotten)
	public $token;

	// RiverID Server Name
	public $server_name;

	// RiverID Server Version Number
	public $server_version;

	// RiverID Server URL
	public $server_url;

	// Array of error messages
	public $error;

	// Cache lifetime
	public $cache_lifetime;

	// Curl object
	public $api;

	function __construct()
	{
		// We haven't authenticated yet
		$this->authenticated = false;

		// We haven't encountered any errors yet
		$this->error = false;

		// Set endpoint
		$this->endpoint = kohana::config('riverid.endpoint');
		$this->api_key = kohana::config('riverid.api_key');

		// Check if endpoint is there
		$this->endpoint_exits = TRUE;
		if ($this->endpoint_exists() == FALSE)
		{
			// This is how we check if the endpoint is up and exists
			$this->error[] = Kohana::lang('auth.password.riverid server down');
			$this->endpoint_exits = FALSE;
		}

		// These will be set only once if about() is called
		$this->server_name = FALSE;
		$this->server_version = FALSE;
		$this->server_url = FALSE;

		// Cache lifetime for variables that we cache
		$this->cache_lifetime = kohana::config('riverid.cache_lifetime');

	}

	/**
	* Returns values for variables that may need to be grabbed before being returned
	*
	* @return mixed, depending on the variable but usually a string
	*/
	function __get($field) {

		switch ($field)
		{
			case 'version':
				return $this->server_version();
			break;
			case 'name':
				return $this->server_name();
			break;
			case 'url':
				return $this->server_url();
			break;
			default:
				throw new Kohana_Exception('libraries.riverid_variable_not_available', $field);
		}

		return FALSE;
	}

	function parseResponse($json, $raw = NULL, $params = array()) {
		if (isset($json->success) AND $json->success) {
			if (isset($params['returnResponse'])) {
				if (isset($json->response)) {
					return $json->response;
				} else {
					return FALSE;
				}
			} else {
				if ($raw) {
					return $raw;
				} else {
					return TRUE;
				}
			}
		}

		if (isset($json->error)) {
			$this->error[] = $json->error;
		} else {
			$error = 'An unexpected authentication error occured. Please try again shortly.';

			if (isset($params['error'])) {
				$this->errorr[] = $error . " (" . $params['error'] . ")";
			} else {
				$this->error[] = $error;
			}
		}

		return FALSE;
	}

	function buildURL($path = '/about', $parameters = array()) {
		$parameters['api_secret'] = $this->api_key;
		$url = $this->endpoint . '/' . ltrim($path, '/');

		if ($parameters) {
			$url .= '?';

			foreach($parameters as $k => $v) {
				$url .= trim($k) . '=' . trim($v) . '&';
			}

			$url = substr($url, 0, -1);
		}

		return $url;
	}

	/**
	* Grabs some information about the server we're dealing with
	*
	* @return bool, true if success, false otherwise
	*/
	function about()
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;

		// Pull from cache first, if we've already called these before
		$cache = Cache::instance();
		$this->server_name = $cache->get('riverid_server_name');
		$this->server_version = $cache->get('riverid_server_version');
		$this->server_url = $cache->get('riverid_server_url');

		if ( ! $this->server_name
			OR ! $this->server_version
			OR ! $this->server_url )
		{
			// Cache is Empty so Re-Cache

			$url = $this->endpoint . '/about';
			$about_response = $this->_curl_req($url);
			$about = json_decode($about_response);

			if (isset($about->success) && $about->success)
			{
				// Successful signin, save some variables
				$this->server_name = $about->response->name;
				$this->server_version = $about->response->version;
				$this->server_url = $about->response->info_url;

				// Set cache so we don't have to keep calling this method
				$cache->set('riverid_server_name', $this->server_name, array('riverid'), $this->cache_lifetime);
				$cache->set('riverid_server_version', $this->server_version, array('riverid'), $this->cache_lifetime);
				$cache->set('riverid_server_url', $this->server_url, array('riverid'), $this->cache_lifetime);
			}
			else
			{
				// Failed signin
				$this->error[] = $about->error;
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	* Checks if an email address has been registered
	*
	* @return string, json of the response from the riverid server
	*/
	function registered()
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;

		return $this->_curl_req($this->buildURL('/registered', array('email' => urlencode($this->email))));
	}

	/**
	* Makes calling registered function easier
	*
	* @return bool, true if registered
	*/
	function is_registered()
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;

		return $this->parseResponse(json_decode($this->registered()), NULL, array('returnResponse' => TRUE));
	}

	/**
	* Registers a user
	*
	* @return string, json of the response from the riverid server
	*/
	function register()
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;

		$raw = $this->_curl_req($this->buildURL('/register', array('email' => urlencode($this->email), 'password' => urlencode($this->password))));
		$response = json_decode($raw);

		if ($this->parseResponse($response)) {
			$this->user_id = $response->response;
			return $raw;
		}

		return FALSE;
	}

	/**
	* Sign a user in
	*
	* @return string, json of the response from the riverid server
	*/
	function signin()
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;

		$raw      = $this->_curl_req($this->buildURL('/signin', array('email' => urlencode($this->email), 'password' => urlencode($this->password))));
		$response = json_decode($raw);

		if ($this->parseResponse($response)) {
			// Successful signin, save some variables
			$this->user_id       = $response->response->user_id;
			$this->session_id    = $response->response->session_id;
			$this->authenticated = true;

			return $raw;
		}

		return FALSE;
	}

	/**
	* Resets a users password
	*
	* @param  string $mailbody This is the body of the email to send the user. Use %token% in the string to be replaced with the token they need to enter.
	* @return string, json of the response from the riverid server
	*/
	function requestpassword($mailbody)
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;

		$raw      = $this->_curl_req($this->buildURL('/requestpassword', array('email' => urlencode($this->email), 'mailbody' => urlencode($mailbody))));
		$response = json_decode($raw);

		return $this->parseResponse($response, $raw);
	}

	/**
	* Set forgotten password with a new one
	*
	* @return string, json of the response from the riverid server
	*/
	function setpassword()
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;


		$raw      = $this->_curl_req($this->buildURL('/setpassword', array('email' => urlencode($this->email), 'password' => urlencode($this->new_password), 'token' => $this->token)));
		$response = json_decode($raw);

		if ($this->parseResponse($response)) {
			$this->password = $this->new_password;
			return $raw;
		}

		return FALSE;
	}

	/**
	* Change password when current password is known
	*
	* @return string, json of the response from the riverid server
	*/
	function changepassword()
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;

		$raw      = $this->_curl_req($this->buildURL('/changepassword', array('email' => urlencode($this->email), 'oldpassword' => urlencode($this->password), 'newpassword' => urlencode($this->new_password))));
		$response = json_decode($raw);

		if ($this->parseResponse($response)) {
			$this->password = $this->new_password;
			return $raw;
		}

		return FALSE;
	}

	/**
	* Checks if a password is correct for a user
	*
	* @return string, json of the response from the riverid server
	*/
	function checkpassword()
	{
		// Check for errors first
		if ($this->errors_exist())
			return FALSE;

		$raw      = $this->_curl_req($this->buildURL('/checkpassword', array('email' => urlencode($this->email), 'password' => urlencode($this->password))));
		$response = json_decode($raw);

		return $this->parseResponse($response, $raw);
	}

	function facebookAuthorized($appid, $appsecret, $permissions) {
		$url = $this->endpoint .
			'/facebook_authorized?email=' . $this->email .
			'&session_id=' . $this->session_id .
			'&api_secret=' . $this->api_key .
			'&fb_appid=' . $appid .
			'&fb_secret=' . $appsecret .
			'&fb_scope=' . $permissions;

		$apiResponse = json_decode($this->_curl_req($url));

		if (isset($apiResponse->success) AND $apiResponse->success) {
			return TRUE;
		} else {
			if (isset($apiResponse->response)) {
				return $apiResponse->response;
			}
		}

		return FALSE;
	}

	function facebookAction($appid, $appsecret, $permissions, $namespace, $action, $object, $url, $params = array()) {
		$url = $this->endpoint .
			'/facebook_authorized?email=' . $this->email .
			'&session_id=' . $this->session_id .
			'&api_secret=' . $this->api_key .
			'&fb_appid=' . $appid .
			'&fb_secret=' . $appsecret .
			'&fb_scope=' . $permissions .
			'&fb_namespace=' . $namespace .
			'&fb_action=' . $action .
			'&fb__object=' . $object .
			'&fb_object_url=' . $url;

		if ($params) {
			foreach ($params as $param => $val) {
				$url .= "&fb_graph_{$param}={$val}";
			}
		}

		$apiResponse = json_decode($this->_curl_req($url));

		if (isset($apiResponse->success) AND $apiResponse->success) {
			return TRUE;
		} else {
			if (isset($apiResponse->response)) {
				return $apiResponse->response;
			}
		}

		return FALSE;
	}

	/**
	 * Helper function to send a cURL request
	 * @param url - URL for cURL to hit
	 */
	function _curl_req($url)
	{
		// Make sure cURL is installed
		if ( ! function_exists('curl_exec'))
		{
			throw new Kohana_Exception('stats.cURL_not_installed');
			return FALSE;
		}

		$this->api = curl_init($url);

		$curlFlags = array(
			CURLOPT_URL             => $url,
			CURLOPT_FOLLOWLOCATION  => TRUE,
			CURLOPT_CONNECTTIMEOUT  => 15,
			CURLOPT_RETURNTRANSFER  => 1,
			CURLOPT_SSL_VERIFYPEER  => FALSE
			);

		if (function_exists('curl_setopt_array')) {
			curl_setopt_array($this->api, $curlFlags);
		} else {
			foreach($curlFlags as $flag => $val) {
				curl_setopt($this->api, $flag, $val);
			}
		}

		$buffer = curl_exec($this->api);
		curl_close($this->api);

		return $buffer;
	}

	/**
	* Checks if the server specified in the url exists.
	*
	* @return true, if the server exists; false otherwise
	*/
	function endpoint_exists()
	{
		if (strpos($this->endpoint, '/') === false) {
			$server = $this->endpoint;
		} else {
			$server = @parse_url($this->endpoint, PHP_URL_HOST);
		}

		if ( ! $server) {
			return FALSE;
		}

		return !!gethostbynamel($server);
	}

	/**
	* Checks if there are any errors
	*
	* @return array of errors if they exist, false otherwise
	*/
	function errors_exist()
	{
		if (count($this->error) > 0)
			return $this->error;

		// No errors!
		return FALSE;
	}

	/**
	* Gets the server version
	*
	* @return string, version number
	*/
	function server_version()
	{
		if ($this->server_version == FALSE)
		{
			$this->about();
		}

		return $this->server_version;
	}

	/**
	* Gets the server name
	*
	* @return string, name of the server (ie: CrowdmapID)
	*/
	function server_name()
	{
		if ($this->server_name == FALSE)
		{
			$this->about();
		}

		return $this->server_name;
	}

	/**
	* Gets the server url
	*
	* @return string, url of the server (ie: https://crowdmapid.com)
	*/
	function server_url()
	{
		if ($this->server_url == FALSE)
		{
			$this->about();
		}

		return $this->server_url;
	}
}
