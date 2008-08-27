<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Length_Core extends Rule {
	
	public function __construct(array $arguments)
	{
		$this->set_min_length($arguments[0]);
		$this->set_max_length($arguments[1]);
	}
	public function set_min_length($length)
	{
		$this->message_vars['min_length']=(int) $length;
	}
	public function set_max_length($length)
	{
		$this->message_vars['max_length']=(int) $length;
	}
	public function is_valid($value)
	{
		$this->value=$value;
		if(utf8::strlen($value)>=$this->message_vars['min_length'] && utf8::strlen($value)<=$this->message_vars['max_length'])
		{
			return true;
		}
		return false;
	}
}