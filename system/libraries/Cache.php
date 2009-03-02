<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Provides a driver-based interface for finding, creating, and deleting cached
 * resources. Caches are identified by a unique string. Tagging of caches is
 * also supported, and caches can be found and deleted by id or tag.
 *
 * $Id: Cache.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_Core {

	// For garbage collection
	protected static $loaded;

	// Configuration
	protected $config;

	// Driver object
	protected $driver;

	/**
	 * Returns a singleton instance of Cache.
	 *
	 * @param   array  configuration
	 * @return  Cache_Core
	 */
	public static function instance($config = array())
	{
		static $obj;

		// Create the Cache instance
		($obj === NULL) and $obj = new Cache($config);

		return $obj;
	}

	/**
	 * Loads the configured driver and validates it.
	 *
	 * @param   array|string  custom configuration or config group name
	 * @return  void
	 */
	public function __construct($config = FALSE)
	{
		if (is_string($config))
		{
			$name = $config;

			// Test the config group name
			if (($config = Kohana::config('cache.'.$config)) === NULL)
				throw new Kohana_Exception('cache.undefined_group', $name);
		}

		if (is_array($config))
		{
			// Append the default configuration options
			$config += Kohana::config('cache.default');
		}
		else
		{
			// Load the default group
			$config = Kohana::config('cache.default');
		}

		// Cache the config in the object
		$this->config = $config;

		// Set driver name
		$driver = 'Cache_'.ucfirst($this->config['driver']).'_Driver';

		// Load the driver
		if ( ! Kohana::auto_load($driver))
			throw new Kohana_Exception('core.driver_not_found', $this->config['driver'], get_class($this));

		// Initialize the driver
		$this->driver = new $driver($this->config['params']);

		// Validate the driver
		if ( ! ($this->driver instanceof Cache_Driver))
			throw new Kohana_Exception('core.driver_implements', $this->config['driver'], get_class($this), 'Cache_Driver');

		Kohana::log('debug', 'Cache Library initialized');

		if (self::$loaded !== TRUE)
		{
			$this->config['requests'] = (int) $this->config['requests'];

			if ($this->config['requests'] > 0 AND mt_rand(1, $this->config['requests']) === 1)
			{
				// Do garbage collection
				$this->driver->delete_expired();

				Kohana::log('debug', 'Cache: Expired caches deleted.');
			}

			// Cache has been loaded once
			self::$loaded = TRUE;
		}
	}

	/**
	 * Fetches a cache by id. Non-string cache items are automatically
	 * unserialized before the cache is returned. NULL is returned when
	 * a cache item is not found.
	 *
	 * @param   string  cache id
	 * @return  mixed   cached data or NULL
	 */
	public function get($id)
	{
		// Change slashes to colons
		$id = str_replace(array('/', '\\'), '=', $id);

		if ($data = $this->driver->get($id))
		{
			if (substr($data, 0, 14) === '<{serialized}>')
			{
				// Data has been serialized, unserialize now
				$data = unserialize(substr($data, 14));
			}
		}

		return $data;
	}

	/**
	 * Fetches all of the caches for a given tag. An empty array will be
	 * returned when no matching caches are found.
	 *
	 * @param   string  cache tag
	 * @return  array   all cache items matching the tag
	 */
	public function find($tag)
	{
		if ($ids = $this->driver->find($tag))
		{
			$data = array();
			foreach ($ids as $id)
			{
				// Load each cache item and add it to the array
				if (($cache = $this->get($id)) !== NULL)
				{
					$data[$id] = $cache;
				}
			}

			return $data;
		}

		return array();
	}

	/**
	 * Set a cache item by id. Tags may also be added and a custom lifetime
	 * can be set. Non-string data is automatically serialized.
	 *
	 * @param   string   unique cache id
	 * @param   mixed    data to cache
	 * @param   array    tags for this item
	 * @param   integer  number of seconds until the cache expires
	 * @return  boolean
	 */
	function set($id, $data, $tags = NULL, $lifetime = NULL)
	{
		if (is_resource($data))
			throw new Kohana_Exception('cache.resources');

		// Change slashes to colons
		$id = str_replace(array('/', '\\'), '=', $id);

		if ( ! is_string($data))
		{
			// Serialize all non-string data, so that types can be preserved
			$data = '<{serialized}>'.serialize($data);
		}

		// Make sure that tags is an array
		$tags = empty($tags) ? array() : (array) $tags;

		if ($lifetime === NULL)
		{
			// Get the default lifetime
			$lifetime = $this->config['lifetime'];
		}

		return $this->driver->set($id, $data, $tags, $lifetime);
	}

	/**
	 * Delete a cache item by id.
	 *
	 * @param   string   cache id
	 * @return  boolean
	 */
	public function delete($id)
	{
		// Change slashes to colons
		$id = str_replace(array('/', '\\'), '=', $id);

		return $this->driver->delete($id);
	}

	/**
	 * Delete all cache items with a given tag.
	 *
	 * @param   string   cache tag name
	 * @return  boolean
	 */
	public function delete_tag($tag)
	{
		return $this->driver->delete(FALSE, $tag);
	}

	/**
	 * Delete ALL cache items items.
	 *
	 * @return  boolean
	 */
	public function delete_all()
	{
		return $this->driver->delete(TRUE);
	}

} // End Cache
