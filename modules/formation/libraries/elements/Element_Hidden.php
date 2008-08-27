<?php

class Element_Hidden_Core extends Element_Input {

	
	protected $attr = array
	(
		'class'=>'hidden'
	);
	public function render()
	{
		$data = $this->data;
		$data[$this->name]=$this->value;
		
		return form::hidden($data,$this->value);
	}	
	public function label()
	{
		return '';
	}
}
?>