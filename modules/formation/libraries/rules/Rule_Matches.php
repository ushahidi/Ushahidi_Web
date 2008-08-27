<?php

class Rule_Matches_Core extends Rule{

	public function __construct($field)
	{
		
		$this->set_field($field);		
	}
	public function set_field($field)
	{	
		$this->match_field=$field;
		
		return $this;
	}
	
	public function is_valid($value)
	{
		$this->message_vars['match_value']=$this->match_field->get_value();
		$this->message_vars['match_field']=$this->match_field->get_name();

		$this->value=$value;
		return ($this->value==$this->message_vars['match_value']);
	}

}