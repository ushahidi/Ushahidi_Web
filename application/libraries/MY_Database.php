<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Custom extendsion to Database library
 *
 * Backports Kohana 3 style named query binding
 *
 * @package	   Ushahidi
 * @author	   Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
 */
class Database extends Database_Core {

	/**
	 * Combine a SQL statement with the bind values. Used for safe queries.
	 * 
	 * If $binds is an indexed array use KO3 style named binding
	 * otherwise fallback to Kohana core
	 *
	 * @param   string  query to bind to the values
	 * @param   array   array of values to bind to the query
	 * @return  string
	 */
	public function compile_binds($sql, $binds)
	{
		$isIndexed = array_values($binds) === $binds;
		// if we have an associative array, use named bindings ala KO3
		if (! $isIndexed)
		{
			// Escape all of the values
			$values = array_map(array($this->driver, 'escape'), $binds);
	
			// Replace the values in the SQL
			$sql = strtr($sql, $values);
			
			return $sql;
		}
		else
		{
			return parent::compile_binds($sql, $binds);
		}
	}
} // End Database Class
