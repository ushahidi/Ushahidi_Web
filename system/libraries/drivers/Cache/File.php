<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * File-based Cache driver.
 *
 * $Id: File.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_File_Driver implements Cache_Driver {

	protected $directory = '';

	/**
	 * Tests that the storage location is a directory and is writable.
	 */
	public function __construct($directory)
	{
		// Find the real path to the directory
		$directory = str_replace('\\', '/', realpath($directory)).'/';

		// Make sure the cache directory is writable
		if ( ! is_dir($directory) OR ! is_writable($directory))
			throw new Kohana_Exception('cache.unwritable', $directory);

		// Directory is valid
		$this->directory = $directory;
	}

	/**
	 * Finds an array of files matching the given id or tag.
	 *
	 * @param  string  cache id or tag
	 * @param  bool    search for tags
	 * @return array   of filenames matching the id or tag
	 * @return void    if no matching files are found
	 */
	public function exists($id, $tag = FALSE)
	{
		if ($id === TRUE)
		{
			// Find all the files
			$files = glob($this->directory.'*~*~*');
		}
		elseif ($tag == TRUE)
		{
			// Find all the files that have the tag name
			$files = glob($this->directory.'*~*'.$id.'*~*');

			// Find all tags matching the given tag
			foreach ($files as $i => $file)
			{
				// Split the files
				$tags = explode('~', $file);

				// Find valid tags
				if (count($tags) !== 3 OR empty($tags[1]))
					continue;

				// Split the tags by plus signs, used to separate tags
				$tags = explode('+', $tags[1]);

				if ( ! in_array($tag, $tags))
				{
					// This entry does not match the tag
					unset($files[$i]);
				}
			}
		}
		else
		{
			// Find all the files matching the given id
			$files = glob($this->directory.$id.'~*');
		}

		return empty($files) ? NULL : $files;
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
		// Remove old cache files
		$this->delete($id);

		// Cache File driver expects unix timestamp
		if ($lifetime !== 0)
		{
			$lifetime += time();
		}

		// Construct the filename
		$filename = $id.'~'.implode('+', $tags).'~'.$lifetime;

		// Write the file, appending the sha1 signature to the beginning of the data
		return (bool) file_put_contents($this->directory.$filename, sha1($data).$data);
	}

	/**
	 * Finds an array of ids for a given tag.
	 *
	 * @param  string  tag name
	 * @return array   of ids that match the tag
	 */
	public function find($tag)
	{
		if ($files = $this->exists($tag, TRUE))
		{
			// Length of directory name
			$offset = strlen($this->directory);

			// Find all the files with the given tag
			$array = array();
			foreach ($files as $file)
			{
				// Get the id from the filename
				$array[] = substr(current(explode('~', $file)), $offset);
			}

			return $array;
		}

		return FALSE;
	}

	/**
	 * Fetches a cache item. This will delete the item if it is expired or if
	 * the hash does not match the stored hash.
	 *
	 * @param   string  cache id
	 * @return  mixed|NULL
	 */
	public function get($id)
	{
		if ($file = $this->exists($id))
		{
			// Always process the first result
			$file = current($file);

			// Validate that the cache has not expired
			if ($this->expired($file))
			{
				// Remove this cache, it has expired
				$this->delete($id);
			}
			else
			{
				$data = file_get_contents($file);

				// Find the hash of the data
				$hash = substr($data, 0, 40);

				// Remove the hash from the data
				$data = substr($data, 40);

				if ($hash !== sha1($data))
				{
					// Remove this cache, it doesn't validate
					$this->delete($id);

					// Unset data to prevent it from being returned
					unset($data);
				}
			}
		}

		// Return NULL if there is no data
		return isset($data) ? $data : NULL;
	}

	/**
	 * Deletes a cache item by id or tag
	 *
	 * @param   string   cache id or tag, or TRUE for "all items"
	 * @param   boolean  use tags
	 * @return  boolean
	 */
	public function delete($id, $tag = FALSE)
	{
		$files = $this->exists($id, $tag);

		if (empty($files))
			return FALSE;

		// Disable all error reporting while deleting
		$ER = error_reporting(0);

		foreach ($files as $file)
		{
			// Remove the cache file
			if ( ! unlink($file))
				Kohana::log('error', 'Cache: Unable to delete cache file: '.$file);
		}

		// Turn on error reporting again
		error_reporting($ER);

		return TRUE;
	}

	/**
	 * Deletes all cache files that are older than the current time.
	 *
	 * @return void
	 */
	public function delete_expired()
	{
		if ($files = $this->exists(TRUE))
		{
			foreach ($files as $file)
			{
				if ($this->expired($file))
				{
					// The cache file has already expired, delete it
					@unlink($file) or Kohana::log('error', 'Cache: Unable to delete cache file: '.$file);
				}
			}
		}
	}

	/**
	 * Check if a cache file has expired by filename.
	 *
	 * @param  string  filename
	 * @return bool
	 */
	protected function expired($file)
	{
		// Get the expiration time
		$expires = (int) substr($file, strrpos($file, '~') + 1);

		// Expirations of 0 are "never expire"
		return ($expires !== 0 AND $expires <= time());
	}

} // End Cache File Driver