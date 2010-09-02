<?php
	$lang = array(
		'organization_name' => array
		(
			'required'		=> 'Введите название Организации.',
			'length'		=> 'Длинна названия организации должна быть не менее 3х и не более 70 символов.',
			'standard_text' => 'Имя пользователя введен неправильно.',
		),

		'organization_website' => array
		(
			'required' => 'Введите Веб сайт вашей Организации.',
			'url' => 'Введите URL правильно. Eg. http://www.ushahidi.com'
		),

		'organization_description' => array
		(
			'required' => 'Опишите Вашу организацию.'
		),

		'organization_email' => array
		(
			'email'		  => 'Email организации введен неправильно.',
			'length'	  => 'Длинна Email организации не должен быть менее 4х и более 100 символов.'
		),

		'organization_phone1' => array
		(
			'length'		=> 'Длинна телефонного номера не должна превышать 50 символов.'
		),

		'organization_phone2' => array
		(
			'length'		=> 'Длинна телефонного номера не должна превышать 50 символов'
		)
	);

?>