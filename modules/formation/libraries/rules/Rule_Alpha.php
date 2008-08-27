<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Depends_On_Core extends Rule {
	
	public function is_valid($value)
	{
		$this->value=$value;
		return valid::alpha($value);
	}
}