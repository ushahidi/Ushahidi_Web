<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Remote url/file helper.
 *
 * $Id: remote.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class remote_Core {

	public static function status($url)
	{
		if ( ! valid::url($url, 'http'))
			return FALSE;

		// Get the hostname and path
		$url = parse_url($url);

		if (empty($url['path']))
		{
			// Request the root document
			$url['path'] = '/';
		}

		// Open a remote connection
		if(isset($_SERVER["SERVER_PORT"])) {
			$server_port = $_SERVER["SERVER_PORT"];
		} else {
			$server_port = '80';
		}
		$remote = fsockopen($url['host'], $server_port, $errno, $errstr, 5);

		if ( ! is_resource($remote))
			return FALSE;

		// Set CRLF
		$CRLF = "\r\n";

		// Send request
		fwrite($remote, 'HEAD '.$url['path'].' HTTP/1.0'.$CRLF);
		fwrite($remote, 'Host: '.$url['host'].$CRLF);
		fwrite($remote, 'Connection: close'.$CRLF);
		fwrite($remote, 'User-Agent: Kohana Framework (+http://kohanaphp.com/)'.$CRLF);

		// Send one more CRLF to terminate the headers
		fwrite($remote, $CRLF);

		while ( ! feof($remote))
		{
			// Get the line
			$line = trim(fgets($remote, 512));

			if ($line !== '' AND preg_match('#^HTTP/1\.[01] (\d{3})#', $line, $matches))
			{
				// Response code found
				$response = (int) $matches[1];

				break;
			}
		}

		// Close the connection
		fclose($remote);

		return isset($response) ? $response : FALSE;
	}

} // End remote