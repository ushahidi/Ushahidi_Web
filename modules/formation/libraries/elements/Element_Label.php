<?php

class Element_Label_Core extends Element_Input {
	
	protected $attr=array();
	protected $text;
	
	protected $label;
	
	public function __construct($name,$text=null)
	{
		$this->set_attr('for',$name);
		if($text==null)
		{
			$text=utf8::ucwords(inflector::humanize($name)).' ';
		}
		$this->set_text($text);
		$this->set_attr('id','label-'.$name);
	}
	
	public function set_text($text)
	{
		$this->text=$text;
	}
	public function get_text()
	{
		return $this->text;
	}
	public function render()
	{
		if($this->label != NULL)
		{
			return $this->label;
		}
		elseif($this->label===false)
		{
			return '';
		}
		
		return form::label($this->attr,$this->text);
		
	}		
	public function set_label($label)
	{
		$this->label=$label;
	}
	public function disable()
	{
		$this->label==false;
	}
	/**
	 * Returns the form HTML
	 */
	public function __toString()
	{
		return $this->render();
	}	
}