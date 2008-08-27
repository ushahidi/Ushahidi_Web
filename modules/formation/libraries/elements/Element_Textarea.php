<?php defined('SYSPATH') or die('No direct script access.');

class Element_Textarea_Core extends Element_Input {
	
	protected $attr = array
	(
		'class' => 'textarea',
	);
	
	protected function html_element()
	{
		$data = $this->attr;
		$data['value']=$this->value;
		$data['name']=$this->name;

		return form::textarea($data);
	}	
}