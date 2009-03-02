<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * SQLite-based Cache driver.
 *
 * $Id: Sqlite.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_Sqlite_Driver implements Cache_Driver {

	// SQLite database instance
	protected $db;

	// Database error messages
	protected $error;

	/**
	 * Logs an SQLite error.
	 */
	protected static function log_error($code)
	{
		// Log an error
		Kohana::log('error', 'Cache: SQLite error: '.sqlite_error_string($error));
	}

	/**
	 * Tests that the storage location is a directory and is writable.
	 */
	public function __construct($filename)
	{
		// Get the directory name
		$directory = str_replace('\\', '/', realpath(pathinfo($filename, PATHINFO_DIRNAME))).'/';

		// Set the filename from the real directory path
		$filename = $directory.basename($filename);

		// Make sure the cache directory is writable
		if ( ! is_dir($directory) OR ! is_writable($directory))
			throw new Kohana_Exception('cache.unwritable', $directory);

		// Make sure the cache database is writable
		if (is_file($filename) AND ! is_writable($filename))
			throw new Kohana_Exception('cache.unwritable', $filename);

		// Open up an instance of the database
		$this->db = new SQLiteDatabase($filename, '0666', $error);

		// Throw an exception if there's an error
		if ( ! empty($error))
			throw new Kohana_Exception('cache.driver_error', sqlite_error_string($error));

		$query  = "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'caches'";
		$tables = $this->db->query($query, SQLITE_BOTH, $error);

		// Throw an exception if there's an error
		if ( ! empty($error))
			throw new Kohana_Exception('cache.driver_error', sqlite_error_string($error));

		if ($tables->numRows() == 0)
		{
			Kohana::log('error', 'Cache: Initializing new SQLite cache database');

			// Issue a CREATE TABLE command
			$this->db->unbufferedQuery(Kohana::config('cache_sqlite.schema'));
		}
	}

	/**
	 * Checks if a cache id is already set.
	 *
	 * @param  string   cache id
	 * @return boolean
	 */
	public function exists($id)
	{
		// Find the id that matches
		$query = "SELECT id FROM caches WHERE id = '$id'";

		return ($this->db->query($query)->numRows() > 0);
	}

	/**
	 * Sets a cache item to the given data, tags, and lifetime.
	 *
	 * @param   string   cache id to set
	 * @param   string   data in the cache
	 * @param   array    cache tags
	 * @param   integer  lifetime
	 * @return  bool
	 */
	public function set($id, $data, $tags, $lifetime)
	{
		// Find the data hash
		$hash = sha1($data);

		// Escape the data
		$data = sqlite_escape_string($data);

		// Escape the tags
		$tags = sqlite_escape_string(implode(',', $tags));

		// Cache Sqlite driver expects unix timestamp
		if ($lifetime !== 0)
		{
			$lifetime += time();
		}

		$query = $this->exists($id)
			? "UPDATE caches SET hash = '$hash', tags = '$tags', expiration = '$lifetime', cache = '$data' WHERE id = '$id'"
			: "INSERT INTO caches VALUES('$id', '$hash', '$tags', '$lifetime', '$data')";

		// Run the query
		$this->db->unbufferedQuery($query, SQLITE_BOTH, $error);

		empty($error) or self::log_error($error);

		return empty($error);
	}

	/**
	 * Finds an array of ids for a given tag.
	 *
	 * @param  string  tag name
	 * @return array   of ids that match the tag
	 */
	public function find($tag)
	{
		$query = "SELECT id FROM caches WHERE tags LIKE '%{$tag}%'";
		$query = $this->db->query($query, SQLITE_BOTH, $error);

		empty($error) or self::log_error($error);

		if (empty($error) AND $query->numRows() > 0)
		{
			$array = array();
			while ($row = $query->fetchObject())
			{
				// Add each id to the array
				$array[] = $row->id;
			}
			return $array;
		}

		return FALSE;
	}

	/**
	 * Fetches a cache item. This will delete the item if it is expired or if
	 * the hash does not match the stored hash.
	 *
	 * @param  string  cache id
	 * @return mixed|NULL
	 */
	public function get($id)
	{
		$query = "SELECT id, hash, expiration, cache FROM caches WHERE id = '{$id}' LIMIT 0, 1";
		$query = $this->db->query($query, SQLITE_BOTH, $error);

		empty($error) or self::log_error($error);

		if (empty($error) AND $cache = $query->fetchObject())
		{
			// Make sure the expiration is valid and that the hash matches
			if (($cache->expiration != 0 AND $cache->expiration <= time()) OR $cache->hash !== sha1($cache->cache))
			{
				// Cache is not valid, delete it now
				$this->delete($cache->id);
			}
			else
			{
				// Return the valid cache data
				return $cache->cache;
			}
		}

		// No valid cache found
		return NULL;
	}

	/**
	 * Deletes a cache item by id or tag
	 *
	 * @param  string  cache id or tag, or TRUE for "all items"
	 * @param  bool    use tags
	 * @return bool
	 */
	public function delete($id, $tag = FALSE)
	{
		if ($id === TRUE)
		{
			// Delete all caches
			$where = '1';
		}
		elseif ($tag == FALSE)
		{
			// Delete by id
			$where = "id = '{$id}'";
		}
		else
		{
			// Delete by tag
			$where = "tags LIKE '%{$tag}%'";
		}

		$this->db->unbufferedQuery('DELETE FROM caches WHERE '.$where, SQLITE_BOTH, $error);

		empty($error) or self::log_error($error);

		return empty($error);
	}

	/**
	 * Deletes all cache files that are older than the current time.
	 */
	public function delete_expired()
	{
		// Delete all expired caches
		$query = 'DELETE FROM caches WHERE expiration != 0 AND expiration <= '.time();

		$this->db->unbufferedQuery($query);

		return TRUE;
	}

} // End Cache SQLite Driver