<?php defined('SYSPATH') or die('No direct script access.');

class Element_Csrf_Core extends Element_Hidden {


	public function __construct($name,$salt='lkjaslh')
	{
		
		parent::__construct($name);
		if(is_array($salt))
		{
			$salt=$salt[0];
		}
		$no_csrf='no_csrf';
		
		$session=Session::instance();
		
		$hash=sha1(uniqid().$salt);
		
		if($session->get($no_csrf,false)==false)
		{
			$session->set($no_csrf,$hash);
		}
		else
		{
			$hash=$session->get($no_csrf);
		}
		
		$this->set_value($hash);
		$this->add_rule('Rule_Required');
		$this->add_rule('Rule_Csrf',$hash);
	}
	

} // End F