<?php
/**
 * Sharing Connect Library
 * Data sharing functions
 * Performs validation and data exchange between Ushahidi Instances
 * 
 * @package    Sharing
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Sharing
{
	private $site_name;
	private $site_email;
	private $site_url;
	private $site_session;
	
	/**
	 * Initiate Sharing Object
	 */
	function __construct()
	{
		$session_cache = new Cache;
		
		$this->site_name = Kohana::config('settings.site_name');
		$this->site_email = Kohana::config('settings.site_email');
		$this->site_url = url::base();
		
		// Set a session for this transaction
		// This session key along with sharing key will be used to 
		// transact with remote server
		$session_key = $session_cache->get('session_key');
		if ( $session_key )
		{ // Session Key already exists
			$this->site_session = $session_key;
		}
		else
		{ // Create a new 15 minute session key
			$session = Session::instance();
			$session_cache->set('session_key', $session->id(), array('session'), 900);
			$this->site_session = $session->id();
		}
	}
	
	
	/**
	 * Notify Remote Site of Share Request
	 *
	 * @param	string url of remote site
	 * @param	string randomly generated sharing key
	 */
	function share_notify($remote_url, $sharing_key, $sharing_action = "notify")
	{
		$request = $this->_curl($remote_url.'/api/'
			, 'POST'
			, array(
				'task' => 'sharing', 
				'type' => $sharing_action,
				'session' => $this->site_session,
				'sharing_key' => $sharing_key,
				'sharing_site_name' => $this->site_name,
				'sharing_email' => $this->site_email,
				'sharing_url' => $this->site_url,
				'sharing_data' => ""
				)
			);
						
		$response = json_decode($request, false);
		if (!$response)
		{
			return FALSE;
		}
		
		if (isset($response->{'payload'}) &&
			$response->{'payload'}->{'success'} == "true")
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}		
	}
	
	
	/**
	 * Register/Edit a Share
	 * @param	string session key of remote site
	 * @param	string key of remote site
	 * @param	string site name of remote site
	 * @param	string email of remote site
	 * @param	string url of remote site
	 */
	function share_edit($sharing_session, $sharing_key, $sharing_site_name, 
			$sharing_email, $sharing_url)
	{	
		// Validate Session
		$validate = $this->_request_validate($sharing_url, $sharing_session);
		if (!$validate)
		{
			return array(
				"success" => FALSE,
				"debug" => "Session Cannot Be Validated"
				);
		}
		
		// Validate Incoming Share Params
		$new_share = array
	    (
			'sharing_site_name' => $sharing_site_name,
			'sharing_email' => $sharing_email,
			'sharing_key' => $sharing_key,
			'sharing_url' => $sharing_url
	    );
		
		$share = new Validation($new_share);
		// Add some filters
        $share->pre_filter('trim', TRUE);
		// Add some rules
		$share->add_rules('sharing_site_name','required', 'length[3,200]');
		$share->add_rules('sharing_url','required', 'url');
		$share->add_rules('sharing_email','required', 'email');
		$share->add_rules('sharing_key','required', 'alpha_numeric', 'length[30,30]');
		
		
		if( $share->validate() )
		{ // Everything is A-Okay!
			// Clean Url
			$sharing_url = $this->_clean_urls($share->sharing_url);

			// First verify that access for this request exists
			$access = ORM::factory('sharing')
				->where('sharing_url', $sharing_url)
				->where('sharing_type', 2)
				->find();

			if (!$access->loaded)
			{ // Does not exist. Create new share
				$access->sharing_type = 2; // Data Push
				$access->sharing_site_name = $share->sharing_site_name;
				$access->sharing_email = $share->sharing_email;
				$access->sharing_url = $sharing_url;
				$access->sharing_key = $share->sharing_key;
				$access->sharing_date = date("Y-m-d H:i:s",time());
				$access->save();

				return array(
					"success" => TRUE,
					"debug" => "New Share Created"
					);
			}
			else
			{ // This Share is already in the DB
				return array(
					"success" => TRUE,
					"debug" => "Share already exists"
					);
			}
		}
		else
		{ // Something does not validate!
			return array(
				"success" => FALSE,
				"debug" => "Submitted items are not valid. Please check items"
				);
		}
	}
	
	
	/**
	 * Remote site has made a request for data
	 * Perform Verification checks and POST requested data back
	 * A data PULL from remote site is not allowed to prevent url/key spoofing
	 *
	 * @param	string session key of remote site
	 * @param	string key of remote site
	 * @param	string site name of remote site
	 * @param	string email of remote site
	 * @param	string url of remote site
	 */
	function share_send($sharing_session, $sharing_key, $sharing_site_name, 
			$sharing_email, $sharing_url)
	{
		// Validate Session
		$validate = $this->_request_validate($sharing_url, $sharing_session);
		if (!$validate)
		{
			return array(
				"success" => FALSE,
				"debug" => "Session Cannot Be Validated"
				);
		}
		
		// Clean Url
		$sharing_url = $this->_clean_urls($sharing_url);
		
		// First verify that access for this request exists
		$access = ORM::factory('sharing')
			->where('sharing_key', $sharing_key)
			->where('sharing_url', $sharing_url)
			->where('sharing_type', 2)
			->where('sharing_active', 1)
			->find();
			
		if ($access->loaded)
		{ // Share Exists and Has Been Activated
			// Get Access Limits
			$sharing_limits = $access->sharing_limits;
			$limit = TRUE;
			if ( $sharing_limits == 1 
				&& (time() - $access->sharing_dateaccess) > 3600 )
			{ // Once Hourly
				$limit = FALSE;
			}
			elseif ( $sharing_limits == 2 
				&& (time() - $access->sharing_dateaccess) > 21600 )
			{ // Once every 6 Hours
				$limit = FALSE;
			}
			elseif ( $sharing_limits == 3 
				&& (time() - $access->sharing_dateaccess) > 43200 )
			{ // Once every 12 Hours
				$limit = FALSE;
			}
			elseif ( $sharing_limits == 4 
				&& (time() - $access->sharing_dateaccess) > 86400 )
			{ // Once daily
				$limit = FALSE;
			}
			
			if(!$limit)
			{ // Sufficient time has passed to allow request
				// PostBack to Instance Requesting Data
				
				$remote = $this->_curl('http://'.$sharing_url.'/api/'
					, 'POST'
					, array(
						'task' => 'sharing', 
						'type' => 'incoming',
						'session' => $this->site_session,
						'sharing_key' => $sharing_key,
						'sharing_site_name' => $this->site_name,
						'sharing_email' => $this->site_email,
						'sharing_url' => $this->site_url,
						'sharing_data' => $this->_build_json()
						)
					);
					
				$response = json_decode($remote, false);
				if (!$response)
				{
					return array(
						"success" => FALSE,
						"debug" => "Transmission Error. Invalid Response Received"
						);
				}

				if (isset($response->{'payload'}) &&
					$response->{'payload'}->{'success'} == "true")
				{
					// Set Access Time
					$access->sharing_dateaccess = time();
					$access->save();
					
					// Log this event
					$log = ORM::factory('sharing_log');
					$log->sharing_id = $access->id;
					$log->sharing_log_date = time();
					$log->save();

					return array(
						"success" => TRUE,
						"debug" => "Data Transmitted Successfully!"
						);
				}
				else
				{
					return array(
						"success" => FALSE,
						"debug" => "Data not Accepted. Null or Invalid data sent"
						);
				}

			}
			else
			{ // Access Denied because of time limit
				return array(
					"success" => FALSE,
					"debug" => "Access Denied - Over Limit. Please try again later"
					);
			}
		}
		else
		{ // Share doesn't Exist or Hasn't Been Activated
			return array(
				"success" => FALSE,
				"debug" => "Share is not active or does not exist"
				);
		}
	}
	
	
	/**
	 * Incoming Data from Remote Site after request has been made
	 *
	 * @param	string session key of remote site
	 * @param	string key of remote site
	 * @param	string site name of remote site
	 * @param	string email of remote site
	 * @param	string url of remote site
	 * @param	string json data from remote site
	 */
	function share_incoming($sharing_session, $sharing_key, $sharing_site_name, 
			$sharing_email, $sharing_url, $sharing_data)
	{
		// Validate Session
		$validate = $this->_request_validate($sharing_url, $sharing_session);
		if (!$validate)
		{
			return array(
				"success" => FALSE,
				"debug" => "Session Cannot Be Validated - ". $sharing_session
				);
		}
		
		// Clean Url
		$sharing_url = $this->_clean_urls($sharing_url);
		
		// First verify that this is a valid request for data
		$access = ORM::factory('sharing')
			->where('sharing_key', $sharing_key)
			->where('sharing_url', $sharing_url)
			->where('sharing_type', 1)
			->where('sharing_active', 1)
			->find();
			
		if ($access->loaded)
		{ // Share Exists and is active on Receiving end
			// Download Data
			$sharing_data = stripslashes($sharing_data);
			
			if (json_decode($sharing_data, false))
			{ // Valid Json				
				// Security Filter (XSS etc...)
				$sharing_data = str_replace("&quot;", "'", $sharing_data);
				$sharing_data = $this->_security($sharing_data);
				
				// Create a cache with no expiration date
				$cache = Cache::instance();
				$cache->set($access->id."_".$sharing_key, $sharing_data, array('sharing'), 0);
				
				// Update the information about the remote site sending data to us
				$access->sharing_site_name = $sharing_site_name;
				$access->sharing_email = $sharing_email;
				$access->save();
				
				// Set Access Time
				$access->sharing_dateaccess = time();
				$access->save();
				
				// Log this event
				$log = ORM::factory('sharing_log');
				$log->sharing_id = $access->id;
				$log->sharing_log_date = time();
				$log->save();
				
				return array(
					"success" => TRUE,
					"debug" => "Data Transmitted Successfully!"
					);
			}
			else
			{ // Json Data is invalid - Does not compute??
				return array(
					"success" => FALSE,
					"debug" => "Invalid JSON Content Received"
					);
			}
		}
		else
		{ // Share doesn't Exist or Has been disabled on Receiver end
			return array(
				"success" => FALSE,
				"debug" => "Share is not active or does not exist"
				);
		}
	}
	
	
	/**
    * Validate Session
	* Remote call sends session variable
	* Remote session variable has to match local session variable
	* or is returned as FALSE
	*
	* @param	string session
    */
	function share_validate($remote_session)
	{
		if ($remote_session && $remote_session == $this->site_session)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
    * Curl request to validate remote Session
    * Remote instance will use share_validate method (above) to match session
	*
	* @param	string sharing_url remote url
	* @param	string session session of remote instance
    */
	private function _request_validate($sharing_url, $sharing_session)
	{
		$request = $this->_curl($sharing_url.'/api/?task=validate&session='.$sharing_session, 'GET', '');
		
		$response = json_decode($request, false);
		if (!$response)
		{
			return FALSE;
		}
		
		if (isset($response->{'payload'}) &&
			$response->{'payload'}->{'success'} == "true")
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	
	/**
    * CURL sending method
    */
	private function _curl($curl_url, $curl_type, $curl_fields)
	{
		$this->_chk_curl();
		if ($curl_type == "POST")
		{
			$curl_handle = curl_init($curl_url);
			curl_setopt($curl_handle,	CURLOPT_POSTFIELDS,$curl_fields);
		}
		else
		{
			$curl_handle = curl_init();
			curl_setopt($curl_handle,	CURLOPT_URL,$curl_url);
		}
		
		curl_setopt($curl_handle, 		CURLOPT_HEADER, FALSE);			// don't return headers
		curl_setopt($curl_handle,		CURLOPT_CONNECTTIMEOUT,60);		// timeout on connect
		curl_setopt($curl_handle,		CURLOPT_TIMEOUT, 60);			// timeout on response
		curl_setopt($curl_handle,		CURLOPT_MAXREDIRS, 10);			// stop after 10 redirects
		curl_setopt($curl_handle,		CURLOPT_RETURNTRANSFER,TRUE);	// return web page
		curl_setopt($curl_handle,		CURLOPT_ENCODING,"");			// handle all encodings
		curl_exec($curl_handle);
		
		$response = curl_exec( $curl_handle );
	    //$err      = curl_errno( $curl_handle );
	    //$errmsg   = curl_error( $curl_handle );
	    //$header   = curl_getinfo( $curl_handle );
		curl_close($curl_handle);
		
		// Check for errors
		/* Not necessary for now
		if ($err == 0)
		{
			// Get HTTP Status code from the response
			$http_code = (isset($header['http_code'])) ? $header['http_code'] : "0";
			if ($http_code == 200 || $http_code == 201)
			{
				return $response;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
		*/
		
		return $response;
	}
	
	
	/**
    * Check for CURL PHP module
    */
    private function _chk_curl() {
        if (!extension_loaded('curl'))
		{
            return FALSE;
        }
    }


	/**
    * Clean Urls
	* We want to standardize urls to prevent duplication
    */
	private function _clean_urls($url)
	{
		// Remove http, https, www
		$remove_array = array('http://www.', 'http://', 'https://', 'https://www.', 'www.');
		
		$url = str_replace($remove_array, "", $url);
		
		// Remove trailing slash/s
		$url = implode("/", array_filter(explode("/", $url)));
		$url = preg_replace('{/$}', '', $url);
		
		return $url;
	}
	
	
	/**
    * Create JSON to send
    */
	private function _build_json()
	{
		$json_item = "";
        $json_array = array();

		// Retrieve all markers
		$markers = ORM::factory('incident')
			->select('DISTINCT incident.*')
			->with('location')
			->join('media', 'incident.id', 'media.incident_id','LEFT')
			->where('incident.incident_active',1)
			->find_all();
			
        foreach ($markers as $marker)
        {	
            $json_item = "{";
            $json_item .= "\"type\":\"Feature\",";
            $json_item .= "\"properties\": {";
			$json_item .= "\"id\": \"".$marker->id."\", \n";
            $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "reports/view/" . $marker->id . "'>" . htmlentities($marker->incident_title) . "</a>")) . "\",";
			$json_item .= "\"category\":[0], ";
			// %%%COLOR%%% will be replaced with share color on receiving end
			$json_item .= "\"color\": \"%%%COLOR%%%\", \n";
			$json_item .= "\"icon\": \"\", \n";
            $json_item .= "\"timestamp\": \"" . strtotime($marker->incident_date) . "\"";
            $json_item .= "},";
            $json_item .= "\"geometry\": {";
            $json_item .= "\"type\":\"Point\", ";
            $json_item .= "\"coordinates\":[" . $marker->location->longitude . ", " . $marker->location->latitude . "]";
            $json_item .= "}";
            $json_item .= "}";

			array_push($json_array, $json_item);
        }


        $json = implode(",", $json_array);
		$json = "{\"type\": \"FeatureCollection\",\"features\": [".$json."]}";
		
		return $json;
	}
	
	
	private function _security($data)
	{
		// Remove malicious javascript
		$data = security::xss_clean($data);
		
		// Remove image tags
		$data = security::strip_image_tags($data);
		
		// Remove php code
		$data = security::encode_php_tags($data);
		
		return $data;
	}
}