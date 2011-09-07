<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Sharing Controller
 * Add/Edit Ushahidi Instance Shares
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

class Sharing_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'settings';
		
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "settings"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}
	
	
	function index()
	{
		$this->template->content = new View('admin/sharing');
		$this->template->content->title = Kohana::lang('ui_admin.settings');
		
		// What to display
		if (isset($_GET['status']) && !empty($_GET['status']))
		{
			$status = $_GET['status'];
			
			if (strtolower($status) == 's')
			{
				$filter = 'sharing_type = 2';
			}
			elseif (strtolower($status) == 'r')
			{
				$filter = 'sharing_type = 1';
			}
			else
			{
				$status = "0";
				$filter = '1=1';
			}
		}
		else
		{
			$status = "0";
			$filter = "1=1";
		}	
		
		// setup and initialize form field names
		$form = array
	    (
			'action' => '',
			'sharing_id' => '',
			'sharing_url' => '',
			'sharing_email' => '',
			'sharing_color' => '',
			'sharing_limits' => '',
			'sharing_type' => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$sharing_id = "";
		
		
		if( $_POST ) 
		{
			// Add Site Variables that need to be validated before submission
			$site_vars = array(
				"sharing_email" => Kohana::config('settings.site_email'),
				);
			$post = Validation::factory(array_merge($_POST,$site_vars));
			
			 //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('sharing_url','required', 'url');
				$post->add_rules('sharing_email','required', 'email');
				$post->add_rules('sharing_color','required', 'length[6,6]');
				$post->add_rules('sharing_limits','required', 'between[1,4]');
				$post->add_rules('sharing_type', 'between[1,2]');
				$post->add_callbacks('sharing_url', array($this,'url_exists_chk'));
			}
			
			if( $post->validate() )
			{
				$sharing_id = $post->sharing_id;
				
				$sharing = new Sharing_Model($sharing_id);
				if ( $post->action == 'd' )
				{ // Delete Action
					$sharing->delete( $sharing_id );
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				else if($post->action == 'v')
				{ // Active/Inactive Action
					if ($sharing->loaded)
					{
						if ($sharing->sharing_active == 1)
						{
							$sharing->sharing_active = 0;
						}
						else
						{ // Make Share Active
							$sharing->sharing_active = 1;
						}
						$sharing->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				else
				{ // Save Action
					$sharing_save = TRUE;
					
					// If this is a new share request, we'll connect to remote instance		
					if (!$sharing->loaded)
					{ // Generate 30 Character Sharing Key
						$sharing_key = text::random('alnum',30);
						
						// Verify that the instance we're connecting to is indeed
						// an Ushahidi Instance.
						$sharing_connect = new Sharing(); // Use sharing library to connect
						if ( !($sharing_connect->share_notify($post->sharing_url, $sharing_key, 'notify')) )
						{
							$sharing_save = FALSE;
							$post->add_error('sharing_url', 'valid');
						}
						
						$sharing->sharing_key = $sharing_key;
						$sharing->sharing_url = $this->_clean_urls($post->sharing_url);
					}
					
					// Save Actions dependent on Share Type
					if ($sharing->loaded && $sharing->sharing_type == 2)
					{
						$sharing->sharing_type = 2;	// Pushing Data
						$sharing->sharing_limits = $post->sharing_limits;
					}
					else
					{
						$sharing->sharing_type = 1;	// Pulling Data
						$sharing->sharing_color = $post->sharing_color;
						$sharing->sharing_limits = $post->sharing_limits;
					}
					
					if ($sharing_save)
					{
						$sharing->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.created_edited'));
					}
					else
					{
						// repopulate the form fields
			            $form = arr::overwrite($form, $post->as_array());

		               // populate the error fields, if any
		                $errors = arr::overwrite($errors, 
							$post->errors('sharing'));
		                $form_error = TRUE;
					}
					
				}
				
			} else {
				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());
	
               // populate the error fields, if any
                $errors = arr::overwrite($errors, 
					$post->errors('sharing'));
				print_r($errors);
                $form_error = TRUE;
			}
		}
		
		
		// Pagination
        $pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items' => ORM::factory('sharing')->where($filter)->count_all()
			));
		
        $shares = ORM::factory('sharing')
			->where($filter)
			->orderby('sharing_site_name', 'asc')
			->find_all($this->items_per_page, $pagination->sql_offset);
		
		$this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->shares = $shares;
		$this->template->content->errors = $errors;
		
		// Status Tab
		$this->template->content->status = $status;
		
		// Site Contact Info
		$this->template->content->site_email = Kohana::config('settings.site_email');
		
		// Sharing Limits Array
		$this->template->content->sharing_limits_array = array(
				"1" => Kohana::lang('ui_admin.hourly'),
				"2" => Kohana::lang('ui_admin.every_six_hours'),
				"3" => Kohana::lang('ui_admin.every_twelve_hours'),
				"4" => Kohana::lang('ui_admin.daily')
			);
		
        // Javascript Header
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/sharing_js');
	}
	
	
	/**
	 * Checks if url already exists.
     * @param Validation $post $_POST variable with validation rules 
	 */
	public function url_exists_chk(Validation $post)
	{
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('sharing_url', $post->errors()))
			return;
		
		$sharing_type = ($post->sharing_type &&
			($post->sharing_type == 1 || $post->sharing_type == 2)
			 ) ? $post->sharing_type : 1;
		
		$share_exists = ORM::factory('sharing')
			->where('sharing_url', $this->_clean_urls($post->sharing_url))
			->find();
		
		if ($share_exists->loaded &&
			$share_exists->id != $post->sharing_id &&
			$share_exists->sharing_type == $sharing_type
			)
		{
			$post->add_error( 'sharing_url', 'exists');
		}
	}
		
	
	/**
    * Clean Urls
	* We want to standardize urls to prevent duplication
    */
	private function _clean_urls($url)
	{
		// Remove http, https, www
		$remove_array = array('http://www.', 'http://', 'https://', 'https://www.', 'www.');
		
		$url = str_replace($remove_array, "", $url);
		
		// Remove trailing slash/s
		$url = implode("/", array_filter(explode("/", $url)));
		$url = preg_replace('{/$}', '', $url);
		
		return $url;
	}
}
