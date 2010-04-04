<?php

$lang = array
(
	'alert_mobile' => array
	(
		'required'		=> 'Pole telefonu komórkowego musi być wypełnione, jeśli zaznaczono pole wyboru.',
		'numeric'		=> 'Pole telefonu komórkowego prawdopodobnie nie zawiera ważnego numeru telefonu. Prosimy wprowadzać numery jedynie wraz z prefiksem kraju.',
		'one_required'	=> 'Musisz wprowadzić swój numer telefonu komórkowego lub adres emailowy.',
		'mobile_check'	=> 'Ten numer telefonu komórkowego został już zarejestrowany jako otrzymujący alarmy dla tej lokalizacji',
		'length'		=> 'Pole telefonu komórkowego prawdopodobnie nie zawiera odpowiedniej ilości cyfr.'
	),
	
	'alert_email' => array
	(
		'required'		=> 'Pole adresu emailowego musi być wypełnione, jeśli zaznaczono pole wyboru.',
		'email'		  => 'Pole adresu emailowego prawdopodobnie nie zawiera ważnego adresu emailowego',
		'length'	  => 'Pole adresu emailowego musi zawierać co najmniej 4 i nie więcej niż 64 znaki.',
		'email_check'	=> 'Pole adresu emailowego zostało już zarejestrowane jako otrzymujące alarmy dla tej lokalizacji.',
		'one_required' => ''
	),
	
	'alert_lat' => array
	(
		'required'		=> 'Poprawna lokalizacja nie została wybrana na mapie.',
		'between' => 'Poprawna lokalizacja nie została wybrana na mapie.'
	),
	
	'alert_lon' => array
	(
		'required'		=> 'Poprawna lokalizacja nie została wybrana na mapie.',
		'between' => 'Poprawna lokalizacja nie została wybrana na mapie.'
	),
	
	'alert_radius' => array
	(
		'required'		=> 'Proszę zaznaczyć promień alertów na mapie.',
		'in_array' => 'Proszę zaznaczyć promień alertów na mapie.'
	),

    'code_not_found' => 'Kod weryfikacyjny nie został odnaleziony. Proszę zweryfikuj czy wprowadziłeś do przeglądarki poprawny URL.',
    'code_already_verified' => 'Ten kod weryfikacujny już został potwierdzony.',
    'code_verified' => ' Weryfikacja przebiegła pomyślnie, będziesz na bieżąco informowany o alertach.',
    'mobile_alert_request_created' => 'Your Mobile Alert request has been created and verification message has been sent to ',
	'verify_code' => 'Nie będziesz otrzymywał alertów dot. danej lokalizacji, do momentu kiedy Twój email nie zostanie potwierdzony (po przez kod weryfikacyjny).',
	'mobile_code' => 'Please enter the SMS confirmation code you received on your mobile phone below: ',
	'mobile_ok_head' =>'Your Mobile Alert Request Has Been Saved!',
	'mobile_error_head' => 'Your Mobile Alert Request Has NOT Been Saved!',
	'error' => 'Nastąpił błąd systemu. Zgłoś to proszę obsłudze systemu.',
	'settings_error' => 'Ta instalacja nie została skonfigurowana do obsługi powiadomień o alertach.',
	'email_code' => 'Proszę wprowadź poniżej kod weryfikujący, który otrzymałeś drogą poczty elektronicznej: ',
	'email_alert_request_created' => 'Notyfikacja o alertach drogą poczty elektronicznej na Twój adres email została utworzona a wiadomość wraz z kodem weryfikacyjnym została wysłana na adres ',
	'email_ok_head' => 'Notyfikacja o alertach na Twój adres email została zachowana!',
	'email_error_head' => 'Notyfikacja o alertach na Twój adres email NIE została zachowana!',
    'create_more_alerts' => 'Powróć do strony z alertami, aby utworzyć kolejne notyfikacje.',
	'unsubscribe' => 'Otrzymałeś tę wiadomość, ponieważ Twój adres email został zapisany w naszym systemie w celu notyfikacji o tworzonych na bieżąco alertach. Jeżeli pragniesz otrzymywać notyfikacje, kliknij na poniższy link (bądź skopiuj go i wklej w okno adresu przeglądarki): ',
	'verification_email_subject' => 'notyfikacje o alertach - weryfikacja',
	'confirm_request' => 'Aby potwierdzić notyfikację o alertach na Twój adres email, kliknij na poniższy link (bądź skopiuj go i wklej w okno adresu przeglądarki): ',
	'unsubscribed' => 'Notyfikacja o alertach została anulowana dla adresu ',
	'unsubscribe_failed' => 'Niestety ale nie byliśmy w stanie anulować notyfikacji. Proszę sprawdź czy wprowadzileś prawidłowy adres URL do przeglądarki.'

);
