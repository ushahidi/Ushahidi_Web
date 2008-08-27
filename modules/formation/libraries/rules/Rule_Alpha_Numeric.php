<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Alpha_Numeric_Core extends Rule {
	
	public function is_valid($value)
	{
		$this->value=$value;
		return valid::alpha_numeric($value);
	}
}