<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model base class.
 *
 * $Id: Model.php 3002 2008-07-08 14:19:12Z Shadowhand $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Model_Core {

	protected $db;

	/**
	 * Loads the database instance, if the database is not already loaded.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		if ( ! is_object($this->db))
		{
			// Load the default database
			$this->db = Database::instance('default');
		}
	}

} // End Model