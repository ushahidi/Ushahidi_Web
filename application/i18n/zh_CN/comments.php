<?php

$lang = array
(
	'comment_author' => array
	(
		'required'		=> '名字是必填项',
		'length'        => '名字长度须大于3个字符'
	),
	
	'comment_description' => array
	(
		'required'        => '评论内容是必填项'
	),
	
	'comment_email' => array
	(
		'required'    => '打钩可将邮件地址设为必填项',
		'email'		  => '邮件地址格式不正确',
		'length'	  => '邮件地址长度应在4至64个字符之间'
	),
	
	'captcha' => array
	(
		'required' => '请输入验证码', 
		'default' => '验证码输入不正确'
	)
	
);
