<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * MSSQL Database Driver
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Database_Mssql_Driver extends Database_Driver
{
	/**
	 * Database connection link
	 */
	protected $link;

	/**
	 * Database configuration
	 */
	protected $db_config;

	/**
	 * Sets the config for the class.
	 *
	 * @param  array  database configuration
	 */
	public function __construct($config)
	{
		$this->db_config = $config;

		Kohana::log('debug', 'MSSQL Database Driver Initialized');
	}

	/**
	 * Closes the database connection.
	 */
	public function __destruct()
	{
		is_resource($this->link) and mssql_close($this->link);
	}

	/**
	 * Make the connection
	 *
	 * @return return connection
	 */
	public function connect()
	{
		// Check if link already exists
		if (is_resource($this->link))
			return $this->link;

		// Import the connect variables
		extract($this->db_config['connection']);

		// Persistent connections enabled?
		$connect = ($this->db_config['persistent'] == TRUE) ? 'mssql_pconnect' : 'mssql_connect';

		// Build the connection info
		$host = isset($host) ? $host : $socket;

		// Windows uses a comma instead of a colon
		$port = (isset($port) AND is_string($port)) ? (KOHANA_IS_WIN ? ',' : ':').$port : '';

		// Make the connection and select the database
		if (($this->link = $connect($host.$port, $user, $pass, TRUE)) AND mssql_select_db($database, $this->link))
		{
			/* This is being removed so I can use it, will need to come up with a more elegant workaround in the future...
			 *
			if ($charset = $this->db_config['character_set'])
			{
				$this->set_charset($charset);
			}
			*/

			// Clear password after successful connect
			$this->config['connection']['pass'] = NULL;

			return $this->link;
		}

		return FALSE;
	}

	public function query($sql)
	{
		// Only cache if it's turned on, and only cache if it's not a write statement
		if ($this->db_config['cache'] AND ! preg_match('#\b(?:INSERT|UPDATE|REPLACE|SET)\b#i', $sql))
		{
			$hash = $this->query_hash($sql);

			if ( ! isset(self::$query_cache[$hash]))
			{
				// Set the cached object
				self::$query_cache[$hash] = new Mssql_Result(mssql_query($sql, $this->link), $this->link, $this->db_config['object'], $sql);
			}

			// Return the cached query
			return self::$query_cache[$hash];
		}

		return new Mssql_Result(mssql_query($sql, $this->link), $this->link, $this->db_config['object'], $sql);
	}

	public function escape_table($table)
	{
		if (stripos($table, ' AS ') !== FALSE)
		{
			// Force 'AS' to uppercase
			$table = str_ireplace(' AS ', ' AS ', $table);

			// Runs escape_table on both sides of an AS statement
			$table = array_map(array($this, __FUNCTION__), explode(' AS ', $table));

			// Re-create the AS statement
			return implode(' AS ', $table);
		}
		return '['.str_replace('.', '[.]', $table).']';
	}

	public function escape_column($column)
	{
		if (!$this->db_config['escape'])
			return $column;

		if (strtolower($column) == 'count(*)' OR $column == '*')
			return $column;

		// This matches any modifiers we support to SELECT.
		if ( ! preg_match('/\b(?:rand|all|distinct(?:row)?|high_priority|sql_(?:small_result|b(?:ig_result|uffer_result)|no_cache|ca(?:che|lc_found_rows)))\s/i', $column))
		{
			if (stripos($column, ' AS ') !== FALSE)
			{
				// Force 'AS' to uppercase
				$column = str_ireplace(' AS ', ' AS ', $column);

				// Runs escape_column on both sides of an AS statement
				$column = array_map(array($this, __FUNCTION__), explode(' AS ', $column));

				// Re-create the AS statement
				return implode(' AS ', $column);
			}

			return preg_replace('/[^.*]+/', '[$0]', $column);
		}

		$parts = explode(' ', $column);
		$column = '';

		for ($i = 0, $c = count($parts); $i < $c; $i++)
		{
			// The column is always last
			if ($i == ($c - 1))
			{
				$column .= preg_replace('/[^.*]+/', '[$0]', $parts[$i]);
			}
			else // otherwise, it's a modifier
			{
				$column .= $parts[$i].' ';
			}
		}
		return $column;
	}

	/**
	 * Limit in SQL Server 2000 only uses the keyword
	 * 'TOP'; 2007 may have an offset keyword, but
	 * I am unsure - for pagination style limit,offset
	 * functionality, a fancy query needs to be built.
	 *
	 * @param unknown_type $limit
	 * @return unknown
	 */
	public function limit($limit, $offset=null)
	{
		return 'TOP '.$limit;
	}

	public function compile_select($database)
	{
		$sql = ($database['distinct'] == TRUE) ? 'SELECT DISTINCT ' : 'SELECT ';
		$sql .= (count($database['select']) > 0) ? implode(', ', $database['select']) : '*';

		if (count($database['from']) > 0)
		{
			// Escape the tables
			$froms = array();
			foreach ($database['from'] as $from)
				$froms[] = $this->escape_column($from);
			$sql .= "\nFROM ";
			$sql .= implode(', ', $froms);
		}

		if (count($database['join']) > 0)
		{
			foreach($database['join'] AS $join)
			{
				$sql .= "\n".$join['type'].'JOIN '.implode(', ', $join['tables']).' ON '.$join['conditions'];
			}
		}

		if (count($database['where']) > 0)
		{
			$sql .= "\nWHERE ";
		}

		$sql .= implode("\n", $database['where']);

		if (count($database['groupby']) > 0)
		{
			$sql .= "\nGROUP BY ";
			$sql .= implode(', ', $database['groupby']);
		}

		if (count($database['having']) > 0)
		{
			$sql .= "\nHAVING ";
			$sql .= implode("\n", $database['having']);
		}

		if (count($database['orderby']) > 0)
		{
			$sql .= "\nORDER BY ";
			$sql .= implode(', ', $database['orderby']);
		}

		if (is_numeric($database['limit']))
		{
			$sql .= "\n";
			$sql .= $this->limit($database['limit']);
		}

		return $sql;
	}

	public function escape_str($str)
	{
		if (!$this->db_config['escape'])
			return $str;

		is_resource($this->link) or $this->connect();
		//mssql_real_escape_string($str, $this->link); <-- this function doesn't exist

		$characters = array('/\x00/', '/\x1a/', '/\n/', '/\r/', '/\\\/', '/\'/');
		$replace    = array('\\\x00', '\\x1a', '\\n', '\\r', '\\\\', "''");
		return preg_replace($characters, $replace, $str);
	}

	public function list_tables(Database $db)
	{
		$sql    = 'SHOW TABLES FROM ['.$this->db_config['connection']['database'].']';
		$result = $this->query($sql)->result(FALSE, MSSQL_ASSOC);

		$retval = array();
		foreach ($result as $row)
		{
			$retval[] = current($row);
		}

		return $retval;
	}

	public function show_error()
	{
		return mssql_get_last_message($this->link);
	}

	public function list_fields($table)
	{
		static $tables;

		if (empty($tables[$table]))
		{
			foreach ($this->field_data($table) as $row)
			{
				// Make an associative array
				$tables[$table][$row->Field] = $this->sql_type($row->Type);
			}
		}

		return $tables[$table];
	}

	public function field_data($table)
	{
		$columns = array();

		if ($query = MSSQL_query('SHOW COLUMNS FROM '.$this->escape_table($table), $this->link))
		{
			if (MSSQL_num_rows($query) > 0)
			{
				while ($row = MSSQL_fetch_object($query))
				{
					$columns[] = $row;
				}
			}
		}

		return $columns;
	}
}

