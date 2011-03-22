<?php defined('SYSPATH') or die('No direct script access.');
/**
 * HTTPS Check Hook
 * 
 * This hook checks if HTTPS has been enabled and whether the Webserver is HTTPS capable
 * If the sanity check fails, $config['site_protocol'] is set back to 'http'
 * and a redirect is performed so as to re-load the URL using the newly set protocol
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   HTTPS Check Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class https_check {
	
	private $https_enabled;   // Flag to denote whether HTTPS is enabled/disabled
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->https_enabled = FALSE;
        
        // Hook into routing
        Event::add_after('system.routing', array('Router', 'find_uri'), array($this, 'verify_https_mode'));
        Event::add_after('system.routing', array('Router', 'setup'), array($this, 'rewrite_url'));
    }
	
    /**
     * Verifies if the WebServer is HTTPS enabled and that the certificate is valid
     * If not, $config['site_protocol'] is set back to 'http' and a redirect is
     * performed
     */
    public function verify_https_mode()
    {
    	// Is HTTPS enabled, check if Web Server is HTTPS capable
    	$this->https_enabled = (Kohana::config('core.site_protocol') == 'https')? TRUE : FALSE;

    	if ($this->https_enabled)
    	{
            // URL to be used for fetching the headers
            /** 
             * Comments By: E.Kala  - 21/02/2011
             * Not an optimal solution but works for now; index.php with cause get_headers to follow the "Location:"
             * headers and this has an impedance on the page load time
             */
            
            // cURL options
            $curl_options = array(
                CURLOPT_URL => url::base().'media/css/error.css',
                CURLOPT_FOLLOWLOCATION => FALSE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HEADER, FALSE
            );
            
            // Initialize session and set cURL
            $ch = curl_init();
            
            // Set the cURL options in the $curl_options array
            curl_setopt_array($ch, $curl_options);
            
            // Perform cURL session
            curl_exec($ch);
            
            // Get the cURL error no. returned; 71 => Connection failed, 0 => Success, 601=>SSL Cert validation failed
            $curl_error_no = curl_errno($ch);
		    
            // Get the HTTP return code
            $http_return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		    
            // Close the cURL resource
            curl_close($ch);
            unset($ch);
		    
            // Check if connection succeeded or there was an error (except authentication of cert with known CA certificates)
            if (($curl_error_no > 0 AND $curl_error_no != 60) OR $http_return_code == 404)
            {
                // Set the protocol in the config
                Kohana::config_set('core.site_protocol', 'http');

                // Re-write the config file and set $config['site_protocol'] back to 'http'
                $config_file = @file('application/config/config.php');
                $handle = @fopen('application/config/config.php', 'w');
			
                if(is_array($config_file) AND $handle)
                {
                    // Read each line in the file
                    foreach ($config_file as $line_number => $line)
                    {
                        if( strpos(" ".$line,"\$config['site_protocol'] = 'https';") != 0 )
                        {
                            fwrite($handle, str_replace("https","http", $line));
                        }
                        else
                        {
                            fwrite($handle, $line);
                        }
                    }
						
                    // Close the file
                    @fclose($handle);
                }
            }
        }
    }
    
    /**
     * Rewrites the URL depending on whether HTTPS is enabled/disabled
     *
     * NOTES: - Emmanuel Kala, 18th Feb 2011
     * This may bring issues with accessing the API (querying or posting) via mobile and/or external applications
     * as they may not support querying information via HTTPS
     *
     */
    public function rewrite_url()
    {
        $is_https_request = (array_key_exists('HTTPS', $_SERVER) AND $_SERVER['HTTPS'] == 'on')
            ? TRUE 
            : FALSE;
            
        if (($this->https_enabled AND ! $is_https_request) OR ( ! $this->https_enabled AND $is_https_request))
        {
            url::redirect(url::base().url::current().Router::$query_string);
        }
    }
}

new https_check();