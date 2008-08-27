<?php defined('SYSPATH') or die('No direct script access.');

class Element_Email_Core extends Element_Input {


	public function __construct($name,$value=null)
	{
		$this->add_rule('Rule_Email');
		parent::__construct($name,$value);
	
	}


} // End F