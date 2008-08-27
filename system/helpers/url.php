<?php defined('SYSPATH') or die('No direct script access.');
/**
 * URL helper class.
 *
 * $Id: url.php 3238 2008-07-30 15:42:28Z Shadowhand $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class url_Core {

	/**
	 * Fetches the current URI.
	 *
	 * @param   boolean  include the query string
	 * @return  string
	 */
	public static function current($qs = FALSE)
	{
		return ($qs === TRUE) ? Router::$complete_uri : Router::$current_uri;
	}

	/**
	 * Base URL, with or without the index page.
	 *
	 * If protocol (and core.site_protocol) and core.site_domain are both empty,
	 * then
	 *
	 * @param   boolean  include the index page
	 * @param   boolean  non-default protocol
	 * @return  string
	 */
	public static function base($index = FALSE, $protocol = FALSE)
	{
		if ($protocol == FALSE)
		{
			// Use the default configured protocol
			$protocol = Kohana::config('core.site_protocol');
		}

		// Load the site domain
		$site_domain = (string) Kohana::config('core.site_domain', TRUE);

		if ($protocol == FALSE)
		{
			if ($site_domain === '' OR $site_domain[0] === '/')
			{
				// Use the configured site domain
				$base_url = $site_domain;
			}
			else
			{
				// Guess the protocol to provide full http://domain/path URL
				$base_url = ((empty($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] === 'off') ? 'http' : 'https').'://'.$site_domain;
			}
		}
		else
		{
			if ($site_domain === '' OR $site_domain[0] === '/')
			{
				// Guess the server name if the domain starts with slash
				$base_url = $protocol.'://'.$_SERVER['HTTP_HOST'].$site_domain;
			}
			else
			{
				// Use the configured site domain
				$base_url = $protocol.'://'.$site_domain;
			}
		}

		if ($index === TRUE AND $index = Kohana::config('core.index_page'))
		{
			// Append the index page
			$base_url = $base_url.$index;
		}

		// Force a slash on the end of the URL
		return rtrim($base_url, '/').'/';
	}

	/**
	 * Fetches an absolute site URL based on a URI segment.
	 *
	 * @param   string  site URI to convert
	 * @param   string  non-default protocol
	 * @return  string
	 */
	public static function site($uri = '', $protocol = FALSE)
	{
		if ($path = trim(parse_url($uri, PHP_URL_PATH), '/'))
		{
			// Add path suffix
			$path .= Kohana::config('core.url_suffix');
		}

		if ($query = parse_url($uri, PHP_URL_QUERY))
		{
			// ?query=string
			$query = '?'.$query;
		}

		if ($fragment = parse_url($uri, PHP_URL_FRAGMENT))
		{
			// #fragment
			$fragment =  '#'.$fragment;
		}

		// Concat the URL
		return url::base(TRUE, $protocol).$path.$query.$fragment;
	}

	/**
	 * Return the URL to a file. Absolute filenames and relative filenames
	 * are allowed.
	 *
	 * @param   string   filename
	 * @param   boolean  include the index page
	 * @return  string
	 */
	public static function file($file, $index = FALSE)
	{
		if (strpos($file, '://') === FALSE)
		{
			// Add the base URL to the filename
			$file = url::base($index).$file;
		}

		return $file;
	}

	/**
	 * Merges an array of arguments with the current URI and query string to
	 * overload, instead of replace, the current query string.
	 *
	 * @param   array   associative array of arguments
	 * @return  string
	 */
	public static function merge(array $arguments)
	{
		if ($_GET === $arguments)
		{
			$query = Router::$query_string;
		}
		elseif ($query = http_build_query(array_merge($_GET, $arguments)))
		{
			$query = '?'.$query;
		}

		// Return the current URI with the arguments merged into the query string
		return Router::$current_uri.$query;
	}

	/**
	 * Convert a phrase to a URL-safe title.
	 *
	 * @param   string  phrase to convert
	 * @param   string  word separator (- or _)
	 * @return  string
	 */
	public static function title($title, $separator = '-')
	{
		$separator = ($separator === '-') ? '-' : '_';

		// Replace accented characters by their unaccented equivalents
		$title = utf8::transliterate_to_ascii($title);

		// Remove all characters that are not the separator, a-z, 0-9, or whitespace
		$title = preg_replace('/[^'.$separator.'a-z0-9\s]+/', '', strtolower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('/['.$separator.'\s]+/', $separator, $title);

		// Trim separators from the beginning and end
		return trim($title, $separator);
	}

	/**
	 * Sends a page redirect header.
	 *
	 * @param  mixed   string site URI or URL to redirect to, or array of strings if method is 300
	 * @param  string  HTTP method of redirect
	 * @return void
	 */
	public static function redirect($uri = '', $method = '302')
	{
		if (Event::has_run('system.send_headers'))
			return;

		$uri = (array) $uri;

		for ($i = 0, $count_uri = count($uri); $i < $count_uri; $i++)
		{
			if (strpos($uri[$i], '://') === FALSE)
			{
				$uri[$i] = url::site($uri[$i]);
			}
		}

		if ($method == '300')
		{
			if ($count_uri > 0)
			{
				header('HTTP/1.1 300 Multiple Choices');
				header('Location: '.$uri[0]);

				$choices = '';
				foreach ($uri as $href)
				{
					$choices .= '<li><a href="'.$href.'">'.$href.'</a></li>';
				}

				exit('<h1>301 - Multiple Choices:</h1><ul>'.$choices.'</ul>');
			}
		}
		else
		{
			$uri = $uri[0];

			if ($method == 'refresh')
			{
				header('Refresh: 0; url='.$uri);
			}
			else
			{
				$codes = array
				(
					'301' => 'Moved Permanently',
					'302' => 'Found',
					'303' => 'See Other',
					'304' => 'Not Modified',
					'305' => 'Use Proxy',
					'307' => 'Temporary Redirect'
				);

				$method = isset($codes[$method]) ? $method : '302';

				header('HTTP/1.1 '.$method.' '.$codes[$method]);
				header('Location: '.$uri);
			}

			exit('<h1>'.$method.' - '.$codes[$method].'</h1><p><a href="'.$uri.'">'.$uri.'</a></p>');
		}
	}

} // End url