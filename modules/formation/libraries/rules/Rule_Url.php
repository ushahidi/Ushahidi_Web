<?php

class Rule_Url_Core extends Rule {
	
	public function is_valid($value)
	{
		return valid::url($value);
	}
}