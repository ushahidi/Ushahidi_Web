<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE submit input library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Form_Submit_Core extends Form_Input {

	protected $data = array
	(
		'type'  => 'submit',
		'class' => 'submit'
	);

	protected $protect = array('type');

	public function __construct($value)
	{
		$this->data['value'] = $value;
	}

	public function html()
	{
		$data = $this->data;
		unset($data['label']);

		return form::button($data);
	}

	public function validate()
	{
		// Submit buttons do not need to be validated
		return $this->is_valid = TRUE;
	}

} // End Form Submit