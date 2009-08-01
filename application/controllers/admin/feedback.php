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
		if (!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
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
				if ($post->action == 'r')	// Read Action
				{
					foreach($post->feedback_id as $item)
					{
						$update = new Feedback_Model($item);
						
						if ($update->loaded == true) {
							$update->feedback_status = '0';
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
					foreach($post->feedback_id as $item)
					{
						$update = new Feedback_Model($item);

						if ($update->loaded == true) {
							$feedback_id = $update->id;
							$update->delete();
						}
						
						// Delete feedback_person
						ORM::factory('feedback_person')->where('feedback_id',$feedback_id)->delete_all();
					}
					
					$form_action = "DELETED";
				}
				
				$form_saved = TRUE;
			}
			else
			{
				$form_error = TRUE;
			}
			
		}
		$filter = "WHERE feedback.feedback_status=1";
		// Pagination
		$pagination = new Pagination(array(
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config(
				'settings.items_per_page_admin'),
			'total_items'    => ORM::factory('feedback')->count_all()
		));

		$all_feedback = ORM::factory('feedback')
			->select('feedback_person.person_ip,feedback.*')
			->join('feedback_person',array('feedback_person.feedback_id'=>'feedback.id'),
			$filter,'LEFT JOIN')
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
		
		// Javascript Header
		$this->template->js = new View('admin/feedback_js');
		
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
		
		// mark message as read when the title link is clicked.	
		$this->_mark_as_read( $id );
		
		//setup and initialize form fields
		$form = array
		(
			'feedback_message'=>'',
			'person_email' => ''
		);
		
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$message_sent = FALSE;
		
		//has form been submitted, if so setup validation
		if($_POST)
		{
			
			$post = Validation::factory($_POST);
			
			//Trim whitespaces
			$post->pre_filter('trim', TRUE);
			
			//Add validation rules
			$post->add_rules('feedback_message','required');
			$post->add_rules('person_email','email', 'length[3,100]');
		
			if( $post->validate() ) { 	
				$sent = $this->_email_reply( $post->person_email,
					$post->feedback_message,$post->feedback_title );
				
				if( $sent ) {
					$message_sent = TRUE;
				}
			}
			else
        	{
	
				// repopulate the form fields
            	$form = arr::overwrite($form, $post->as_array());

            	// populate the error fields, if any
            	$errors = arr::overwrite($errors, $post->errors('feedback'));
				$form_error = TRUE;
			}
		}
			
		$this->template->content->feedback = $feedback;
		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->message_sent = $message_sent;
		
	}
	
	/**
	 * Send reply message 
	 */
	public function _email_reply( $email, $message, $subject )
	{
		$to = $email;
		$from = 'no-reply@ushahidi.com';
		$subject = $subject;
		
		$message .= "\n\n";
		//email details
		if( email::send( $to, $from, $subject, $message, FALSE ) == 1 )
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	
	}
	
	/**
	 * Marks an unread message as read
	 * @param id - the message id 
	 */
	public function _mark_as_read( $id ) 
	{
		$update = new Feedback_Model($id);
		if ($update->loaded == true) {
			$update->feedback_status = '1';
			$update->save();
		}
	}
	
}
