<?php
/**
 * HTTP client Implementation based on php-curl
 *
 * @author Henry Addo <henry@addhen.org>
 * @version 1.0
 * @package HttpClient
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class HttpClient_Core {
    
    /**
     * Curl handler
     *
     * @access private
     * @var resource
     */
    private $ch;
    /**
     * Ability to turn debugging info for useful info when 
     * debugging
     *
     * @access private
     * @var string
     */
    private $debug;
    
    /**
     * Holds error messages if error occurs
     *
     * @access private
     * @var string
     */
    private $error_msg;

    /**
     * The Ushahidi URL
     *
     * @access private
     * @var string
     */
    private $url;

    /**
     * Timeout to do a request that is taking forever.
     *
     * @access private
     * @var int
     */
    private $timeout;
    
    public function __construct($url, $timeout=20)
    {
        $this->url = $url;
        $this->timeout = $timeout;
        $this->init_curl();
    }    
    
    /**
     * Initialize a curl session
     *
     * @access private
     */
    private function init_curl()
    {
        //initial curl handle
        $this->ch = curl_init();        
        // set curl's various options        
        
        //set error in case http return code bigger than 300
        curl_setopt($this->ch, CURLOPT_FAILONERROR, TRUE);		
        
        // allow redirects just incase a user wants that
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, TRUE);		
        
        // use gzip if possible for performance
        curl_setopt($this->ch,CURLOPT_ENCODING , 'gzip, deflate');
        
        // do not veryfy ssl for 
		// as well for being able to access pages with non valid cert
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST,  2);
    }    
        
    /**
     * Set client's user agent
     *
     * @access private
     * @param string useragent
     */
    public function set_useragent($useragent)
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT, $useragent);
    }    
        
    /**
     * Get http response code
     *
	 * @access private
	 * @return int
	 */
	public function get_http_response_code()
	{
		return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }    
    
    /**
     * Set error message that might show up
     *
     * @access protected
     * @param string error_msg - The error message
     */
    public function get_error_msg()
    {
       return $this->error_msg;
    }	
    
    /**
     * Return last error message and error number
     *
     * @access 	private 
     * @return string - Error msg
	 */
	public function set_error_msg()
	{
		$err = "Error number: " .curl_errno($this->ch) ."\n";
        $err .= "Error message: " .curl_error($this->ch)."\n";
        $this->error_msg .= $err;

        return $this->error_msg;
	}
	
	/**
	 * Close curl session and free resource
	 * Usually no need to call this function directly
     * in case you do you have to call init() to recreate curl
     *
	 * @access private
	 */
	private function close()
	{
		//close curl session and free up resources
		curl_close($this->ch);
    }

    /**
     * Setup ip interface to curl and timeouts for curl. Shows all debugging and error info
     * if there are any
     *
     * @access private
     *  
     * @param string qry_string
     * @param string ip address to bind (default null)
     * @param int timeout in sec for complete curl operation (default 10)
     *
     */
    private function prepare_curl()
    {

        //set various curl options first
		curl_setopt($this->ch, CURLOPT_URL,$this->url);

		// return into a variable rather than displaying it
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,TRUE);

		//set curl function timeout to $timeout
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);


    }

    /** 
     * Fetch data from target URL return data returned from url or false if error occured
     *
     * @access proctected
     *
	 * @param string getdata - The query data to pass to the url	 
	 * @param string ip address to bind (default null)
	 * @param int timeout in sec for complete curl operation (default 5)
	 * @return string data
	 */
	public function execute()
    {
        $this->prepare_curl(); 
		//set method to get

		//and finally send curl request
		$result = curl_exec($this->ch);

		if (curl_errno($this->ch))
        {
            $this->set_error_msg();
            $this->close();

			return FALSE;
		}
		
        $this->close();
	    return $result;
		
	}
    
}

?>
