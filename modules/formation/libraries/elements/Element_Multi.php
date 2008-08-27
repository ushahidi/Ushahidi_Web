<?php
class Element_Multi_Core extends Element_Input{
	
	protected $options=array();
	
	public function set_options($options)
	{
		$this->options=$options;
		return $this;		
	}
	public function get_options($options)
	{
		$this->options;
	}
	public function add_option($option,$value)
	{
		$this->options[$option]=$value;
	}
}
?>