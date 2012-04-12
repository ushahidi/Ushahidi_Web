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

class Alert_Model extends ORM {
	
	/**
	 * Many-to-one relationship definition
	 * @var array
	 */
	protected $belongs_to = array('user');
	
	/**
	 * One-to-many relationship definition
	 * @var array
	 */
	protected $has_many = array('incident' => 'alert_sent', 'category' => 'alert_category');
    
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'alert';

	/**
	 * Ignored columns - alert_mobile & alert_email will be replaced with alert_recipient
	 * These are columns not contained in the Model itself
	 * @var array
	 */
	protected $ignored_columns = array('alert_mobile', 'alert_email'); 	

	/**
	 * Method that provides the functionality of the magic method, __set, without the overhead
	 * of having to instantiate a Reflection class to realize it, and provides for object chaining
	 * 
	 * @param string $column
	 * @param mixed $value
	 * @return Alert_Model
	 */
	public function set($column, $value)
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
	 * @return Alert_Model
	 */
	public function assign(array $params = array())
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
	 * @return bool TRUE when validation succeeds, FALSE otherwise
	 */
	public function validate(array & $post, $save = FALSE)
	{
		// Initialise the validation library and setup some rules
		$post = Validation::factory($post)
			->pre_filter('trim')
			->add_rules('alert_mobile', 'numeric', 'length[6,20]')
			->add_rules('alert_email', 'email', 'length[3,64]')
			->add_rules('alert_lat', 'required', 'between[-90,90]')
			->add_rules('alert_lon', 'required', 'between[-180,180]')
			->add_rules('alert_radius','required','in_array[1,5,10,20,50,100]');
				
		// TODO Callbacks to check for duplicate alert subscription - same
		// subscriber for the same lat/lon
		//$post->add_callbacks('alert_mobile', array($this, '_mobile_check'));
		//$post->add_callbacks('alert_email', array($this, '_email_check'));

		// Check if a recipient mobile phone no. or email address has been
		// specified	
		if (empty($post->alert_mobile) AND empty($post->alert_email))
		{
			$post->add_rules('alert_recipient', 'required');
		}


		return parent::validate($post, $save);
		
	} // END function validate


    /**
     * Callback tests if a mobile number exists in the database for this alert
	 * @param   mixed mobile number to check
	 * @return  boolean
     */
    public function _mobile_check(Validation $post)
    {
		// If add->rules validation found any errors, get me out of here!
        if (array_key_exists('alert_mobile', $post->errors()) 
            OR array_key_exists('alert_lat', $post->errors()) 
            OR array_key_exists('alert_lon', $post->errors()))
            return;

        if ($post->alert_mobile AND (bool) $this->db
			->where(array(
				'alert_type' => 1,
				'alert_recipient' => $post->alert_mobile,
				'alert_lat' => $post->alert_lat,
				'alert_lon' => $post->alert_lon
				))
			->count_records($this->table_name) )
		{
			$post->add_error( 'alert_mobile', 'mobile_check');
		}
    } // END function _mobile_check

	
	/**
	 * Callback tests if an email accounts exists in the database for this alert
	 * @param   mixed mobile number to check
	 * @return  boolean
	 */
	public function _email_check(Validation $post)
	{
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('alert_email', $post->errors()) 
			OR array_key_exists('alert_lat', $post->errors()) 
			OR array_key_exists('alert_lon', $post->errors()))
			return;

		if ( $post->alert_email AND (bool) $this->db
			->where(array(
					'alert_type' => 2,
					'alert_recipient' => $post->alert_email,
					'alert_lat' => $post->alert_lat,
					'alert_lon' => $post->alert_lon
				))
			->count_records($this->table_name) )
		{
			$post->add_error('alert_email', 'email_check');
		}
	} // END function _email_check

	
	/**
	 * Checks if the alert subscription in @param $alert_code exists
	 *
	 * @param string $alert_code
	 * @return bool TRUE if the alert code exists, FALSE otherwise
	 */
	public static function alert_code_exists($alert_code)
	{
		return (ORM::factory('alert')
					->where('alert_code', $alert_code)
					->count_all() > 0
				);
	}
	
	/**
	 * Removes the alert code in @param $alert_code from the list of alerts
	 *
	 * @param string $alert_code
	 * @return bool TRUE if succeeds, FALSE otherwise
	 */
	public static function unsubscribe($alert_code)
	{
		// Fetch the alerts with the specified code
		$alerts = ORM::factory('alert')
			->where('alert_code', $alert_code)
			->find();
		
		// Check if the alert exists
		if ($alerts->loaded)
		{
			// Delete all categories linked to the alert
			ORM::factory('alert_category')->where('alert_id', $alerts->id)->delete_all();
			
			// Delete the alert
			$alerts->delete();

			// Success!
			return TRUE;
		}
		else
		{
			// Alert code not found. FAIL
			return FALSE;
		}
	}

} // END class Alert_Model
