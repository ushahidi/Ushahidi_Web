<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Digit_Core extends Rule {
	
	public function is_valid($value)
	{
		$this->value=$value;
		return valid::digit($value);
	}
}