<?php
	$lang = array
	(
		'form_title' => array
		(
			'required' => 'أدخل اسم النموذج',
			'length'   => 'حقل اسم النموذج يجب ان يحتوى على الأقل 3 حروف ويجب ألا يزيد على 100 حرف.'
		),
		
		'form_description' => array
		(
			'required' => 'أدخل توصيف النموذج'
		),
		
		'form_id' => array
		(
			'default' => 'لا يمكن حذف النموذج الأصلى',
			'required' => 'حدد النموذج الذى تريد إضافة هذا الحقل إليه',
			'numeric' => 'حدد النموذج الذى تريد إضافة هذا الحقل إليه'
		),
		
		'field_type' => array
		(
			'required' => 'حدد نوع الحقل',
			'numeric' => 'حدد نوع حقل صحيح'
		),
		
		'field_name' => array
		(
			'required' => 'أدخل اسم الحقل',
			'length'   => 'اسم الحقل يجب ألا يقل عن 3 حروف وألا يزيد عن 100 حرف'
		),
		
		'field_default' => array
		(
			'length'   => 'اسم الحقل يجب ألا يقل عن 3 حروف وألا يزيد عن 200 حرف'
		),
		
		'field_required' => array
		(
			'required' => 'حدد نعم أو لا للحقل المطلوب',
			'between'   => 'لقد أدخلت قيمة غير صالحة للحقل المطلوب'
		),
		
		'field_width' => array
		(
			'between' => 'لتحديد عرض الحقل أدخل قيمة من 0 إلى 300'
		),
		
		'field_height' => array
		(
			'between' => 'لتحديد طول الحقل أدخل قيمة من 0 إلى 50'
		),
		
		'field_isdate' => array
		(
			'required' => 'حدد نعم أو لا للحقل الخاص بالتاريخ',
			'between'   => 'لقد أدخلت قيمة غير صالح فى الحقل الخاص بالتاريخ'
		)
	);

?>