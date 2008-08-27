<?php

class Rule_Csrf_Core extends Rule{

	public function __construct($hash)
	{
		
		$this->hash=$hash;
	}

	
	public function is_valid($value)
	{
		$this->value=$value;
		return ($this->value==$this->hash);
	}

}