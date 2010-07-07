<?php

$lang = array
(
	'parent_id' => array
	(
		'required'		=> '父栏目是必填项',
		'numeric'		=> '父栏目必须是数字',
		'exists'		=> '父栏目不存在',
		'same'			=> '不能与父栏目相同',
	),
	
	'category_title' => array
	(
		'required'		=> '标题是必填项',
		'length'		=> '标题必须在3至80个字符之间',
	),
	
	'category_description' => array
	(
		'required'		=> '栏目描述是必填项'
	),	
	
	'category_color' => array
	(
		'required'		=> '颜色是必填项',
		'length'		=> '颜色值长度应是6个字符',
	),
	
	'category_image' => array
	(
		'valid'		=> '图片文件格式不正确',
		'type'		=> '图片文件格式不正确，只能上传 .JPG, .PNG 和 .GIF',
		'size'		=> '图片文件大小不能超过50K'
	),	
);