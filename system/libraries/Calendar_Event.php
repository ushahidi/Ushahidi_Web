<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Calendar event observer class.
 *
 * $Id: Calendar_Event.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Calendar
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Calendar_Event_Core extends Event_Observer {

	// Boolean conditions
	protected $booleans = array
	(
		'current',
		'weekend',
		'first_day',
		'last_day',
		'last_occurrence',
		'easter',
	);

	// Rendering conditions
	protected $conditions = array();

	// Cell classes
	protected $classes = array();

	// Cell output
	protected $output = '';

	/**
	 * Adds a condition to the event. The condition can be one of the following:
	 *
	 * timestamp       - UNIX timestamp
	 * day             - day number (1-31)
	 * week            - week number (1-5)
	 * month           - month number (1-12)
	 * year            - year number (4 digits)
	 * day_of_week     - day of week (1-7)
	 * current         - active month (boolean) (only show data for the month being rendered)
	 * weekend         - weekend day (boolean)
	 * first_day       - first day of month (boolean)
	 * last_day        - last day of month (boolean)
	 * occurrence      - occurrence of the week day (1-5) (use with "day_of_week")
	 * last_occurrence - last occurrence of week day (boolean) (use with "day_of_week")
	 * easter          - Easter day (boolean)
	 * callback        - callback test (boolean)
	 *
	 * To unset a condition, call condition with a value of NULL.
	 *
	 * @chainable
	 * @param   string  condition key
	 * @param   mixed   condition value
	 * @return  object
	 */
	public function condition($key, $value)
	{
		if ($value === NULL)
		{
			unset($this->conditions[$key]);
		}
		else
		{
			if ($key === 'callback')
			{
				// Do nothing
			}
			elseif (in_array($key, $this->booleans))
			{
				// Make the value boolean
				$value = (bool) $value;
			}
			else
			{
				// Make the value an int
				$value = (int) $value;
			}

			$this->conditions[$key] = $value;
		}

		return $this;
	}

	/**
	 * Add a CSS class for this event. This can be called multiple times.
	 *
	 * @chainable
	 * @param   string  CSS class name
	 * @return  object
	 */
	public function add_class($class)
	{
		$this->classes[$class] = $class;

		return $this;
	}

	/**
	 * Remove a CSS class for this event. This can be called multiple times.
	 *
	 * @chainable
	 * @param   string  CSS class name
	 * @return  object
	 */
	public function remove_class($class)
	{
		unset($this->classes[$class]);

		return $this;
	}

	/**
	 * Set HTML output for this event.
	 *
	 * @chainable
	 * @param   string  HTML output
	 * @return  object
	 */
	public function output($str)
	{
		$this->output = $str;

		return $this;
	}

	/**
	 * Add a CSS class for this event. This can be called multiple times.
	 *
	 * @chainable
	 * @param   string  CSS class name
	 * @return  object
	 */
	public function notify($data)
	{
		// Split the date and current status
		list ($month, $day, $year, $week, $current) = $data;

		// Get a timestamp for the day
		$timestamp = mktime(0, 0, 0, $month, $day, $year);

		// Date conditionals
		$condition = array
		(
			'timestamp'   => (int) $timestamp,
			'day'         => (int) date('j', $timestamp),
			'week'        => (int) $week,
			'month'       => (int) date('n', $timestamp),
			'year'        => (int) date('Y', $timestamp),
			'day_of_week' => (int) date('w', $timestamp),
			'current'     => (bool) $current,
		);

		// Tested conditions
		$tested = array();

		foreach ($condition as $key => $value)
		{
			// Test basic conditions first
			if (isset($this->conditions[$key]) AND $this->conditions[$key] !== $value)
				return FALSE;

			// Condition has been tested
			$tested[$key] = TRUE;
		}

		if (isset($this->conditions['weekend']))
		{
			// Weekday vs Weekend
			$condition['weekend'] = ($condition['day_of_week'] === 0 OR $condition['day_of_week'] === 6);
		}

		if (isset($this->conditions['first_day']))
		{
			// First day of month
			$condition['first_day'] = ($condition['day'] === 1);
		}

		if (isset($this->conditions['last_day']))
		{
			// Last day of month
			$condition['last_day'] = ($condition['day'] === (int) date('t', $timestamp));
		}

		if (isset($this->conditions['occurrence']))
		{
			// Get the occurance of the current day
			$condition['occurrence'] = $this->day_occurrence($timestamp);
		}

		if (isset($this->conditions['last_occurrence']))
		{
			// Test if the next occurance of this date is next month
			$condition['last_occurrence'] = ((int) date('n', $timestamp + 604800) !== $condition['month']);
		}

		if (isset($this->conditions['easter']))
		{
			if ($condition['month'] === 3 OR $condition['month'] === 4)
			{
				// This algorithm is from Practical Astronomy With Your Calculator, 2nd Edition by Peter
				// Duffett-Smith. It was originally from Butcher's Ecclesiastical Calendar, published in
				// 1876. This algorithm has also been published in the 1922 book General Astronomy by
				// Spencer Jones; in The Journal of the British Astronomical Association (Vol.88, page
				// 91, December 1977); and in Astronomical Algorithms (1991) by Jean Meeus.

				$a = $condition['year'] % 19;
				$b = (int) ($condition['year'] / 100);
				$c = $condition['year'] % 100;
				$d = (int) ($b / 4);
				$e = $b % 4;
				$f = (int) (($b + 8) / 25);
				$g = (int) (($b - $f + 1) / 3);
				$h = (19 * $a + $b - $d - $g + 15) % 30;
				$i = (int) ($c / 4);
				$k = $c % 4;
				$l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
				$m = (int) (($a + 11 * $h + 22 * $l) / 451);
				$p = ($h + $l - 7 * $m + 114) % 31;

				$month = (int) (($h + $l - 7 * $m + 114) / 31);
				$day = $p + 1;

				$condition['easter'] = ($condition['month'] === $month AND $condition['day'] === $day);
			}
			else
			{
				// Easter can only happen in March or April
				$condition['easter'] = FALSE;
			}
		}

		if (isset($this->conditions['callback']))
		{
			// Use a callback to determine validity
			$condition['callback'] = call_user_func($this->conditions['callback'], $condition, $this);
		}

		$conditions = array_diff_key($this->conditions, $tested);

		foreach ($conditions as $key => $value)
		{
			if ($key === 'callback')
			{
				// Callbacks are tested on a TRUE/FALSE basis
				$value = TRUE;
			}

			// Test advanced conditions
			if ($condition[$key] !== $value)
				return FALSE;
		}

		$this->caller->add_data(array
		(
			'classes' => $this->classes,
			'output'  => $this->output,
		));
	}

	/**
	 * Find the week day occurrence for a specific timestamp. The occurrence is
	 * relative to the current month. For example, the second Saturday of any
	 * given month will return "2" as the occurrence. This is used in combination
	 * with the "occurrence" condition.
	 *
	 * @param   integer  UNIX timestamp
	 * @return  integer
	 */
	protected function day_occurrence($timestamp)
	{
		// Get the current month for the timestamp
		$month = date('m', $timestamp);

		// Default occurrence is one
		$occurrence = 1;

		// Reduce the timestamp by one week for each loop. This has the added
		// benefit of preventing an infinite loop.
		while ($timestamp -= 604800)
		{
			if (date('m', $timestamp) !== $month)
			{
				// Once the timestamp has gone into the previous month, the
				// proper occurrence has been found.
				return $occurrence;
			}

			// Increment the occurrence
			$occurrence++;
		}
	}

} // End Calendar Event
