<?php

$lang = array
(
	'parent_id' => array
	(
		'required'		=> 'يجب ملء حقل الفئة الأصلية',
		'numeric'		=> 'يجب ان يكون حقل الفئة الأصلية رقمى',
		'exists'		=> 'الفئة الأصلية غير موجودة',
		'same'			=> 'لا يجب تطابق الفئة والفئة الأصلية',
	),
	
	'category_title' => array
	(
		'required'		=> 'يجب ملء حقل العنوان',
		'length'		=> 'حقل العنوان يجب ألا يقل عن 3 حروف أو أرقام وألا يزيد على 80 حرف او رقم',
	),
	
	'category_description' => array
	(
		'required'		=> 'يجب ملء حقل التوصيف'
	),	
	
	'category_color' => array
	(
		'required'		=> 'يجب ملء حقل اللون',
		'length'		=> 'يجب ألا يقل حقل اللون عن 6 حروف',
	),
	
	'category_image' => array
	(
		'valid'		=> 'لا يبدو ان الحقل الخاص بالصور يحتوى على ملف صحيح',
		'type'		=> 'لا يبدو أن الحقل الخاص بالصور يحتوى على صورة سليمة. The only accepted formats are .JPG, .PNG and .GIF.',
		'size'		=> 'يجب الحرص أن لا يتعدى حجم ملف الصورة عن 50ك.ب.'
	),	
);