<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Reporters Controller
 * Add/Edit Ushahidi Reporters
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Reporters_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'messages';
		
		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("messages_reporters"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}
	
	public function index($service_id = 1)
	{
		$this->template->content = new View('admin/reporters/main');
		$this->template->content->title = Kohana::lang('ui_admin.reporters');
		
		// setup and initialize form field names
		$form = array
		(
			'reporter_id' => '',
			'level_id' => '',
			'service_name' => '',
			'service_account' => '',
			'location_id' => '',
			'location_name' => '',
			'latitude' => '',
			'longitude' => ''
		);
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			    //  Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('reporter_id.*','required','numeric');
			
			if ($post->action == 'l')
			{
				$post->add_rules('level_id','required','numeric');
			}
			elseif ($post->action == 'a')
			{
				$post->add_rules('level_id','required','numeric');
				// If any location data is provided, require all location parameters
				if ($post->latitude OR $post->longitude OR $post->location_name)
				{
					$post->add_rules('latitude','required','between[-90,90]');		// Validate for maximum and minimum latitude values
					$post->add_rules('longitude','required','between[-180,180]');	// Validate for maximum and minimum longitude values
					$post->add_rules('location_name','required', 'length[3,200]');
				}
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{	
				if( $post->action == 'd' )				// Delete Action
				{
					foreach($post->reporter_id as $item)
					{
						// Delete Reporters Messages
						ORM::factory('message')
							->where('reporter_id', $item)
							->delete_all();
					
						// Delete Reporter
						$reporter = ORM::factory('reporter')->find($item);
						$reporter->delete( $item );
					}
					
					$form_saved = TRUE;
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				elseif( $post->action == 'l' )			// Modify Level Action
				{
					foreach($post->reporter_id as $item)
					{
						// Update Reporter Level
						$reporter = ORM::factory('reporter')->find($item);
						if ($reporter->loaded)
						{
							$reporter->level_id = $post->level_id;
							$reporter->save();
						}
					}
					
					$form_saved = TRUE;
					$form_action = utf8::strtoupper(Kohana::lang('ui_admin.modified'));
				}
				else if( $post->action == 'a' ) 		// Save Action
				{
					foreach($post->reporter_id as $item)
					{
						$reporter = ORM::factory('reporter')->find($item);
					
						// SAVE Reporter only if loaded
						if ($reporter->loaded)
						{
							$reporter->level_id = $post->level_id;

							// SAVE Location if available
							if ($post->latitude AND $post->longitude)
							{
								$location = new Location_Model($post->location_id);
								$location->location_name = $post->location_name;
								$location->latitude = $post->latitude;
								$location->longitude = $post->longitude;
								$location->location_date = date("Y-m-d H:i:s",time());
								$location->save();
							
								$reporter->location_id = $location->id;
							}
						
							$reporter->save();

							$form_saved = TRUE;
							$form_action = utf8::strtoupper(Kohana::lang('ui_admin.modified'));
						}
					}
				}
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());
				
				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('reporters'));
				$form_error = TRUE;
			}
		}

		// Start building query
		$filter = '1=1 ';
		
		// Default search type to service id
		$search_type = ( isset($_GET['s']) ) ? intval($_GET['s']) : intval($service_id);
		if ($search_type > 0)
		{
			$filter .= 'AND service_id = '.intval($search_type).' ';
		}
		
		// Get Search Keywords (If Any)
		$keyword = '';
		if (isset($_GET['k']) AND !empty($_GET['k']))
		{
			$keyword = $_GET['k'];
			$filter .= 'AND service_account LIKE \'%'.Database::instance()->escape_str($_GET['k']).'%\' ';
		}

		// Pagination
		$pagination = new Pagination(array(
		                    'query_string' => 'page',
		                    'items_per_page' => $this->items_per_page,
		                    'total_items' => ORM::factory('reporter')
								->where($filter)
								->count_all()
		                ));

		$reporters = ORM::factory('reporter')
						->where($filter)
		                ->orderby('service_account', 'asc')
		                ->find_all($this->items_per_page,  $pagination->sql_offset);

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->reporters = $reporters;
		$this->template->content->service_id = $service_id;
		$this->template->content->search_type = $search_type;
		$search_type_array = Service_Model::get_array();
		$search_type_array[0] = "All";
		asort($search_type_array);
		$this->template->content->search_type_array = $search_type_array;
		$this->template->content->keyword = $keyword;
		
		$levels = ORM::factory('level')->orderby('level_weight')->find_all();
		$this->template->content->levels = $levels;

		// Level and Service Arrays
		$this->template->content->level_array = Level_Model::get_array();
		$this->template->content->service_array = Service_Model::get_array();
		
		// Javascript Header
        $this->template->map_enabled = TRUE;
        $this->template->js = new View('admin/reporters/reporters_js');
		$this->template->js->default_map = Kohana::config('settings.default_map');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->js->latitude = Kohana::config('settings.default_lat');
		$this->template->js->longitude = Kohana::config('settings.default_lon');
		$this->template->js->form_error = $form_error;
	}
}
