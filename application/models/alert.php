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
 * @module     Alert Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Alert_Model 
extends ORM
{
    protected $has_many = array('incident' => 'alert_sent');
    
    // Database table name
    protected $table_name = 'alert';
    
    
    /**
     * Method that provides the functionality of the magic method, __set, without the overhead
     * of having to instantiate a Reflection class to realize it, and provides for object chaining
     * 
     * @param string $column
     * @param mixed $value
     * @return Alert_Model $this for a fluent interface
     */
    public function set ($column, $value)
    {   // CALL the magic method __set, with the parameters provided
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
    {   // ITERATE through all of the column/value pairs provided ...
        foreach ($params as $column => $value)
        {   // CALL the set method with the column/value pair
            $this->set($column, $value);
        }
        
        // RETURN $this for a fluent interface
        return $this;
        
    } // END function assign

} // END class Alert_Model
