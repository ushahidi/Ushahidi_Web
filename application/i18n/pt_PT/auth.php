<?php

$lang = array
(
	'name' => array
	(
		'required'		=> 'O Campo nome completo é de preenchimento obrigatório.',
		'length'		=> 'O Campo nome completo deve ser preenchido com 3 a 100 caracteres.',
		'standard_text' => 'O Campo nome de utilizador contém caracteres não autorizados.',
		'login error'	=> 'Por favor certique que inseriu o nome correcto.'
	),
	
	'email' => array
	(
		'required'	  => 'O Campo email é de preenchimento obrigatório.',
		'email'		  => 'O campo de email, parece não conter um email válido?',
		'length'	  => 'O campo email deve ter entre 4 a 64 caracteres.',
		'exists'	  => 'Desculpe, mas já existe uma conta com este endereço de email.',
		'login error' => 'Por favor certique que inseriu o email correcto.'
	),

	'username' => array
	(
		'required'		=> 'O Campo nome de utilizador é de preenchimento obrigatório.',
		'length'		=> 'O Campo nome de utilizador deve ser preenchido com 3 a 16 caracteres',
		'alpha' => 'O nome de utilizador deve conter só letras.',
		'admin' 		=> 'The admin user role cannot be modified.',
		'superadmin'	=> 'The super admin role cannot be modified.',
		'exists'		=> 'Desculpe mas este nome já está a ser utilizado.',
		'login error'	=> 'Por favor certifique que inseriu o nome de utilizador correcto.'
	),

	'password' => array
	(
		'required'		=> 'O Campo password é de preenchimento obrigatório.',
		'length'		=> 'O Campo password deve ser preenchido com 6 a 12 caracteres.',
		'alpha_numeric'		=> 'A password deve conter só letras e números.',
		'login error'	=> 'Por favor, certifique que colocou a password correcta.',
		'matches'		=> 'Por favor, insira a mesma password nos dois campos.'
	),

	'password_confirm' => array
	(
		'matches' => 'Os campos das passwords devem ser iguais.'
	),

	'roles' => array
	(
		'required' => 'Você deve definir pelo menos um tipo de regra.',
		'values' => 'Tem de seleccionar a regra ADMIN ou USER.'
	),
	
	'resetemail' => array
        (
    	        'required' => 'O Campo email é de preenchimento obrigatório.',
       	        'invalid' => 'Desculpe, mas não temos o seu email na nossa base de dados.',
                'email'  => 'O campo de email, parece não conter um email válido?',
        ),

);
