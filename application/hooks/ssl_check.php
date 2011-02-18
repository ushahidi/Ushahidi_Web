<?php defined('SYSPATH') or die('No direct script access.');
/**
 * SSL Check Hook
 * 
 * This hook checks if SSL has been enabled and whether the Webserver is SSL capable
 * If the sanity check fails, $config['site_protocol'] is set back to 'http'
 * and a redirect is performed so as to re-load the URL using the newly set protocol
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   SSL Check Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class ssl_check {
	
    /**
     * Constructor
     */
    public function __construct()
    {
        // Hook into routing
        Event::add_after('system.routing', array('Router', 'find_uri'), array($this, 'verify_ssl_mode'));
    }
	
    /**
     * Verifies if the WebServer is SSL enabled and that the certificate is valid
     * If not, $config['site_protocol'] is set back to 'http' and a redirect is
     * performed
     */
    public function verify_ssl_mode()
    {
    	// Is SSL enabled, check if Web Server is SSL capable
    	$ssl_enabled = (Kohana::config('core.site_protocol') == 'https')? TRUE : FALSE;

    	if ($ssl_enabled)
    	{
            // Initialize session and set cURL
            $ch = curl_init();

            // Set the URL
            curl_setopt($ch, CURLOPT_URL, url::base());

            // Disable following every "Location:" that is sent as part of the HTTP(S) header
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            // Suppress verification of the SSL certificate
            /** 
             * E.Kala - 17th Feb 2011
             * This currently causes an inifinte re-direct loop therefore
             */
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            // Disable checking of the Common Name (CN) in the SSL certificate; Certificate may not be X.509
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

            // Suppress the header in the output
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            
            // Perform cURL session
            curl_exec($ch);
		
            // Check if connection succeeded
            if (curl_errno($ch) == 71)
            {
                // Set the protocol in the config
                Kohana::config_set('core.site_protocol', 'http');

                // Re-write the config file and set $config['site_protocol'] back to HTTP
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
                
                // Redirect using the new site protocol
                url::redirect(url::base().url::current());
            }

            // Close the cURL resource
            curl_close($ch);
            unset($ch);
        }
    }
}

new ssl_check();