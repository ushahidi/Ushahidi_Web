<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE checkbox input library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Form_Checkbox_Core extends Form_Input {

	protected $data = array
	(
		'type' => 'checkbox',
		'class' => 'checkbox',
		'value' => '1',
		'checked' => FALSE,
	);

	protected $protect = array('type');

	public function __get($key)
	{
		if ($key == 'value')
		{
			// Return the value if the checkbox is checked
			return $this->data['checked'] ? $this->data['value'] : NULL;
		}

		return parent::__get($key);
	}

	protected function html_element()
	{
		// Import the data
		$data = $this->data;

		if ($label = arr::remove('label', $data))
		{
			// There must be one space before the text
			$label = ' '.ltrim($label);
		}

		return '<label>'.form::checkbox($data).$label.'</label>';
	}

	protected function load_value()
	{
		if (is_bool($this->valid))
			return;

		// Makes the box checked if the value from POST is the same as the current value
		$this->data['checked'] = ($this->input_value($this->name) == $this->data['value']);
	}

} // End Form Checkbox