<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Min_Length_Core extends Rule_Length {
	
	public function __construct($length)
	{
		$this->set_min_length($length);
	}
	public function is_valid($value)
	{
		$this->value=$value;
		return (utf8::strlen($value)>=$this->message_vars['min_length']);
	}
}