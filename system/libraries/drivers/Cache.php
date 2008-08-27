<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cache driver interface.
 *
 * $Id: Cache.php 3135 2008-07-17 13:14:17Z Geert $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
interface Cache_Driver {

	/**
	 * Set a cache item.
	 */
	public function set($id, $data, $tags, $lifetime);

	/**
	 * Find all of the cache ids for a given tag.
	 */
	public function find($tag);

	/**
	 * Get a cache item.
	 * Return NULL if the cache item is not found.
	 */
	public function get($id);

	/**
	 * Delete cache items by id or tag.
	 */
	public function delete($id, $tag = FALSE);

	/**
	 * Deletes all expired cache items.
	 */
	public function delete_expired();

} // End Cache Driver