<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Errors associated with the core of the system
 */
$lang = array
(
	'there_can_be_only_one' => 'Sólo se permite una instancia de Ushahidi en cada pedido',
	'uncaught_exception'    => 'Excepción %s: %s en el archivo %s en la línea %s',
	'invalid_method'        => 'Método inválido: %s llamado en: %s',
	'invalid_property'      => 'La propiedad %s no existe en la clase %s.',
	'log_dir_unwritable'    => 'El directorio de bitácoras (logs) no tiene permiso de escritura: %s',
	'resource_not_found'    => 'El %s solicitado, %s, no se puede encontrar',
	'invalid_filetype'      => 'El tipo de archivo solicitado, .%s, no esta permitido por el archivo de configuración de vistas',
	'view_set_filename'     => 'Debe seleccionar el archivo de vistas antes de llamar al render',
	'no_default_route'      => 'Por favor seleccione una ruta predeterminada en config/routes.php',
	'no_controller'         => 'Ushahidi no pudo encontrar un controlador para ejecutar este pedido: %s',
	'page_not_found'        => 'La página que ha solicitado, %s, no pudo ser encontrada.',
	'stats_footer'          => 'Cargado en {execution_time} segundos, utilizando {memory_usage} de memoria. Generado por Ushahidi v%s.',
	'report_bug'			=> '<a href="%s" id="show_bugs">Reporte esto a Ushahidi</a>',
	'error_file_line'       => '<tt>%s <strong>[%s]:</strong></tt>',
	'stack_trace'           => 'Stack Trace',
	'generic_error'         => 'No es posible completar este pedido',
	'errors_disabled'       => 'Puede ir a <a href="%s">la página de inicio</a> o <a href="%s">intentar nuevamente</a>.',

	// Drivers
	'driver_implements'     => 'El driver %s para la biblioteca %s debe implementar la interface %s',
	'driver_not_found'      => 'El driver %s para la biblioteca %s no puede ser encontrado',

	// Resource names
	'config'                => 'archivo de configuración',
	'controller'            => 'controlador (controller)',
	'helper'                => 'ayudante (helper)',
	'library'               => 'biblioteca (library)',
	'driver'                => 'controlador (driver)',
	'model'                 => 'modelo (model)',
	'view'                  => 'vista (view)',
);
