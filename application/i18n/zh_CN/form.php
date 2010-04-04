<?php
	$lang = array
	(
		'form_title' => array
		(
			'required' => '请输入表单的名字',
			'length'   => '表单名字的长度必须在3至100个字符之间'
		),
		
		'form_description' => array
		(
			'required' => '请输入表单描述'
		),
		
		'form_id' => array
		(
			'default' => '默认表单不能删除',
			'required' => '请选择要添加的表单',
			'numeric' => '请选择要添加的表单'
		),
		
		'field_type' => array
		(
			'required' => '请选择属性类型',
			'numeric' => '请选择一个有效的类型'
		),
		
		'field_name' => array
		(
			'required' => '请输入属性名称',
			'length'   => '名称的长度应为3至100个字符'
		),
		
		'field_default' => array
		(
			'length'   => '长度应为3至200个字符'
		),
		
		'field_required' => array
		(
			'required' => '请选择是否为必填项',
			'between'   => '你输入的内容格式不正确'
		),
		
		'field_width' => array
		(
			'between' => '请选择属性输入框长度，须在0到300之间'
		),
		
		'field_height' => array
		(
			'between' => '请选择属性输入框高度，须在0到50之间'
		),
		
		'field_isdate' => array
		(
			'required' => '请选择是否为必填项',
			'between'   => '你输入的日期格式不正确'
		)
	);

?>