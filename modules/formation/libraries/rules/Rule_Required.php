<?php

class Rule_Required_Core extends Rule{

	public function is_valid($value)
	{
		$this->value=$value;
		return !($value === '' OR $value === NULL OR $value === FALSE OR (is_array($value) AND empty($value)));
	}

}