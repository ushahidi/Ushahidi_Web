<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Remote url helper, extends generic remote_Core
 *
 * @package    Core
 * @author     zextra <zextra@gmail.com>
 * @license    http://kohanaphp.com/license.html
 */
class remote extends remote_Core {
	
	/**
	 * Shorthand method to GET data from remote url
	 *
	 * @param string $url
	 * @param array $headers
	 * @return HTTP_Response
	 */
	public static function get($url, $headers = array())
	{
		return self::request('GET', $url, $headers);
	}
	
	/**
	 * Shorthand method to POST data to remote url
	 *
	 * @param string $url
	 * @param array $data
	 * @param array $headers
	 * @return HTTP_Response
	 */
	public static function post($url, $data = array(), $headers = array())
	{
		return self::request('POST', $url, $headers, $data);
	}

	/**
	 * Method that allows sending any kind of HTTP request to remote url
	 *
	 * @param string $method
	 * @param string $url
	 * @param array $headers
	 * @param array $data
	 * @return HTTP_Response
	 */
	public static function request($method, $url, $headers = array(), $data = array())
	{
		$valid_methods = array('POST', 'GET', 'PUT', 'DELETE');
		
		$method = utf8::strtoupper($method);
		
		if ( ! valid::url($url, 'http'))
			return FALSE;
		
		if ( ! in_array($method, $valid_methods))
			return FALSE;

		// Get the hostname and path
		$url = parse_url($url);

		if (empty($url['path']))
		{
			// Request the root document
			$url['path'] = '/';
		}

		// Open a remote connection
		$remote = fsockopen($url['host'], 80, $errno, $errstr, 5);

		if ( ! is_resource($remote))
			return FALSE;

		// Set CRLF
		$CRLF = "\r\n";
		
		$path = $url['path'];
		
		if ($method == 'GET' AND ! empty($url['query']))
			$path .= '?'.$url['query'];
			
		$headers_default = array(
			'Host' => $url['host'],
			'Connection' => 'close',
			'User-Agent' => 'Ushahidi Scheduler (+http://ushahidi.com/)',
		);
		
		$body_content = '';
		
		if ($method != 'GET')
		{
			$headers_default['Content-Type'] = 'application/x-www-form-urlencoded';
			if (count($data) > 0)
			{
				$body_content = http_build_query($data);
			}
			$headers_default['Content-Length'] = strlen($body_content);
		}
		
		$headers = array_merge($headers_default, $headers);

		// Send request
		$request = $method.' '.$path.' HTTP/1.0'.$CRLF;
		
		foreach ($headers as $key => $value)
		{
			$request .= $key.': '.$value.$CRLF;
		}

		// Send one more CRLF to terminate the headers
		$request .= $CRLF;
		
		if ($body_content)
		{
			$request .= $body_content.$CRLF;
		}
		
		fwrite($remote, $request);

		$response = '';
		
		while ( ! feof($remote))
		{
			// Get 1K from buffer
			$response .= fread($remote, 1024);
        }

		// Close the connection
        fclose($remote);

        return new HTTP_Response($response, $method);
	}

}

/**
 * Very simple class that handles raw response received from remote host
 *
 * @package Core
 */
class HTTP_Response {
	/**
	 * HTTP method
	 *
	 * @var string
	 */
    protected $method;
    
    /**
     * HTTP status code
     *
     * @var integer
     */
    protected $status;
    
    /**
     * Complete request, as received from remote host
     *
     * @var string
     */
    protected $response;
    
    /**
     * Headers received from remote host
     *
     * @var string
     */
    protected $headers;
    
    /**
     * Body received from remote host
     *
     * @var string
     */
    protected $body;

    public function __construct($full_response, $method)
    {
        $this->method = $method;
        $this->response = $full_response;
        $this->parse();
    }

    /**
     * Splits $this->response into header and body, also extract status code
     *
     * @return void
     */
    protected function parse()
    {
		// split response by newlines and detect first empty line (between header and body)
        $lines = explode("\n", $this->response);

        $headers = array();
		
		foreach ($lines as $line)
		{
			// each time we take one line, we will remove that line, until we find empty one
            $headers[] = array_shift($lines);

            if ($line !== '' AND preg_match('#^HTTP/1\.[01] (\d{3})#', $line, $matches))
            {
                // Response code found
                $this->status = (int) $matches[1];
            }

			if ($line === "\r" or $line === "")
			{
				break;
            }
		}

        $this->headers = trim(implode("\n", $headers));
		$this->body = implode("\n", $lines);
    }

    /**
     * Returns only body() if object is stringified
     *
     * @return unknown
     */
    public function __toString()
    {
        return (string) $this->body();
    }

    /**
     * Basic getter for class members
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args)
    {
        if (isset($this->$method) AND count($args) == 0)
        {
            return $this->$method;
        }
    }
}

