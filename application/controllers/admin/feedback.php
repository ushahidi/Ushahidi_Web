<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to manage users
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Users Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Feedback_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'feedback';
		
		// If this is not a super-user account, redirect to dashboard
		if (!$this->auth->logged_in('admin'))
        {
             url::redirect('admin/dashboard');
		}
	}
	
	function index( $page=1 )
	{	
		$this->template->content = new View('admin/feedback');
		$this->template->content->title = 'Feedback';
		
		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
	    if ($_POST)
	    {
			$post = Validation::factory($_POST);
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('feedback_id.*','required','numeric');
			
			if ($post->validate())
	        {
				if ($post->action == 'a')	// Read Action
				{
					foreach($post->feedback_id as $item)
					{
						$update = new Feedback_Model($item);
						if ($update->loaded == true) {
							$update->feedback_status = '1';
							$update->save();
						}
					}
					$form_action = "READ";
				}
				
				elseif ($post->action == 'u') 	// Unread Action
				{
					foreach($post->feedback_id as $item)
					{
						$update = new Feedback_Model($item);
						if ($update->loaded == true) {
							$update->feedback_status = '1';
							$update->save();
						}
					}
					//TODO write unread action code
					$form_action = "UNREAD";
				}
				
				elseif ($post->action == 'd')	// Delete Action
				{
					//TODO write delete action code
					$form_action = "DELETED";
				}
				
				$form_saved = TRUE;
			}
			else
			{
				$form_error = TRUE;
			}
			
		}
		$filter = "";
		// Pagination
		$pagination = new Pagination(array(
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config(
				'settings.items_per_page_admin'),
			'total_items'    => ORM::factory('feedback')->count_all()
		));

		$all_feedback = ORM::factory('feedback')
			->select('feedback_person.person_full_name, feedback_person.person_ip,feedback.*')
			->join('feedback_person',array('feedback_person.feedback_id'=>'feedback.id'),
			'','LEFT JOIN')
			->orderby('feedback_dateadd', 'desc')
			->find_all((int) Kohana::config('settings.items_per_page_admin'), 
			$pagination->sql_offset);
		
		$this->template->content->all_feedback = $all_feedback;
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Total feedback
		$this->template->content->total_items = $pagination->total_items;
		
	}
	
	/**
	 * View feedback
	 * @param id - feedback id
	 */
	function view( $id=false ) 
	{
		$this->template->content = new View('admin/feedback_view');
		$this->template->content->title = Kohana::lang('feedback.feedback_page_title');
		
		$feedback = ORM::factory('feedback')
			->select('feedback_person.*,feedback.*')
			->join('feedback_person',array('feedback_person.feedback_id'=>'feedback.id'),
			"WHERE feedback.id=$id.",'LEFT JOIN')
			->orderby('feedback_dateadd', 'desc')->find();
			
		$this->template->content->feedback = $feedback;
		
	}
	
}
