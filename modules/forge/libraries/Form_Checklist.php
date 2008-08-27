<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE checklist input library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Form_Checklist_Core extends Form_Input {

	protected $data = array
	(
		'name'    => '',
		'type'    => 'checkbox',
		'class'   => 'checklist',
		'options' => array(),
	);

	protected $protect = array('name', 'type');

	public function __construct($name)
	{
		$this->data['name'] = $name;
	}

	public function __get($key)
	{
		if ($key == 'value')
		{
			// Return the currently checked values
			$array = array();
			foreach($this->data['options'] as $id => $opt)
			{
				// Return the options that are checked
				($opt[1] === TRUE) and $array[] = $id;
			}
			return $array;
		}

		return parent::__get($key);
	}

	public function html()
	{
		// Import base data
		$base_data = $this->data;

		// Make it an array
		$base_data['name'] .= '[]';

		// Newline
		$nl = "\n";

		$checklist = '<ul class="'.arr::remove('class', $base_data).'">'.$nl;
		foreach(arr::remove('options', $base_data) as $val => $opt)
		{
			// New set of input data
			$data = $base_data;

			// Get the title and checked status
			list ($title, $checked) = $opt;

			// Set the name, value, and checked status
			$data['value']   = $val;
			$data['checked'] = $checked;

			$checklist .= '<li><label>'.form::checkbox($data).' '.$title.'</label></li>'.$nl;
		}
		$checklist .= '</ul>';

		return $checklist;
	}

	protected function load_value()
	{
		foreach($this->data['options'] as $val => $checked)
		{
			if ($input = $this->input_value($this->data['name']))
			{
				$this->data['options'][$val][1] = in_array($val, $input);
			}
			else
			{
				$this->data['options'][$val][1] = FALSE;
			}
		}
	}

} // End Form Checklist