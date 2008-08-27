<?php

class Rule_Regex_Core extends Rule {
	
	public function __construct($regex)
	{
		$this->set_regex($regex);
	}
	public function set_regex($regex)
	{
		$this->message_vars['regex']= $regex;
	}
	public function is_valid($value)
	{
		$this->value=$value;
		if (preg_match($regex, $this->value))
		{
			// Regex matches, return
			return TRUE;
		}		
		return false;
	}
}