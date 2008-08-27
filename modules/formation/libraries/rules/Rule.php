<?php

 class Rule_Core {

	protected $is_valid;
	
	protected $language_file='validation';
	
	protected $message_vars=array();
	
	protected $message;
	
	protected $value;
	
	public function __get($key)
	{
		if(isset($this->message_vars[$key]))
		{
			return $this->message_vars[$key];
		}
	}
	public function __set($key,$value)
	{
		$this->message_vars[$key]=$value;
	}
	public function __construct(array $arguments=null)
	{
		if($arguments != null)
		{
			$this->set_message_vars($arguments);		
		}

	}
	public function set_language_file($file)
	{
		$this->language_file=$file;
		return $this;
	}
	public function set_message($message)
	{
		$this->message=(string) $message;	
	}
	public function get_message()
	{
		$message=Kohana::lang($this->language_file.'.'.strtolower(get_class($this)));
			
		if($this->message!=null)
		{
			$message=$this->message;	
		}
		$message=(string) $this->parse_message($message);
		
		return $message;
	}
	protected function parse_message($message)
	{	
		foreach($this->message_vars as $key=>$var)
		{
			$message=str_replace('{'.$key.'}',(string) $var,$message);
		}
		return str_replace('{value}',$this->value,$message);			
	}
	protected function set_message_vars(array $vars=array())
	{
		foreach( $vars as $key=>$argument)
		{
			$this->message_vars[$key]=$argument;
		}
	}	
}