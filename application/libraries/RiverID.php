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

			$url = $this->endpoint.'/about';
			$about_response = $this->_curl_req($url);
			$about = json_decode($about_response);

			if ($about->success)
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

		$url = $this->endpoint.'/registered?email='.$this->email.'&api_secret='.$this->api_key;
		return $this->_curl_req($url);
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

		$registered = json_decode($this->registered());

		if ($registered->success == true)
		{
			return $registered->response;
		}
		else
		{
			$this->error[] = $registered->error;
			return FALSE;
		}
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

		$url = $this->endpoint.'/register?email='.$this->email.'&password='.$this->password.'&api_secret='.$this->api_key;
		$register_response = $this->_curl_req($url);
		$register = json_decode($register_response);

		if ($register->success == true)
		{
			// Successful creation, save the user_id
			$this->user_id = $register->response;
		}
		else
		{
			$this->error[] = $register->error;
			return FALSE;
		}

		return $register_response;
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

		$url = $this->endpoint.'/signin?email='.$this->email.'&password='.$this->password.'&api_secret='.$this->api_key;
		$signin_response = $this->_curl_req($url);
		$signin = json_decode($signin_response);

		if ($signin->success)
		{
			// Successful signin, save some variables
			$this->user_id = $signin->response->user_id;
			$this->session_id = $signin->response->session_id;
			$this->authenticated = true;
		}
		else
		{
			// Failed signin
			$this->error[] = $signin->error;
			return FALSE;
		}

		return $signin_response;
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

		$url = $this->endpoint.'/requestpassword?email='.$this->email.'&mailbody='.urlencode($mailbody).'&api_secret='.$this->api_key;
		$requestpassword_response = $this->_curl_req($url);

		$requestpassword = json_decode($requestpassword_response);

		if ($requestpassword->success)
		{
			// Successful Password Reset
		}
		else
		{
			$this->error[] = $requestpassword->error;
			return FALSE;
		}

		return $requestpassword_response;
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

		$url = $this->endpoint.'/setpassword?email='.$this->email.'&password='.$this->new_password.'&token='.$this->token.'&api_secret='.$this->api_key;
		$setpassword_response = $this->_curl_req($url);
		$setpassword = json_decode($setpassword_response);

		if ($setpassword->success)
		{
			// Successful Set Password

			// Since this was a success, save that password as the newly set one
			$this->password = $this->new_password;
		}
		else
		{
			$this->error[] = $setpassword->error;
			return FALSE;
		}

		return $setpassword_response;
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

		$url = $this->endpoint.'/changepassword?email='.$this->email.'&oldpassword='.$this->password.'&newpassword='.$this->new_password.'&api_secret='.$this->api_key;
		$changepassword_response = $this->_curl_req($url);
		$changepassword = json_decode($changepassword_response);

		if ($changepassword->success)
		{
			// Successful Password Change

			// Since this was a success, save that password as the newly set one
			$this->password = $this->new_password;
		}
		else
		{
			$this->error[] = $changepassword->error;
			return FALSE;
		}

		return $changepassword_response;
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

		$url = $this->endpoint.'/checkpassword?email='.$this->email.'&password='.$this->password.'&api_secret='.$this->api_key;
		$checkpassword_response = $this->_curl_req($url);
		$checkpassword = json_decode($checkpassword_response);

		if ($checkpassword->success && $checkpassword->response)
		{
			// Successful Checking of Password
		}
		else
		{
			$this->error[] = 'Incorrect password provided.';
			return FALSE;
		}

		return $checkpassword_response;
	}

	public function facebookAuthorized($appid, $appsecret, $permissions) {
		$url = $this->endpoint .
			'/facebook_authorized?email=' . $this->email .
			'&session_id=' . $this->session_id .
			'&api_secret=' . $this->api_key .
			'&fb_appid=' . $appid .
			'&fb_secret=' . $appsecret .
			'&fb_scope=' . $permissions;

		$apiResponse = json_decode($this->_curl_req($url));

		if(isset($apiResponse->success) AND $apiResponse->success) {
			return TRUE;
		} else {
			if(isset($apiResponse->response)) return $apiResponse->response;
			return FALSE;
		}
	}

	public function facebookAction($appid, $appsecret, $permissions, $namespace, $action, $object, $url, $params = array()) {
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

		if($params) {
			foreach($params as $param => $val) {
				$url .= "&fb_graph_{$param}={$val}";
			}
		}

		$apiResponse = json_decode($this->_curl_req($url));

		if(isset($apiResponse->success) && $apiResponse->success) {
			return TRUE;
		} else {
			if(isset($apiResponse->response)) return $apiResponse->response;
			return FALSE;
		}
	}

	/**
     * Helper function to send a cURL request
     * @param url - URL for cURL to hit
     */
    public function _curl_req($url)
    {
        // Make sure cURL is installed
        if ( ! function_exists('curl_exec'))
        {
            throw new Kohana_Exception('stats.cURL_not_installed');
            return FALSE;
        }

        $curl_handle = curl_init();
        curl_setopt($curl_handle,CURLOPT_URL,$url);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,15); // Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
        curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); //Set curl to store data in variable instead of print
        curl_setopt($curl_handle,CURLOPT_SSL_VERIFYPEER, false);
        $buffer = curl_exec($curl_handle);
        curl_close($curl_handle);

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
