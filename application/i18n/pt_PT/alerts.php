<?php

$lang = array
(
	'alert_mobile' => array
	(
		'required'		=> 'É necessário inserir o número de telemóvel caso tenha seleccionado esta opção.',
		'numeric'		=> 'O campo do telemóvel parece não conter um número válido, por favor insira o número novamente e com o código do país(portugal +351).',
		'one_required'	=> 'Tem de inserir o seu número de telemóvel ou email.',
		'mobile_check'	=> 'O seu telemóvel já se encontra registado na nossa base de dados para receber alertas nessa localização',
		'length'		=> 'O campo do número de telemóvel parece não conter o número de digitos válidos.'
	),
	
	'alert_email' => array
	(
		'required'		=> 'É necessário inserir o seu email, caso tenha seleccionado essa opção.',
		'email'		  => 'O campo de email parece estar mal preenchido?',
		'length'	  => 'O campo de email deve ter entre 4 a 64 caracteres de comprimento.',
		'email_check'	=> 'O seu email já se encontra registado na nossa base de dados para receber alertas nessa localização',
		'one_required' => ''
	),
	
	'alert_lat' => array
	(
		'required'		=> 'Não seleccionou o local válido no mapa.',
		'between' => 'Não seleccionou o local válido no mapa.'
	),
	
	'alert_lon' => array
	(
		'required'		=> 'Não seleccionou o local válido no mapa.',
		'between' => 'Não seleccionou o local válido no mapa.'
	),
	
	'alert_radius' => array
	(
		'required'		=> 'Não seleccionou um valor válido para o raio em torno da localização pretendida.',
		'in_array' => 'ão seleccionou um valor válido para o raio em torno da localização pretendida.'
	),

    'code_not_found' => 'Este código de verificação não é válido! Por favor confirme que tem o URL correcto.',
    'code_already_verified' => 'Este código já foi verificado anteriormente!',
    'code_verified' => ' O seu código foi verificado correctamente. A partir deste momento será notificado dos relatos sempre que eles se registarem.',
    'mobile_alert_request_created' => 'O seu pedido de alerta sms foi registado e será enviado em breve uma mensagem de verificação para ',
	'verify_code' => 'Não receberá nenhum alerta até confirmar o seu pedido.',
	'mobile_code' => 'Por favor, insira o código de verificação enviado por SMS para o seguinte número: ',
	'mobile_ok_head' =>'O seu pedido de alertas por SMS foi registado!',
	'mobile_error_head' => 'O seu pedido de alertas por SMS NÃO FICOU registado!',
	'error' => 'O sistema não consegui processar o seu pedido!',
	'settings_error' => 'Esta funcionalidade não está configurada correctamente para enviar alertas',
	'email_code' => 'Insira no campo seguinte o código de confirmação enviado por email: ',
	'email_alert_request_created' => 'O seu pedido de alerta por email foi registado e será enviado em breve uma mensagem de verificação para',
	'email_ok_head' =>'O seu pedido de alertas por EMAIL foi registado!',
	'email_error_head' => 'O seu pedido de alertas por EMAIL NÃO FICOU registado!',
    'create_more_alerts' => 'Voltar à página de alertas para criar novos alertas',
	'unsubscribe' => 'Você está a receber este email porque subscreveu os nossos alertas. Caso não pretenda receber mais os nossos avisos por favor click em ',
	'verification_email_subject' => 'alerts - verificação',
	'confirm_request' => 'Para confirmar o seu pedido de alerta por favor vá a ',
	'unsubscribed' => 'Nunca mais irá receber alertas de ',
	'unsubscribe_failed' => 'Não estamos a conseguir retirar o seu contacto da base de dados. Por favor confirm que utilizou o URL correcto.'
);
