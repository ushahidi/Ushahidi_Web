<?php

$lang = array
(
	'alert_mobile' => array
	(
		'required'		=> '打钩将手机号码设为必填项',
		'numeric'		=> '手机号码格式不正确，请输入数字，需包含国家代码',
		'one_required'	=> '你必须输入手机号码或邮件地址',
		'mobile_check'	=> '手机号码已注册，可收取信息更新',
		'length'		=> '手机号码的位数不正确'
	),
	
	'alert_email' => array
	(
		'required'		=> '打钩将邮件地址设为必填项',
		'email'		  => '邮件地址格式不正确',
		'length'	  => '邮件地址长度须在4至64个字符之间',
		'email_check'	=> '邮件地址已注册，可收取信息更新',
		'one_required' => ''
	),
	
	'alert_lat' => array
	(
		'required'		=> '你需要在地图上选择一个位置',
		'between' => '你需要在地图上选择一个位置'
	),
	
	'alert_lon' => array
	(
		'required'		=> '你需要在地图上选择一个位置',
		'between' => '你需要在地图上选择一个位置'
	),
	
	'alert_radius' => array
	(
		'required'		=> '你需要在地图上选择一个半径',
		'in_array' => '你需要在地图上选择一个半径'
	),

    'code_not_found' => '验证码没找到！请确认你输入的URL是否正确',
    'code_already_verified' => '这个验证码已经使用过了',
    'code_verified' => ' 验证通过，当有事件发生时你会收到消息',
    'mobile_alert_request_created' => '你的手机灾害警报请求已创建，验证码将发送到 ',
	'verify_code' => '在你确认请求之前，不会收到信息更新',
	'mobile_code' => '请输入你收到的验证码 ',
	'mobile_ok_head' =>'你的手机灾害警报请求已被保存',
	'mobile_error_head' => '你的手机灾害警报请求没有被保存！',
	'error' => '系统不能处理你的验证请求！',
	'settings_error' => '系统配置不正确，不能正确处理灾害警报',
	'email_code' => '请输入你收到的邮件验证码 ',
	'email_alert_request_created' => '你的邮件灾害警报请求已创建，验证码将发送到 ',
	'email_ok_head' =>'你的邮件灾害警报请求已被保存',
	'email_error_head' => '你的邮件灾害警报请求没有被保存',
    'create_more_alerts' => '返回灾害警报页面创建更多',
	'unsubscribe' => '你收到这封邮件是因为你订阅了灾害警报。如果你不想再收到请点击 ',
	'verification_email_subject' => '灾害警报 - 验证',
	'confirm_request' => '验证你的警报发送请求，请点击 ',
	'unsubscribed' => '你不会再收到警报请求 ',
	'unsubscribe_failed' => '我们不能取消你的警报订阅，请确认你输入了正确的URL'
);
