<?php
	$lang = array
	(
		'form_title' => array
		(
			'required' => 'Wprowadź nazwę formularza.',
			'length'   => 'Pole nazwy formularza musi mieć co najmniej 3 i nie więcej niż 100 znaków.'
		),
		
		'form_description' => array
		(
			'required' => 'Wprowadź opis formularza\formularzy.'
		),
		
		'form_id' => array
		(
			'default' => 'Domyślny formularz nie może być usunięty.',
			'required' => 'Wybierz formularz, który chcesz dodać do tego pola.',
			'numeric' => 'Wybierz formularz, który chcesz dodać do tego pola.'
		),
		
		'field_type' => array
		(
			'required' => 'Wybierz typ pola.',
			'numeric' => 'Wybierz ważny typ pola.'
		),
		
		'field_name' => array
		(
			'required' => 'Wprowadź nazwę pola.',
			'length'   => 'Nazwa pola musi zawierać co najmniej 3 i nie więcej niż 100 znaków.'
		),
		
		'field_default' => array
		(
			'length'   => 'Nazwa pola musi zawierać co najmniej 3 i nie więcej niż 200 znaków.'
		),
		
		'field_required' => array
		(
			'required' => 'Wybierz odpowiedź Tak lub Nie dla wymaganego pola',
			'between'   => 'Wprowadziłeś nieważną wartość w wywaganym polu'
		),
		
		'field_width' => array
		(
			'between' => 'Wprowadź wartość do 0 do 300 dla szerokości pola'
		),
		
		'field_height' => array
		(
			'between' => 'Wprowadź wartość do 0 do 50 dla wysokości pola'
		),
		
		'field_isdate' => array
		(
			'required' => 'Wybierz odpowiedź Tak lub Nie dla daty pola',
			'between'   => 'Wprowadziłeś nieważną wartość dla daty pola'
		)
	);

?>