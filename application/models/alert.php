<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Alerts
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Alert_Model extends ORM
{
	protected $belongs_to = array('user');
	protected $has_many = array('incident' => 'alert_sent', 'category' => 'alert_category');
    
	// Database table name
	protected $table_name = 'alert';

	// Ignored columns - alert_mobile & alert_email will be replaced with alert_recipient
	// These are columns not contained in the Model itself
	protected $ignored_columns = array('alert_mobile', 'alert_email'); 	

	/**
	 * Method that provides the functionality of the magic method, __set, without the overhead
	 * of having to instantiate a Reflection class to realize it, and provides for object chaining
	 * 
	 * @param string $column
	 * @param mixed $value
	 * @return Alert_Model $this for a fluent interface
	 */
	public function set ($column, $value)
	{
		// CALL the magic method __set, with the parameters provided
		$this->__set($column, $value);

		// RETURN $this for a fluent interface
		return $this;

	} // END function set
    
    
	/**
	 * Method that allows for the use of the set method, en masse
	 * 
	 * @param array $params
	 * @return Alert_Model $this for a fluent interface
	 */
	public function assign (array $params = array())
	{
		// ITERATE through all of the column/value pairs provided ...
		foreach ($params as $column => $value)
		{
			// CALL the set method with the column/value pair
			$this->set($column, $value);
		}
        
		// RETURN $this for a fluent interface
		return $this;

	} // END function assign


	/**
	 * Model Validation
	 * 
	 * @param array $array values to check
	 * @param boolean $save save[Optional] the record when validation succeeds
	 * @return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Initialise the validation library and setup some rules
		$array = Validation::factory($array)
			->pre_filter('trim')
			->add_rules('alert_mobile', 'numeric', 'length[6,20]')
			->add_rules('alert_email', 'email', 'length[3,64]')
			->add_rules('alert_lat', 'required', 'between[-90,90]')
			->add_rules('alert_lon', 'required', 'between[-180,180]')
			->add_rules('alert_radius', 'required', 'in_array[1,5,10,20,50,100]')
			->add_callbacks('alert_mobile', array($this, '_mobile_or_email'))
			->add_callbacks('alert_mobile', array($this, '_mobile_check'))
			->add_callbacks('alert_email', array($this, '_email_check'));

		return parent::validate($array, $save);
	} // END function validate


    /**
     * Callback tests if a mobile number exists in the database for this alert
	 * @param   mixed mobile number to check
	 * @return  boolean
     */
    public function _mobile_check(Validation $array)
    {
		// If add->rules validation found any errors, get me out of here!
        if (array_key_exists('alert_mobile', $array->errors()) 
            || array_key_exists('alert_lat', $array->errors()) 
            || array_key_exists('alert_lon', $array->errors()))
            return;

        if ($array->alert_mobile && (bool) $this->db
			->where(array(
				'alert_type' => 1,
				'alert_recipient' => $array->alert_mobile,
				'alert_lat' => $array->alert_lat,
				'alert_lon' => $array->alert_lon
				))
			->count_records($this->table_name) )
		{
			$array->add_error( 'alert_mobile', 'mobile_check');
		}
    } // END function _mobile_check

	
	/**
	 * Callback tests if an email accounts exists in the database for this alert
	 * @param   mixed mobile number to check
	 * @return  boolean
	 */
	public function _email_check(Validation $array)
	{
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('alert_email', $array->errors()) 
			OR array_key_exists('alert_lat', $array->errors()) 
			OR array_key_exists('alert_lon', $array->errors()))
			return;

		if ( $array->alert_email && (bool) $this->db
			->where(array(
					'alert_type' => 2,
					'alert_recipient' => $array->alert_email,
					'alert_lat' => $array->alert_lat,
					'alert_lon' => $array->alert_lon
				))
			->count_records($this->table_name) )
		{
			$array->add_error( 'alert_email', 'email_check');
		}
	} // END function _email_check


	/**
	 * Tests if an email accounts exists in the database for this alert
	 * @param   mixed mobile number to check
	 * @return  boolean
	 */
	public function _mobile_or_email(Validation $array)
	{
		if ( empty($array->alert_mobile) && empty($array->alert_email) )
			$array->add_error( 'alert_mobile', 'one_required');
	} // END function _mobile_or_email
	
	/**
	 * Checks if the alert subscription in @param $alert_code exists
	 *
	 * @param string $alert_code
	 */
	public static function alert_code_exists($alert_code)
	{
		return (ORM::factory('alert')
					->where('alert_code', $alert_code)
					->count_all() > 0
				) ? TRUE : FALSE;
	}
	
	/**
	 * Removes the alert code in @param $alert_code from the list of alerts
	 *
	 * @param string $alert_code
	 */
	public static function unsubscribe($alert_code)
	{
		// Fetch all alerts with the specified code
		$alerts = ORM::factory('alert')
			->where('alert_code', $alert_code)
			->find_all();
			
		foreach ($alerts as $alert)
		{
			// Delete all alert categories with the specified code
			ORM::factory('alert_category')
				->where('alert_id', $alert->id)
				->delete_all();

			$alert->delete();
		}
		
	}

} // END class Alert_Model
