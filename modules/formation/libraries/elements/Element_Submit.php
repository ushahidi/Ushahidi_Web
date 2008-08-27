<?php defined('SYSPATH') or die('No direct script access.');

class Element_Submit_Core extends Element_Input {

	protected $attr = array
	(
		'type'  => 'submit',
		'class'=>'submit'	
		
	);
	public function __construct($name,$value=null)
	{
		parent::__construct($name,$value);
	}

	public function render()
	{
		$data = $this->attr;
		$data['value']=isset($data['value'])?$data['value']: $this->name;
		$data['name']=$this->name;
				
		return form::button($data);
	}
	public function set_value(){	}
	public function set_text($value)
	{
		$this->attr['value']=$value;
	}
	public function label()
	{
		return '';
	}
	public function validate()
	{
		// Submit buttons do not need to be validated
		return $this->is_valid = TRUE;
	}

	/**
	 * Set a form attribute. This method is chainable.
	 *
	 * @param   string        attribute name, or an array of attributes
	 * @param   string        attribute value
	 * @return  object
	 */
	public function set_attr($key, $val = NULL)
	{
		// Set the new attribute
		$this->attr[$key] = $val;
		
		return $this;
	}
} // End Form Submit