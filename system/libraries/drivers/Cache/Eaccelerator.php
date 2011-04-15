<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Eaccelerator-based Cache driver.
 *
 * $Id: Eaccelerator.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_Eaccelerator_Driver implements Cache_Driver {

	public function __construct()
	{
		if ( ! extension_loaded('eaccelerator'))
			throw new Kohana_Exception('cache.extension_not_loaded', 'eaccelerator');
	}

	public function get($id)
	{
		return eaccelerator_get($id);
	}

	public function find($tag)
	{
		return FALSE;
	}

	public function set($id, $data, $tags, $lifetime)
	{
		count($tags) and Kohana::log('error', 'tags are unsupported by the eAccelerator driver');

		return eaccelerator_put($id, $data, $lifetime);
	}

	public function delete($id, $tag = FALSE)
	{
		if ($id === TRUE)
			return eaccelerator_clean();

		if ($tag == FALSE)
			return eaccelerator_rm($id);

		return TRUE;
	}

	public function delete_expired()
	{
		eaccelerator_gc();
	}

} // End Cache eAccelerator Driver