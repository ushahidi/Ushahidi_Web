<?php
class Element_Group_Core extends Formation {
	
	protected $name;
	protected $_order;

	public function __construct($name)
	{
		$this->legend=$name;
		$this->name=$name;
	}
	public function get_name()
	{
		return $this->name;
	}
	public function get_value()
	{
		return $this->as_array();
	}
	public function render()
	{
		$form=new View($this->template);

		//Forms are already opened
		$form->open  = '';
		$form->close = '';
		// Set the inputs
		$form->inputs = $this;
		
		//Errors and messages passed on to the form
		$form->errors= $this->errors();
		
		//Set template variables set with __set
		$form->set($this->template_vars);
		
		return $form;
		
	}	
	public function set_attr()
	{
		return false;
	}
	public function get_attr()
	{
		return false;
	}
	public function get_order()
	{
		return $this->_order;
	}
	public function set_order($value)
	{
		$this->_order=(int) $value;
		return $this;
	}
}
?>