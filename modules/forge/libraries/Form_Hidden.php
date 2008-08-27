<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE hidden input library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Form_Hidden_Core extends Form_Input {

	protected $data = array
	(
		'value' => '',
	);

	public function html()
	{
		return form::hidden($this->data['name'], $this->data['value']);
	}

} // End Form Hidden