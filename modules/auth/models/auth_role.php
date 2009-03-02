<?php defined('SYSPATH') OR die('No direct access allowed.');

class Auth_Role_Model extends ORM {

	protected $has_and_belongs_to_many = array('users');

	/**
	 * Validates and optionally saves a role record from an array.
	 *
	 * @param  array    values to check
	 * @param  boolean  save the record when validation succeeds
	 * @return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validation::factory($array)
			->pre_filter('trim')
			->add_rules('name', 'required', 'length[4,32]')
			->add_rules('description', 'length[0,255]');

		return parent::validate($array, $save);
	}

	/**
	 * Allows finding roles by name.
	 */
	public function unique_key($id)
	{
		if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
		{
			return 'name';
		}

		return parent::unique_key($id);
	}

} // End Auth Role Model