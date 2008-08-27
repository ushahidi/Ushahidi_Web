<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE textarea input library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Form_Textarea_Core extends Form_Input {

	protected $data = array
	(
		'class' => 'textarea',
		'value' => '',
	);

	protected $protect = array('type');

	protected function html_element()
	{
		$data = $this->data;

		unset($data['label']);

		return form::textarea($data);
	}

} // End Form Textarea