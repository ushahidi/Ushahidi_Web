<?php
	$lang = array(
	'category_color' => array(
		'length' => '颜色值长度应是6个字符',
		'required' => '颜色是必填项',
	),
	'category_description' => array(
		'required' => '栏目描述是必填项',
	),
	'category_image' => array(
		'size' => '图片文件大小不能超过50K',
		'type' => '图片文件格式不正确，只能上传 .JPG, .PNG 和 .GIF',
		'valid' => '图片文件格式不正确',
	),
	'category_title' => array(
		'length' => '标题必须在3至80个字符之间',
		'required' => '标题是必填项',
	),
	'parent_id' => array(
		'exists' => '父栏目不存在',
		'numeric' => '父栏目必须是数字',
		'required' => '父栏目是必填项',
		'same' => '不能与父栏目相同',
	));
?>
