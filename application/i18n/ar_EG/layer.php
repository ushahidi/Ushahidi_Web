<?php

$lang = array
(
	'layer_name' => array
	(
		'required'		=> 'يجب إدخال حقل الإسم',
		'length'		=> 'يجب ان يحتوى حقل الاسم على الأقل 3 حروف وليس اكثر من 80 حرف',
	),
	
	'layer_url' => array
	(
		'url' => 'أدخل رابط صحيح. Eg. http://www.ushahidi.com/layerl.kml',
		'atleast' => 'يجب إدخال رابط KML أو ملف',
		'both' => 'لا يمكنك الحصول على ملف KML ورابط فى نفس الوقت'
	),
	
	'layer_color' => array
	(
		'required'		=> 'يجب إدخال حقل اللون',
		'length'		=> 'يجب ان يحتوى حقل اللون على 6 حروف',
	),
	
	'layer_file' => array
	(
		'valid'		=> 'لا يبدو ان هذا الحقل يحتوى على قيمة صالحة',
		'type'		=> 'لايبدو ان حقل الملفات يحتوى على ملف صالح. The only accepted formats are .KMZ, .KML.'
	),	
);