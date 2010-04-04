<?php

$lang = array
(
	'contact_name' => array
	(
		'required'		=> '名字是必填项',
		'length'        => '名字长度必须超过3个字符'
	),

	'contact_subject' => array
	(
		'required'		=> '主题是必填项',
		'length'        => '主题长度必须超过3个字符'
	),
	
	'contact_message' => array
	(
		'required'        => '消息内容是必填项'
	),
	
	'contact_email' => array
	(
		'required'    => '打扣可将邮件设为必填项',
		'email'		  => '邮件地址格式不正确',
		'length'	  => '邮件地址长度应为4至64个字符'
	),
	
	'captcha' => array
	(
		'required' => '请输入验证码', 
		'default' => '验证码输入不正确'
	)
	
);
