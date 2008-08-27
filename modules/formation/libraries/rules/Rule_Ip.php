<?php defined('SYSPATH') or die('No direct access allowed.');

class Rule_Ip_Core extends Rule {
	
	public function is_valid($value)
	{
		$this->valu=$value;
		return valid::ip($value);
	}
}