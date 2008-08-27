<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE dateselect input library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Form_Dateselect_Core extends Form_Input {

	protected $data = array
	(
		'name'  => '',
		'class' => 'dropdown',
	);

	protected $protect = array('type');

	// Precision for the parts, you can use @ to insert a literal @ symbol
	protected $parts = array
	(
		'month'   => array(),
		'day'     => array(1),
		'year'    => array(),
		' @ ',
		'hour'    => array(),
		':',
		'minute'  => array(5),
		'am_pm'   => array(),
	);

	public function __construct($name)
	{
		// Set name
		$this->data['name'] = $name;

		// Default to the current time
		$this->data['value'] = time();
	}

	public function __call($method, $args)
	{
		if (isset($this->parts[substr($method, 0, -1)]))
		{
			// Set options for date generation
			$this->parts[substr($method, 0, -1)] = $args;
			return $this;
		}

		return parent::__call($method, $args);
	}

	public function html_element()
	{
		// Import base data
		$data = $this->data;

		// Get the options and default selection
		$time = $this->time_array(arr::remove('value', $data));

		// No labels or values
		unset($data['label']);

		$input = '';
		foreach($this->parts as $type => $val)
		{
			if (is_int($type))
			{
				// Just add the separators
				$input .= $val;
				continue;
			}

			// Set this input name
			$data['name'] = $this->data['name'].'['.$type.']';

			// Set the selected option
			$selected = $time[$type];

			if ($type == 'am_pm')
			{
				// Options are static
				$options = array('AM' => 'AM', 'PM' => 'PM');
			}
			else
			{
				// minute(s), hour(s), etc
				$type .= 's';

				// Use the date helper to generate the options
				$options = empty($val) ? date::$type() : call_user_func_array(array('date', $type), $val);
			}

			$input .= form::dropdown($data, $options, $selected);
		}

		return $input;
	}

	protected function time_array($timestamp)
	{
		$time = array_combine
		(
			array('month', 'day', 'year', 'hour', 'minute', 'am_pm'), 
			explode('--', date('n--j--Y--g--i--A', $timestamp))
		);

		// Minutes should always be in 5 minute increments
		$time['minute'] = num::round($time['minute'], current($this->parts['minute']));

		return $time;
	}

	protected function load_value()
	{
		if (is_bool($this->valid))
			return;

		$time = $this->input_value($this->name);

		// Make sure all the required inputs keys are set
		$time += $this->time_array(time());

		$this->data['value'] = mktime
		(
			date::adjust($time['hour'], $time['am_pm']),
			$time['minute'],
			0,
			$time['month'],
			$time['day'],
			$time['year']
		);
	}

} // End Form Dateselect