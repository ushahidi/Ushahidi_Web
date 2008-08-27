<?php

class Rule_Upload_Required_Core extends Rule{
	public function __construct(array $arguments=null)
	{
		if($arguments != null)
		{
			$this->set_message_vars($arguments);		
		}

	}
	public function is_valid($upload=null)
	{
		$this->message_vars['filename']=$upload['name'];
				
		$this->set_message_vars((array) $upload);
		
		if (empty($upload) OR $upload['error'] === UPLOAD_ERR_NO_FILE)
		{
			return false;
		}
		return true;
	}	
/*	protected function set_message_vars($vars)
	{
	
	}*/	
}