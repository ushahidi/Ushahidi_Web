<?php

class Rule_Upload_Allowed_Core extends Rule_Upload_Required{

	public function __construct(array $allowed)
	{
		$this->set_allowed($allowed);		
	}
	public function set_allowed(array $allowed)
	{	
		$this->message_vars['allowed']=$allowed;
		
		return $this;
	}	
	public function is_valid($upload=null)
	{
		if(count($allowed=$this->message_vars['allowed'])==0)
			return false;

		$this->message_vars['filename']=$upload['name'];
		
		$this->set_message_vars($upload);
						
		if (defined('FILEINFO_MIME'))
		{
			$info = new finfo(FILEINFO_MIME);

			// Get the mime type using Fileinfo
			$mime = $info->file($upload['tmp_name']);

			$info->close();
		}
		elseif (ini_get('magic.mime') AND function_exists('mime_content_type'))
		{
			// Get the mime type using magic.mime
			$mime = mime_content_type($upload['tmp_name']);
		}
		else
		{
			// Trust the browser
			$mime = $upload['type'];
		}
		$this->message_vars['mimetype']=$mime;

		// Allow nothing by default
		$allow = FALSE;
		
		$types=$this->message_vars['allowed'];
		
		foreach ($types as $type)
		{
			if (in_array($mime, Config::item('mimes.'.$type)))
			{
				// Type is valid
				$allow = TRUE;
				break;
			}
		}

		if ($allow === FALSE)
		{
			return false;
		}
		return true;
	}

}