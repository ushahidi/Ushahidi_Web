<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Matches_Core extends Rule{

	public function __construct($array)
	{
		$this->set_array($array);		
	}
	public function set_array($array)
	{	
		$this->array=$array;
		return $this;
	}
	
	public function is_valid($value)
	{
		$this->value=$value;
		return (in_array($value,$this->array));
	}

}