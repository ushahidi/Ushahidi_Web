<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Email_Core extends Rule{

	public function is_valid($value)
	{
		$this->value=$value;
		return ($this->is_valid= valid::email($value));
	}

}