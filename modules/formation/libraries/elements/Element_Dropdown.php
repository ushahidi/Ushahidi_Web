<?php defined('SYSPATH') or die('No direct script access.');

class Element_Dropdown_Core extends Element_Input {

	protected $options=array();
	
	protected $attr = array
	(
		'class'=>'dropdown'
	);

	public function set_options($options)
	{
		$this->options=$options;
		return $this;		
	}
	public function get_options($options)
	{
		$this->options=$options;
	}	
	public function get_selected()
	{
		return $this->selected;
	}
	public function set_selected($select)
	{
		$this->selected=$select;
		return $this;
	}
	public function get_value()
	{
		return $this->get_selected();
	}
	public function __toString(){
		return $this->get_value();
	}
	/**
	 * Magically gets a variable.
	 *
	 * @param   string  variable key
	 * @return  mixed   variable value if the key is found
	 * @return  void    if the key is not found
	 */

	public function html_element()
	{
		// Import base data
		$data = $this->attr;
		$data['name']=$this->name;
		// Get the options and default selection
		$options = $this->options;
		$selected =$this->selected;

		return form::dropdown($data, $options, $selected);
	}

	public function set_value($value)
	{
		if (is_bool($this->is_valid))
			return;

		$this->selected = $value;
	}

} // End Form Dropdown