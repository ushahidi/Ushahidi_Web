<?php

class Rule_Exact_Length_Core extends Rule {
	
	public function __construct($length)
	{
		$this->set_length($length);
	}
	public function set_length($length)
	{
		$this->message_vars['length']=(int) $length;
	}
	public function is_valid($value)
	{
		$this->value=$value;
		return (utf8::strlen($value)==$this->message_vars['length']);
	}
}