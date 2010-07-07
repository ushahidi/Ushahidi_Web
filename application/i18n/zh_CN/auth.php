<?php

$lang = array
(
	'name' => array
	(
		'required'		=> '全名是必填项',
		'length'		=> '全名的长度须在3至100个字符之间',
		'standard_text' => '用户名包含不合法字符',
		'login error'	=> '你输入的名字不正确'
	),
	
	'email' => array
	(
		'required'	  => '邮件是必填项',
		'email'		  => '邮件地址格式不正确',
		'length'	  => '邮件地址长度须在4至64个字符之间',
		'exists'	  => '对不起，这个邮件地址已被使用',
		'login error' => '你输入的邮件地址不正确'
	),

	'username' => array
	(
		'required'		=> '用户名是必填项',
		'length'		=> '用户名长度须在2至16个字符之间',
		'standard_text' => '用户名包含非法字符',
		'admin' 		=> '管理员帐户不能被修改',
		'superadmin'	=> '超级管理员帐户不能被修改',
		'exists'		=> '对不起，这个用户名已被使用',
		'login error'	=> '你输入的用户名不正确'
	),

	'password' => array
	(
		'required'		=> '密码是必填项',
		'length'		=> '密码长度须在5至16个字符之间',
		'standard_text' => '密码包含非法字符',
		'login error'	=> '你输入的密码不正确',
		'matches'		=> '你输入的两次密码不一致'
	),

	'password_confirm' => array
	(
		'matches' => '你输入的两次密码不一致'
	),

	'roles' => array
	(
		'required' => '至少要定义一个角色',
		'values' => '必须选择 ADMIN 或 USER 角色.'
	),
	
	'resetemail' => array
        (
    	        'required' => '邮件是必填项',
       	        'invalid' => '对不起，我们没有你的邮件地址',
                'email'  => '邮件地址格式不正确',
        ),

);
