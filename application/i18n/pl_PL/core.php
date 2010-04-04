<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Errors associated with the core of the system
 */
$lang = array
(
	'there_can_be_only_one' => 'Może być tylko jeden przypadek Ushahidi na żądanie strony',
	'uncaught_exception'    => 'Niewychwycone %s: %s w pliku %s na linii %s',
	'invalid_method'        => 'Nieprawidłowa metoda %s zastosowana w %s',
	'invalid_property'      => 'Właściwość %s nie istnieje w klasie %s.',
	'log_dir_unwritable'    => 'Ten katalog logowania nie jest zapisywalny: %s',
	'resource_not_found'    => 'Żądane %s, %s, nie mogą być znalezione',
	'invalid_filetype'      => 'Żądany typ pliku, .%s, nie jest dozwolony w twoim pliku konfiguracji  widoku',
	'view_set_filename'     => 'Musisz określić nazwę pliku widoku przed obrazowaniem',
	'no_default_route'      => 'Określ domyślną ścieżkę w config/routes.php',
	'no_controller'         => 'Ushahidi nie był w stanie określić kontrolera do przetworzenia tego żądania: %s',
	'page_not_found'        => 'Strona, której zażądałeś, %s, nie została znaleziona.',
	'stats_footer'          => 'Przesłane w {czas wykonania} sekund, przy użyciu {używana pamięć} pamięci. Wygenerowane przez Ushahidi v%s.',
	'report_bug'			=> '<a href="%s">prześlij tę kwestię do Ushahidi</a>',
	'error_file_line'       => '<tt>%s <strong>[%s]:</strong></tt>',
	'stack_trace'           => 'Stack Trace',
	'generic_error'         => 'Żądanie nie może być zrealizowane',
	'errors_disabled'       => 'Możesz przejść do <a href="%s">home page</a> or <a href="%s">try again</a>.',

	// Drivers
	'driver_implements'     => 'Sterownik %s sterownik dla biblioteki %s musi wykorzystywać interface %s',
	'driver_not_found'      => 'Sterownik %s dla biblioteki %s nie został znaleziony',

	// Resource names
	'config'                => 'plik konfiguracyjny',
	'controller'            => 'kontroler',
	'helper'                => 'pomocnik',
	'library'               => 'biblioteka',
	'driver'                => 'sterownik',
	'model'                 => 'model',
	'view'                  => 'widok',
);
