<?php

$lang = array
(
'contact_name' => array
(
'required' => 'Введите имя.',
'length' => 'Длинна Поля Имени не должна содержать менее 3х символов.'
),

'contact_subject' => array
(
'required' => ' Введите тему.',
'length' => 'Длинна темы не может быть менее 3х символов.'
),

'contact_message' => array
(
'required' => 'Введите сообщение.'
),

'contact_email' => array
(
'required'		=> 'Нужно ввести адрес Email, или уберите галочку.',
		'email'		  => 'Ваш Email адрес введен неверно, введите правильно.',
		'length'	  => 'Длинна Email не может быть менее 4х и более 64х символов.'
),

'captcha' => array
(
'required' => 'Введите защитный код', 
		'default' => 'Введите правильный защитный код'
)

);

