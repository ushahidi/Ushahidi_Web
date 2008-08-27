<?php
class Jquery_Validation_Core extends ArrayObject {
	
	protected $form;

	/**
	 * construct jquery_Validation object
	 * @param	object	object of type Validation
	 */
	public function __construct($form =null)
	{
		if(!empty($form) && $form instanceof Validate)
		{
			$this->load($form);
		}
	}
	/**
	 * Load actual form into object
	 * @param	object	validation object
	 */
	public function load(Validate $form)
	{
		
		$this->form=$form;
		$jquery=array();
		foreach ($this->form as $field=> $input)
		{
			if($input->get_name()!='')
			{
				$rules    = array();
				$messages = array();
				
				foreach($input->get_rules() as $rule)
				{
					if($input instanceof Element_Group)
						continue;
						
					$field_name=($input->get_screen_name()==null) ? $input->get_name() : $input->get_screen_name();
						
					//check all Kohana rules and match them with jQuery validate rules
					switch (get_class($rule))
					{
						case 'Rule_Required':
							$rules['required']= true;
							$messages['required']=str_replace('{name}',$field_name,$rule->get_message());
							break;
							
						case 'Rule_Email':
							$rules['email']   = true;
							$messages['email']=str_replace('{name}',$field_name,$rule->get_message());
							break;
														
						case 'Rule_Length':
							$rules['minlength']   = $rule->min_length;
							$rules['maxlength']   = $rule->max_length;							
							$messages['rangelength']=str_replace('{name}',$field_name,$rule->get_message());	
							break;
							
						case 'Rule_Url':
							$rules['url']   = true;
							$messages['url']=str_replace('{name}',$field_name,$rule->get_message());							
							break;
														
						case 'Rule_Digit':
							$rules['digit']   = true;
							$messages['digit']=str_replace('{name}',$field_name,$rule->get_message());	
							break;							
							
						case 'Rule_Numeric':
							$rules['numeric']   = true;
							$messages['numeric']=str_replace('{name}',$field_name,$rule->get_message());								
							break;														
					}
				}
				if($rules!=array()&&$messages!=array())
				{
					$jquery['rules'][$input->name]=$rules;
					$jquery['messages'][$input->name]=$messages;		
				}
				
				$this->set_spl($jquery);
				
			}
		}
		return $this;
		
	}
	//Do the spl magic with the array
	public function set_spl($jquery_array)
	{
		//spl magic
		$this->exchangeArray($jquery_array);
		$this->setFlags(ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST);		
	}
	//returns rules and messages as array
	public function as_array()
	{
		return $this->getArrayCopy();
	}
	//Load an array if you want to bypass Formation
	public function load_array(array $array)
	{
		$this->set_spl($array);
		return $this;
	}
	/*
	 * Return rules and messages
	 * @param	boolean	return as json or not
	 */
	public function get_rules_messages($json=true){
		$array=array($this['rules'],$this['messages']);
		
		return $json ? json_encode($array) : $array; 
	}
	
	//returns all rules and messages as json ready to be fed to jquery validation
	public function as_json()
	{
		return json_encode($this->as_array());
	}
	//Returns string which does the whole validation
	public function js_validate(){

		return '$().ready(function() {$("#'.$this->form->get_attr('id').'").validate('.$this->as_json().');});';
	 
	}
	//proxy to jquery_validation()
	public function __toString()
	{
		return	$this->js_validate();
	}

}
?>