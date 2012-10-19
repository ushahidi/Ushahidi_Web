<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Layers
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

class Layer_Model extends ORM
{
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'layer';
	
	/** 
	 * Validates and optionally saves a new layer record from an array
	 * 
	 * @param array $array Values to check
	 * @param bool $save Saves the record when validation succeeds
	 * @return bool
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Set up validation
		$array = Validation::factory($array)
				->pre_filter('trim')
				->add_rules('layer_name','required', 'length[3,80]')
				->add_rules('layer_color','required', 'length[6,6]');
		
		// Add callbacks for the layer url and layer file
		$array->add_callbacks('layer_url', array($this, 'layer_url_file_check'));
		$array->add_callbacks('layer_file', array($this, 'layer_url_file_check'));
		
		// Pass validation to parent and return
		return parent::validate($array, $save);
	}
	
	/**
	 * Performs validation checks on the layer url and layer file - Checks that at least
	 * one of them has been specified using the applicable validation rules
	 *
	 * @param Validation $array Validation object containing the field names to be checked
	 */
	public function layer_url_file_check(Validation $array)
	{
		// Ensure at least a layer URL or layer file has been specified
		if (empty($array->layer_url) AND empty($array->layer_file) AND empty($array->layer_file_old))
		{
			$array->add_error('layer_url', 'atleast');
		}
		
		// Add validation rule for the layer URL if specified
		if ( ! empty($array->layer_url) AND (empty($array->layer_file) OR empty($array->layer_file_old)))
		{
			if (! valid::url($array->layer_url))
			{
				$array->add_error('layer_url', 'url');
			}
		}
		
		// Check if both the layer URL and the layer file have been specified
		if ( ! empty($array->layer_url) AND ( ! empty($array->layer_file_old) OR ! empty($array->layer_file)))
		{
			$array->add_error('layer_url', 'both');
		}
	}
	
	/**
	 * Checks if the specified layer id is a valid integer and exists in the database
	 *
	 * @param int $layer_id 
	 * @return bool
	 */
	public static function is_valid_layer($layer_id)
	{
		return (intval($layer_id) > 0)
				? self::factory('layer', intval($layer_id))->loaded
				: FALSE;
	}
}
