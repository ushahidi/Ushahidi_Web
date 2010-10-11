<?php
	$lang = array(
	'category_color' => array(
		'length' => 'يجب ألا يقل حقل اللون عن 6 حروف',
		'required' => 'يجب ملء حقل اللون',
	),
	'category_description' => array(
		'required' => 'يجب ملء حقل التوصيف',
	),
	'category_image' => array(
		'size' => 'يجب الحرص أن لا يتعدى حجم ملف الصورة عن 50ك.ب.',
		'type' => 'لا يبدو أن الحقل الخاص بالصور يحتوى على صورة سليمة. The only accepted formats are .JPG, .PNG and .GIF.',
		'valid' => 'لا يبدو ان الحقل الخاص بالصور يحتوى على ملف صحيح',
	),
	'category_title' => array(
		'length' => 'حقل العنوان يجب ألا يقل عن 3 حروف أو أرقام وألا يزيد على 80 حرف او رقم',
		'required' => 'يجب ملء حقل العنوان',
	),
	'parent_id' => array(
		'exists' => 'الفئة الأصلية غير موجودة',
		'numeric' => 'يجب ان يكون حقل الفئة الأصلية رقمى',
		'required' => 'يجب ملء حقل الفئة الأصلية',
		'same' => 'لا يجب تطابق الفئة والفئة الأصلية',
	));
?>
