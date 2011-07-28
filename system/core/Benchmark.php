<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Simple benchmarking.
 *
 * $Id: Benchmark.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
final class Benchmark {

	// Benchmark timestamps
	private static $marks;

	/**
	 * Set a benchmark start point.
	 *
	 * @param   string  benchmark name
	 * @return  void
	 */
	public static function start($name)
	{
		if ( ! isset(self::$marks[$name]))
		{
			self::$marks[$name] = array
			(
				'start'        => microtime(TRUE),
				'stop'         => FALSE,
				'memory_start' => function_exists('memory_get_usage') ? memory_get_usage() : 0,
				'memory_stop'  => FALSE
			);
		}
	}

	/**
	 * Config checked version of start()
	 *
	 * @param   string  benchmark name
	 * @return  void
	 */
	public static function f_start($name)
	{
		if (Kohana::config('benchmark.enable') === TRUE)
		{
			self::start($name);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Set a benchmark stop point.
	 *
	 * @param   string  benchmark name
	 * @return  void
	 */
	public static function stop($name)
	{
		if (isset(self::$marks[$name]) AND self::$marks[$name]['stop'] === FALSE)
		{
			self::$marks[$name]['stop'] = microtime(TRUE);
			self::$marks[$name]['memory_stop'] = function_exists('memory_get_usage') ? memory_get_usage() : 0;
		}
	}

	/**
	 * Config checked version of stop()
	 *
	 * @param   string  benchmark name
	 * @return  void
	 */
	public static function f_stop($name)
	{
		if (Kohana::config('benchmark.enable') === TRUE)
		{
			self::stop($name);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Get the elapsed time between a start and stop.
	 *
	 * @param   string   benchmark name, TRUE for all
	 * @param   integer  number of decimal places to count to
	 * @return  array
	 */
	public static function get($name, $decimals = 4)
	{
		if ($name === TRUE)
		{
			$times = array();
			$names = array_keys(self::$marks);

			foreach ($names as $name)
			{
				// Get each mark recursively
				$times[$name] = self::get($name, $decimals);
			}

			// Return the array
			return $times;
		}

		if ( ! isset(self::$marks[$name]))
			return FALSE;

		if (self::$marks[$name]['stop'] === FALSE)
		{
			// Stop the benchmark to prevent mis-matched results
			self::stop($name);
		}

		// Return a string version of the time between the start and stop points
		// Properly reading a float requires using number_format or sprintf
		return array
		(
			'time'   => number_format(self::$marks[$name]['stop'] - self::$marks[$name]['start'], $decimals),
			'memory' => (self::$marks[$name]['memory_stop'] - self::$marks[$name]['memory_start'])
		);
	}

	/**
	 * Saves the benchmark results to a database
	 * @todo Use the database libraries for this instead of platform-specific DB calls
	 *
	 * @return void
	 */
	public static function save_results(){

		// Ignore all of these actions if we have benchmarking disabled
		if (Kohana::config('benchmark.enable') === FALSE) return FALSE;

		// Connect to the benchmark database
		$db = Kohana::config('benchmark.db');
		$link = mysql_connect($db['host'], $db['user'], $db['pass']) or die('Could not connect to benchmark database.');
		mysql_select_db($db['database']) or die('Could not select benchmark database.');
		$table = mysql_real_escape_string($db['table_prefix']).'benchmark';

		$benchmark_results = Benchmark::get(TRUE);
		foreach ($benchmark_results as $name => $data)
		{
			// Don't save the generic system benchmark results
			if (strstr($name,'system_benchmark_') === FALSE)
			{
				$query = 'INSERT INTO '.$table.' (`name`, `time`, `memory`) VALUES (\''.mysql_real_escape_string($name)
				. '\', \''.mysql_real_escape_string($data['time']).'\', \''.mysql_real_escape_string($data['memory']).'\');';
				
				// Execute the query
				mysql_query($query,$link);
			}
		}
		
		// Close the connection to the Benchmar DB
		mysql_close($link);
	}

} // End Benchmark
