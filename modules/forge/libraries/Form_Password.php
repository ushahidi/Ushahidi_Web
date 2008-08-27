<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE password input library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Form_Password_Core extends Form_Input {

	protected $data = array
	(
		'type'  => 'password',
		'class' => 'password',
		'value' => '',
	);

	protected $protect = array('type');

} // End Form Password