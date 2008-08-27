<?php

class Rule_Numeric_Core extends Rule {
	
	public function is_valid($value)
	{
		return valid::numeric($value);
	}
}