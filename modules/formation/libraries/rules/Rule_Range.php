<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Range_Core extends Rule {
	
	public function __construct(array $arguments)
	{
		$this->set_min($arguments[0]);
		$this->set_max($arguments[1]);
	}
	public function set_min($length)
	{
		$this->message_vars['min']=(int) $length;
	}
	public function set_max($length)
	{
		$this->message_vars['max']=(int) $length;
	}
	public function is_valid($value)
	{
		$this->value=$value;
		if($this->value>=$this->message_vars['min'] && $this->value<=$this->message_vars['max'])
		{
			return true;
		}
		return false;
	}
}