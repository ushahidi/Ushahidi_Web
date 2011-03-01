<?php defined('SYSPATH') OR die('No direct access allowed.');

if (class_exists('PHPUnit_Util_Filter'))
{
	restore_exception_handler();
	restore_error_handler();

	Event::clear('system.ready');
	Event::clear('system.routing');
	Event::clear('system.execute');
}