/**
 * MSSQL Result
 */
class Mssql_Result extends Database_Result {

	// Fetch function and return type
	protected $fetch_type  = 'mssql_fetch_object';
	protected $return_type = MSSQL_ASSOC;

	/**
	 * Sets up the result variables.
	 *
	 * @param  resource  query result
	 * @param  resource  database link
	 * @param  boolean   return objects or arrays
	 * @param  string    SQL query that was run
	 */
	public function __construct($result, $link, $object = TRUE, $sql)
	{
		$this->result = $result;

		// If the query is a resource, it was a SELECT, SHOW, DESCRIBE, EXPLAIN query
		if (is_resource($result))
		{
			$this->current_row = 0;
			$this->total_rows  = mssql_num_rows($this->result);
			$this->fetch_type = ($object === TRUE) ? 'mssql_fetch_object' : 'mssql_fetch_array';
		}
		elseif (is_bool($result))
		{
			if ($result == FALSE)
			{
				// SQL error
				throw new Kohana_Database_Exception('database.error', mssql_get_last_message($link).' - '.$sql);
			}
			else
			{
				// Its an DELETE, INSERT, REPLACE, or UPDATE querys
				$last_id          = mssql_query('SELECT @@IDENTITY AS last_id', $link);
				$result           = mssql_fetch_assoc($last_id);
				$this->insert_id  = $result['last_id'];
				$this->total_rows = mssql_rows_affected($link);
			}
		}

		// Set result type
		$this->result($object);

		// Store the SQL
		$this->sql = $sql;
	}

