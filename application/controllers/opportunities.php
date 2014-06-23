<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Opportunities controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Opportunities_Controller extends Main_Controller
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function index($page = 1)
	{
		$this->template->header->this_page = $this->themes->this_page = 'opportunities';
		$this->template->content = new View('opportunities/main');
	}
	
	public function fetch_searchfor() 
	{
	
		// Initialize custom field array
		$form['form_id'] = 1;
		$form_id = $form['form_id'];
		$form['custom_field'] = customforms::get_custom_form_fields($id,$form_id,true);
	
		// Get Info for 'In Search of' Table
		$resource_needed = array();
		foreach (ORM::factory('custom_field')->orderby('custom_field')->find_all() as $resource_needed)
		{
			// Create a list of Resources Needed
			$this_resource_needed = $resource_needed->resource_needed;
			if (strlen($resource_needed) > 35)
			{
				$this_resource_needed = substr($this_resource_needed, 0, 35) . "...";
			}
			$resources_needed[$resource_needed->id] = $this_resource_needed;
		}
	}
		$this->template->content->resource_needed = $resource_needed;

	public function resources_available() 
	{
		// Initialize resources available array
		$form['resource_id'] = 1;
		$form_id = $form['form_id'];
		$form['custom_field'] = customforms::get_custom_form_fields($id,$form_id,true);

		// Get info for 'Resources Available' Table
		$resource_needed = array();
		foreach (ORM::factory('custom_field')->orderby('custom_field')->find_all() as $resource_needed)
		{
			// Create a list of Resources Needed
			$this_resource_needed = $resource_needed->resource_needed;
			if (strlen($resource_needed) > 35)
			{
				$this_resource_needed = substr($this_resource_needed, 0, 35) . "...";
			}
			$resources_needed[$resource_needed->id] = $this_resource_needed;
		}
	}
		$this->template->content->resource_needed = $resource_needed;

	public function add_resource()
	{
		$db = new Database();

		// Setup and initialize form field names
		$form = array (
			'PCV_name' => '',
			'resource_available' => '',
			'available_from' => '',
			'available_until' => '',
			'contact' => '',
			'add_info' => '',
		);

		// Copy the form as errors, so the errors will be stored with keys
		// corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = ($saved == 'saved');
	}
}
?>