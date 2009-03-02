<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Xcache Cache driver.
 *
 * $Id: Xcache.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_Xcache_Driver implements Cache_Driver {

	public function __construct()
	{
		if ( ! extension_loaded('xcache'))
			throw new Kohana_Exception('cache.extension_not_loaded', 'xcache');
	}

	public function get($id)
	{
		if (xcache_isset($id))
			return xcache_get($id);

		return NULL;
	}

	public function set($id, $data, $tags, $lifetime)
	{
		count($tags) and Kohana::log('error', 'Cache: tags are unsupported by the Xcache driver');

		return xcache_set($id, $data, $lifetime);
	}

	public function find($tag)
	{
		Kohana::log('error', 'Cache: tags are unsupported by the Xcache driver');
		return FALSE;
	}

	public function delete($id, $tag = FALSE)
	{
		if ($tag !== FALSE)
		{
			Kohana::log('error', 'Cache: tags are unsupported by the Xcache driver');
			return TRUE;
		}
		elseif ($id !== TRUE)
		{
			if (xcache_isset($id))
				return xcache_unset($id);

			return FALSE;
		}
		else
		{
			// Do the login
			$this->auth();
			$result = TRUE;
			for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++)
			{
				if (xcache_clear_cache(XC_TYPE_VAR, $i) !== NULL)
				{
					$result = FALSE;
					break;
				}
			}

			// Undo the login
			$this->auth(TRUE);
			return $result;
		}

		return TRUE;
	}

	public function delete_expired()
	{
		return TRUE;
	}

	private function auth($reverse = FALSE)
	{
		static $backup = array();

		$keys = array('PHP_AUTH_USER', 'PHP_AUTH_PW');

		foreach ($keys as $key)
		{
			if ($reverse)
			{
				if (isset($backup[$key]))
				{
					$_SERVER[$key] = $backup[$key];
					unset($backup[$key]);
				}
				else
				{
					unset($_SERVER[$key]);
				}
			}
			else
			{
				$value = getenv($key);

				if ( ! empty($value))
				{
					$backup[$key] = $value;
				}

				$_SERVER[$key] = Kohana::config('cache_xcache.'.$key);
			}
		}
	}

} // End Cache Xcache Driver