	/**
	 * Destruct, the cleanup crew!
	 */
	public function __destruct()
	{
		if (is_resource($this->result))
		{
			mssql_free_result($this->result);
		}
	}

	public function result($object = TRUE, $type = MSSQL_ASSOC)
	{
		$this->fetch_type = ((bool) $object) ? 'mssql_fetch_object' : 'mssql_fetch_array';

		// This check has to be outside the previous statement, because we do not
		// know the state of fetch_type when $object = NULL
		// NOTE - The class set by $type must be defined before fetching the result,
		// autoloading is disabled to save a lot of stupid overhead.
		if ($this->fetch_type == 'mssql_fetch_object')
		{
			$this->return_type = (is_string($type) AND Kohana::auto_load($type)) ? $type : 'stdClass';
		}
		else
		{
			$this->return_type = $type;
		}

		return $this;
	}

	public function as_array($object = NULL, $type = MSSQL_ASSOC)
	{
		return $this->result_array($object, $type);
	}

	public function result_array($object = NULL, $type = MSSQL_ASSOC)
	{
		$rows = array();

		if (is_string($object))
		{
			$fetch = $object;
		}
		elseif (is_bool($object))
		{
			if ($object === TRUE)
			{
				$fetch = 'mssql_fetch_object';

				// NOTE - The class set by $type must be defined before fetching the result,
				// autoloading is disabled to save a lot of stupid overhead.
				$type = (is_string($type) AND Kohana::auto_load($type)) ? $type : 'stdClass';
			}
			else
			{
				$fetch = 'mssql_fetch_array';
			}
		}
		else
		{
			// Use the default config values
			$fetch = $this->fetch_type;

			if ($fetch == 'mssql_fetch_object')
			{
				$type = (is_string($type) AND Kohana::auto_load($type)) ? $type : 'stdClass';
			}
		}

		if (mssql_num_rows($this->result))
		{
			// Reset the pointer location to make sure things work properly
			mssql_data_seek($this->result, 0);

			while ($row = $fetch($this->result, $type))
			{
				$rows[] = $row;
			}
		}

		return isset($rows) ? $rows : array();
	}

	public function list_fields()
	{
		$field_names = array();
		while ($field = mssql_fetch_field($this->result))
		{
			$field_names[] = $field->name;
		}

		return $field_names;
	}

	public function seek($offset)
	{
		if ( ! $this->offsetExists($offset))
			return FALSE;

		return mssql_data_seek($this->result, $offset);
	}

} // End mssql_Result Class