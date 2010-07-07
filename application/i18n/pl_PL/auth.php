<?php



$lang = array
(
	'name' => array
	(
		'required'		=> 'Wymagane jest wypełnienie pełnej nazwa pola.',
		'length'		=> 'Pełna nazwa pola musi mieć co najmniej 3 i nie więcej niż 100 znaków.',
		'standard_text' => 'Pole nazwy użytkownika zawiera niedozwolone znaki.',
		'login error'	=> 'Sprawdź, czy wprowadziłeś właściwą nazwę.'
	),
	
	'email' => array
	(
		'required'	  => 'Pole adresu emailowego musi być wypełnione.',
		'email'		  => 'Pole adresu emailowego prawdopodobnie nie zawiera ważnego adresu emailowego',
		'length'	  => 'Adres emailowy musi mieć co najmniej  4 i nie więcej niż 64 znaki.',
		'exists'	  => 'Przepraszamy, dla tego adresu emailowego już istnieje konto  użytkownika.',
		'login error' => 'Sprawdź, czy wprowadziłeś właściwy adres emailowy.'
	),

	'username' => array
	(
		'required'		=> 'Pole nazwy użytkownika musi być wypełnione.',
		'length'		=> 'Pole nazwy użytkownika musi mieć co najmniej 2 i nie więcej niż 16 znaków.',
		'standard_text' => 'Pole nazwy użytkowniak zawiera niedozwolone znaki.',
		'admin' 		=> 'Funkcja administratora/użytkownika nie może być zmodyfikowana.',
		'superadmin'	=> 'Funkcja superadministratora nie może być zmodyfikowana.',
		'exists'		=> 'Przepraszamy, ta nazwa użytkownika jest już używana.',
		'login error'	=> 'Sprawdź, czy wprowadziłeś właściwą nazwę użytkownika.'
	),

	'password' => array
	(
		'required'		=> 'Pole hasła musi być wypełnione.',
		'length'		=> 'Pole hasła musi mieć co najmniej 5 i nie więcej niż 16 znaków.',
		'standard_text' => 'Pole hasła zawiera niedozwolone znaki.',
		'login error'	=> 'Sprawdź, czy wprowadziłeś właściwe hasło.',
		'matches'		=> 'Wprowadź to samo hasło w obydwu polach hasła.'
	),

	'password_confirm' => array
	(
		'matches' => 'Pole potwierdzenia hasła musi odpowiadać polu hasła.'
	),

	'roles' => array
	(
		'required' => 'Musisz zdefiniować co najmniej jedną funkcję.',
		'values' => 'Musisz wybrać funkcję albo ADMINISTRATORA, albo UŻYTKOWNIKA.'
	),
	
	'resetemail' => array
        (
    	        'required' => 'Pole adresu emailowego musi być wypełnione.',
       	        'invalid' => 'Przepraszamy, nie mamy twojego adresu emailowego',
                'email'  => 'Pole adresu emailowego prawdopodobnie nie zawiera ważnego adresu emailowego',
        ),

        'forgot_password' => 'Zapomniałeś hasła?',
);
