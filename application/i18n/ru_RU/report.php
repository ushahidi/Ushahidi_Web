<?php

/**
 *
 * translated by Ismailov A. altyni@gmail.com
 * 
 * Misprints, mistypes, language errors and mistakes, new vars are
 * fixed by Sergei 'the_toon' Plaxienko sergei.plaxienko@gmail.com
 *
 * ru_RU
 *
 */

$lang = array
(
	'locale' => array
	(
		'required'	=> 'Введите местоположение.',
		'length'	=> 'Местоположение введен неверно. ',
		'alpha_dash'	=> 'Поле Местоположения введено неправильно. ',
		'locale'	=> 'Сообщение о событии и перевод имеет одинаковое местоположение (перевод)',
		'exists'	=> 'Данное сообщение уже имеет перевод на данный язык'
	),

	'incident_title' => array
	(
		'required'	=> 'Введите название.',
		'length'	=> 'Длина Названия не может быть менее 3х и более 200 символов.'
	),

	'incident_description' => array
	(
		'required'	=> 'Введите описание.'
	),	

	'incident_date' => array
	(
		'required'	=> 'Введите дату.',
		'date_mmddyyyy' => 'Дата введена в неправильном формате',
		'date_ddmmyyyy' => 'Дата введена в неправильном формате'
	),

	'incident_hour' => array
	(
		'required'	=> 'Введите часы.',
		'between'	=> 'Часы введены в неправильном формате.'
	),

	'incident_minute' => array
	(
		'required'	=> 'Введите минуты.',
		'between' 	=> 'Минуты введены в неправильном формате.'
	),

	'incident_ampm' => array
	(
		'validvalues'	=> 'Неправильно указано время суток'
	),

	'latitude' => array
	(
		'required'	=> 'Введите координаты широты местности. Нажмите на карте чтобы поставить точку местоположения.',
		'between'	=> 'Координаты широты местности введены неправильно'
	),

	'longitude' => array
	(
		'required'	=> 'Введите координаты долготы местности. Нажмите на карте чтобы поставить точку местоположения.',
		'between'	=> 'Координаты долготы местности введены неправильно'
	),

	'location_name' => array
	(
		'required'		=> 'Введите название местоположения.',
		'length'		=> 'Длина названия местоположения не может быть менее 3х и более 200 символов.',
	),

	'incident_category' => array
	(
		'required'		=> 'Введите категорию.',
		'numeric'		=> 'категория введена неверно'
	),

	'incident_news' => array
	(
		'url'		=> 'URL ссылки новостей введен неправильно.'
	),

	'incident_video' => array
	(
		'url'		=> 'Ссылка на видео введен неверно'
	),

	'incident_photo' => array
	(
		'valid'		=> 'Загружаемые файлы картинок испорчены или в неправильном формате',
		'type'		=> 'Загружаемые файлы картинок в неверном формате, введите файлы в .JPG, .PNG и .GIF формате.',
		'size'		=> 'Загружаемый файл не может превышать 2MB.'
	),

	'person_first' => array
	(
		'length'		=> 'Длина имени не должно быть менее 3х и более 100 символов.'
	),

	'person_last' => array
	(
		'length'		=> 'Длина Фамилии не должна быть менее 3х и более 100 символов.'
	),

	'person_email' => array
	(
		'email'		  => 'Email введен в неправильном формате',
		'length'	  => 'Длина Email не должна быть менее 4х и более 64 символов.'
	),

	// Admin - Report Download Validation
	'data_point' => array
	(
		'required'		  => 'Выберите отчёт в правильном формате для загрузки',
		'numeric'		  => 'Выберите отчёт в правильном формате для загрузки',
		'between'		  => 'Выберите отчёт в правильном формате для загрузки'
	),
	'data_include' => array
	(
		'numeric'		  => 'Выберите правильный формат для загрузки',
		'between'		  => 'Выберите правильный формат для загрузки'
	),
	'from_date' => array
	(
		'date_mmddyyyy'		  => 'Поле даты ОТ содержит неверные значения',
		'range'	  => ' Введите правильный формат даты для ОТ поля. Он не может быть больше сегодняшнего числа.'
	),
	'to_date' => array
	(
		'date_mmddyyyy'		  => 'Поле даты ДО содержит неверные значения',
		'range'	  => 'Введите правильный формат даты для ДО поля. Он не может быть больше сегодняшнего числа.',
		'range_greater'	=> 'Дата поля ОТ не может быть больше чем поля даты ДО.'
	),

	'custom_field' => array
	(
		'values'		  => 'Введите значения в формы в правильном формате'
	),

	'incident_active' => array
	(
		'required'		=> 'Введите правильные значения для подтверждения этого события',
		'between'		=> 'Введите правильные значения для подтверждения этого события'
	),

	'incident_verified' => array
	(
		'required'		=> 'Введите правильные значения для проверки этого события',
		'between'		=> 'Введите правильные значения для проверки этого события'
	),

	'incident_source' => array
	(
		'alpha'		=> 'Введите правильные значения для введения достоверности источника',
		'length'		=> 'Введите правильные значения для введения достоверности источника'
	),

	'incident_information' => array
	(
		'alpha'		=> 'Введите правильные значения для введения вероятности информации',
		'length'		=> 'Введите правильные значения для введения вероятности информации'
	),

	'comments_form_leave_comment' => 'Прокомментировать',
	'comments_form_error' => 'Ошибка!',
	'comments_form_name' => 'Имя',
	'comments_form_email' => 'Электронная почта',
	'comments_form_comment' => 'Комментарий',
	'comments_form_security_code' => 'Секретный код',
	'comments_form_submit_comment' => 'Отправить',

	'comments_additional_reports' => 'Дополнительные материалы и обсуждение',
	'comments_add' => 'Добавить',
	'comments_credibility' => 'Надёжность',

	'view_verified' => 'Проверено',
	'view_unverified' => 'Не проверено',
	'view_location' => 'Местоположение',
	'view_date' => 'Дата',
	'view_time' => 'Время',
	'view_category' => 'Категория',
	'view_incident' => 'Событие',
	'view_nearby_incident' => 'Ближайшее событие',
	'view_alt_incident' => 'Событие',
	'view_alt_nearby_incident' => 'Ближайшее событие',
	'view_incident_report_description' => 'Детальное описание события',
	'view_credibility' => 'Надёжность',
	'view_images' => 'Изображения',
	'view_videos' => 'Видео',
	'view_incident_reports' => 'Сообщение(я) о событии',
	'view_incident_reports_title' => 'Краткое описание',
	'view_incident_reports_location' => 'Местоположение',
	'view_incident_reports_date' => 'Дата',

	'geocode_not_found' => ' не найден!\nВведите больше информации,\nнапример, город и страну,\nили введите ближайщий город\nи переместите указатель к нужной позиции.'
);
?>