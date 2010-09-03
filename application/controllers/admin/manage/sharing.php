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
 * @module     Admin Forms Controller  
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
		if ( ! admin::permissions($this->user, "manage"))
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
			'sharing_name' => '',
			'sharing_url' => '',
			'sharing_color' => '',
			'sharing_active' => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$sharing_id = "";
		
		
		if( $_POST ) 
		{
			$post = Validation::factory($_POST);
			
			 //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('sharing_name','required', 'length[3,150]');
				$post->add_rules('sharing_url','required', 'url', 'length[3,255]');
				$post->add_rules('sharing_color','required', 'length[6,6]');
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
					$sharing->sharing_name = $post->sharing_name;
					$sharing->sharing_url = $this->_clean_urls($post->sharing_url);
					$sharing->sharing_color = $post->sharing_color;
					$sharing->save();
					
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.created_edited'));
				}
				
			}
			else
			{
				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());
	
               // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('sharing'));
                $form_error = TRUE;
			}
		}
		
		
		// Pagination
        $pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('sharing')->where($filter)->count_all()
			));
		
        $shares = ORM::factory('sharing')
			->where($filter)
			->orderby('sharing_name', 'asc')
			->find_all((int) Kohana::config('settings.items_per_page_admin'), 
				$pagination->sql_offset);
		
		$this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->shares = $shares;
		$this->template->content->errors = $errors;
		
		// Status Tab
		$this->template->content->status = $status;
		
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
		
		$share_exists = ORM::factory('sharing')
			->where('sharing_url', $this->_clean_urls($post->sharing_url))
			->find();
		
		if ($share_exists->loaded)
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