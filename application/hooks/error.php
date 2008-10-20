<?php defined('SYSPATH') or die('No direct script access.');

/**
* Custom Error Pages
*/

Event::clear('system.404', array('Kohana', 'show_404'));

Event::add('system.404', 'error_404');

function error_404() {
	$controller = new Error_Controller();
	$controller->error_404();
	die();
}
