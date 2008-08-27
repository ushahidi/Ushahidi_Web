<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Depends_On_Core extends Rule {
	public function __construct($field)
	{
		
		$this->set_field($field);		
	}
	public function set_field($field)
	{	
		$this->depends_on=$field;
		
		return $this;
	}	
	public function is_valid($value)
	{
		$this->value=$value;
		$this->message_vars['depends_on_field']=$this->depends_on->get_name();
		$this->message_vars['depends_on_value']=$this->depends_on->get_value();
		
		$depend_value=$this->message_vars['depends_on_value'];
		
		return !($depend_value === '' OR $depend_value === NULL OR $depend_value === FALSE OR (is_array($depend_value) AND empty($depend_value)));
	}
}