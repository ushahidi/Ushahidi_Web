<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Comments_Api_Object
 * 
 * This class handles commenting activities via the API.
 *
 * @version 25 - Emmanuel Kala 2010-10-26
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Comments_Api_Object extends Api_Object_Core {

    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }
    
    public function perform_task()
    {
        // by request
        if($this->api_service->verify_array_index($this->request, 'by') )
        {
            $this->by = $this->request['by'];

            switch ($this->by)
            {
                case "all":
                    $this->response_data = $this->_get_all_comments();
                break;
            
                case "spam":
                    $this->response_data = $this->_get_spam_comments();
                break;
            
                case "pending":
                    $this->response_data = $this->_get_pending_comments();
                break;
            
                case "approved":
                    $this->response_data = $this->_get_approved_comments();
                break;

			    case "reportid":
				    if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                    {
                        $this->set_error_message(array(
                            "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                        return;
                    }
                    else
                    {
                        $this->response_data = $this->_get_comment_by_report_id($this->check_id_value($this->request['id']));
                    }
            	break;
            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(002)
                ));
            }

            return;
        }
        
        //action request
        else if($this->api_service->verify_array_index(
            $this->request, 'action'))
        {
            $this->comment_action();
            return;
        }
        else
        {

            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 
                'by or action')
            ));
            return;

        }

    }
    

    /**
     * Handles comment action API requests
     */    
    public function comment_action()
    {
        $action = ''; //Will hold comment action

        if ( ! $this->api_service->verify_array_index($this->request,
                'action'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'action')
            ));
            return;
        }
        else
        {
            $action = $this->request['action'];
        }
        
        switch ($action)
        {
            case "approve": //Aprrove / Unapprove comment
                 $this->response_data = $this->_approve_comment();
            break;
            
            case "add": // Add a new comment
               $this->response_data = $this->_add_comment();
            break;
            
            case "delete": // Delete an existing comment
                $this->response_data = $this->_delete_comment();
            break;
            
            case "spam": // Spam or Unspam a comment
                $this->response_data = $this->_spam_comment();
            break;
            
            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(002)
                ));
        }
    }
        
    /**
     * Gets a list of comments by
     * 
     * @param string status - List comments by status.
     * @param string response_type - XML or JSON
     * 
     * @return array
     */
    private function _get_comment_list($where, $limit = '') 
    {
       
        $xml = new XMLWriter();
        $json = array();
        $json_item = array();

        $this->query = "SELECT * FROM comment $where $limit";
        
        $items = $this->db->query($this->query);

        // Set the no. of records returned
        $this->record_count = $items->count();
        
        if ($this->response_type == "xml") 
        {
            $xml->openMemory();
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('response');
            $xml->startElement('payload');
            $xml->writeElement('domain',$this->domain);
            $xml->startElement('comments');
        }
        
        //No record found.
        if ($items->count() == 0)
        {
            return $this->response(4);
        }
        
        foreach ($items as $list_item) 
        {
            if ($this->response_type == "json") 
            {
                $json_item = (array) $list_item;
            }
            else 
            {
                $xml->startElement('comment');

                $xml->writeElement('id',$list_item->id);
                $xml->writeElement('incident_id',$list_item->incident_id);
                $xml->writeElement('user_id',$list_item->user_id);
                $xml->writeElement('comment_author',
                        $list_item->comment_author);
                $xml->writeElement('comment_email',
                        $list_item->comment_email);
                $xml->writeElement('comment_description',
                        $list_item->comment_description);
                $xml->writeElement('comment_ip',$list_item->comment_ip);
                $xml->writeElement('comment_rating',
                        $list_item->comment_spam);
                $xml->writeElement('comment_active',
                        $list_item->comment_active);
                $xml->writeElement('comment_date',$list_item->comment_date);
                    
                $xml->endElement(); // comment
            }
        }

        if ($this->response_type == "xml") 
        {
            $xml->endElement(); // comments
            $xml->endElement(); // payload
            $xml->startElement('error');
            $xml->writeElement('code',0);
            $xml->writeElement('message','No Error');
            $xml->endElement();//end error
            $xml->endElement(); // end response

            return $xml->outputMemory(true);
        }
        else 
        {
            $json = array("payload" => array("comments" => $json_item));

            return $this->array_as_json($json);
        }
    }

    /**
     * List all comments marked as spam
     *
     * @return array
     */
    private function _get_spam_comments()
    {
        $where = "\nWHERE comment_spam = 1";
        $where .= "\nORDER BY comment_date DESC";
        $limit = "\nLIMIT 0, $this->list_limit";

        return $this->_get_comment_list($where, $limit); 
    }

    /**
     * List all comments submited to Ushahidi
     * 
     * @param string response_type - The format of the response needed. 
     * XML or JSON
     *
     * @return array
     */
    private function _get_all_comments()
    {
        $where = "\nWHERE comment_spam = 0";
        $where .= "\nORDER BY comment_date DESC";
        $limit = "\nLIMIT 0, $this->list_limit";

        return $this->_get_comment_list($where, $limit); 
    }
    
    /**
     * List all approved comments
     *
     * @param string response_type - The format of the response needed.
     * XML or JSON
     *
     * @return array
     */
    private function _get_approved_comments()
    {
        $where = "\nWHERE comment_active = 1 AND comment_spam = 0";
        $where .= "\nORDER BY comment_date DESC";
        $limit = "\nLIMIT 0, $this->list_limit";

        return $this->_get_comment_list($where, $limit); 

    }
            
    /**
     * List all pending comments
     * 
     * @param string response_type - The format of the response needed.
     * XML or JSON
     *
     * @return array
     */
    private function _get_pending_comments()
    {
        $where = "\nWHERE comment_active = 0 AND comment_spam = 0";
        $where .= "\nORDER BY comment_date DESC";
        $limit = "\nLIMIT 0, $this->list_limit";

        return $this->_get_comment_list($where, $limit); 
    }

    /**
     * Spams / Unspams a comment
     * 
     * @param string response_type - The response to return. XML or JSON
     *
     * @return array
     */
    private function _spam_comment() 
    {
        $form = array(
            'comment_id' => ''
        );

        $ret_value = 0;
        
        if ($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('comment_id','required','numeric');

            if ($post->validate())
            {
                $comment_id = $post->comment_id;
                $comment = new Comment_Model($comment_id);
                
                if ($comment->loaded == true)
                {
                    //spam
                    if ( $post->action == strtolower('s'))
                    {
                        $comment->comment_active = '0';
                        $comment->comment_spam = '1';
                    } 
                    //unspam
                    elseif ($post->action == strtolower('n')) 
                    {
                        $comment->comment_active = '1';
                        $comment->comment_spam = '0';
                    }

                    $comment->save();
                }
                else
                {
                    //Comment id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "Comment ID does not exist.";
                    $ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Comment ID is required.";
                $ret_value = 1;
            }

        }
        else
        {
            $ret_value = 3;
        }
        
        return $this->response($ret_value, $this->error_messages);

    }

    /**
     * Deletes a comment
     *
     * @param string response_type - The type of response to return XML or 
     * JSON.
     *
     * @return Array
     */
    private function _delete_comment() 
    {
        $form = array (
            'comment_id' => ''
        );

        $errors = $form;
		$form_error = FALSE;

        $ret_value = 0;
        
        if ($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('comment_id','required','numeric');

            if ($post->validate())
            {
                $comment_id = $post->comment_id;
                $comment = new Comment_Model($comment_id);
                if ($comment->loaded == true)
                {
                    $comment->delete();
                }
                else
                {
                    //Comment id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "Comment ID does not exist.";
                    $this->ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Comment ID is required.";
                $ret_value = 1;
            }

        }
        else
        {
            $ret_value = 3;
        }
        
        return $this->response($ret_value, $this->error_messages);
    }
    
    /**
     * Approves / Dissaproves a comment
     *
     * @param string response_type - The resposne format to return.XML 
     * or JSON
     *
     * @return Array
     */
    private function _approve_comment() 
    {
        $form = array
        (
            'comment_id' => '',
        );

        $errors = $form;
		$form_error = FALSE;
        
        $ret_value = 0;
        
        if($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('comment_id','required','numeric');
            

            if ($post->validate())
            {
                $comment_id = $post->comment_id;
                $comment = new Comment_Model($comment_id);
                if ($comment->loaded == true)
                {
                    //approve
                    if($post->action == strtolower('a'))
                    {
                        $comment->comment_active = '1';
                        $comment->comment_spam = '0';
                    }
                    else if($post->action == strtolower('u'))
                    {
                        $comment->comment_active = '0';
                    }

                    $comment->save();
                }
                else
                {
                    //Comment id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "Comment ID does not exist.";
                    $ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Comment ID is required.";
                $ret_value = 1;
            }

        }
        else
        {
            $ret_value = 3;
        }
        
        return $this->response($ret_value, $this->error_messages);
    }

    /**
     * Submit comments
     *
     * @return int
     */
    private function _add_comment()
    {
        $api_akismet = Kohana::config('settings.api_akismet');

		// Comment Post?
		// Setup and initialize form field names
        
		$form = array
		(
            'incident_id' => '',
			'comment_author' => '',
			'comment_description' => '',
			'comment_email' => '',
		);

		$captcha = Captcha::factory();
		$errors = $form;
		$form_error = FALSE;
        $ret_value = 0;

		// Check, has the form been submitted, if so, setup validation

		if ($_POST AND Kohana::config('settings.allow_comments') )
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things

			$post = Validation::factory($_POST);

			// Add some filters

			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
            $post->add_rules('incident_id', 'required');
			$post->add_rules('comment_author', 'required', 'length[3,100]');
			$post->add_rules('comment_description', 'required');
			$post->add_rules('comment_email', 'required','email', 'length[4,100]');
			// Test to see if things passed the rule checks

			if ($post->validate())
			{
				// Yes! everything is valid
                $incident = ORM::factory('incident')
				->where('id',$post->incident_id)
				->where('incident_active',1)
				->find();
			    if ( $incident->id == 0 )	// Not Found
			    {
				    return $this->response(1, "No incidents with that ID");
			    }


				if ($api_akismet != "")
				{
					// Run Akismet Spam Checker

					$akismet = new Akismet();

					// Comment data

					$comment = array(
						'author' => $post->comment_author,
						'email' => $post->comment_email,
						'website' => "",
						'body' => $post->comment_description,
						'user_ip' => $_SERVER['REMOTE_ADDR']
					);

					$config = array(
						'blog_url' => url::site(),
						'api_key' => $api_akismet,
						'comment' => $comment
					);

					$akismet->init($config);

					if ($akismet->errors_exist())
					{
						if ($akismet->is_error('AKISMET_INVALID_KEY'))
						{
							// throw new Kohana_Exception('akismet.api_key');

						}
                        elseif ($akismet->is_error('AKISMET_RESPONSE_FAILED'))
                        {

							// throw new Kohana_Exception('akismet.server_failed');

						}
                        elseif ($akismet->is_error('AKISMET_SERVER_NOT_FOUND'))
                        {

							// throw new Kohana_Exception('akismet.server_not_found');

						}

						// If the server is down, we have to post
						// the comment :(
						// $this->_post_comment($comment);

						$comment_spam = 0;
					}
					else
					{
						$comment_span = ($akismet->is_spam())? 1 : 0;
					}
				}
				else
				{
					// No API Key!!
					$comment_spam = 0;
				}


				$comment = new Comment_Model();
				$comment->incident_id = strip_tags($post->incident_id);
				$comment->comment_author = strip_tags($post->comment_author);
				$comment->comment_description = strip_tags($post->comment_description);
				$comment->comment_email = strip_tags($post->comment_email);
				$comment->comment_ip = $_SERVER['REMOTE_ADDR'];
				$comment->comment_date = date("Y-m-d H:i:s",time());

				// Activate comment for now
				if ($comment_spam == 1)
				{
					$comment->comment_spam = 1;
					$comment->comment_active = 0;
				}
				else
				{
					$comment->comment_spam = 0;
					// 1 - Auto-approve, 0 - Manually approve
					$comment->comment_active = (Kohana::config('settings.allow_comments') == 1)? 1 : 0;
				}
				$comment->save();

				// Notify Admin Of New Comment
				$send = notifications::notify_admins(
					"[".Kohana::config('settings.site_name')."] ".
						Kohana::lang('notifications.admin_new_comment.subject'),
						Kohana::lang('notifications.admin_new_comment.message')
						."\n\n'".strtoupper($incident->incident_title)."'"
						."\n".url::base().'reports/view/'.$post->incident_id
					);
			}
            else
            {

				// No! We have validation errors, we need to show the form again, with the errors

				// Repopulate the form fields

				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any

				$errors = arr::overwrite($errors, $post->errors('comments'));
			    
                foreach ($errors as $error_item => $error_description)
                {
                    if ( ! is_array($error_description))
                    {
                        $this->error_messages .= $error_description;
                        
                        if ($error_description != end($errors))
                        {
                            $this->error_messages .= " - ";
                        }
                    }
                }
                 
                $ret_value = 1; // Validation error
			}
		}
        else
        {   
            $ret_value = 3;
        }

        return $this->response($ret_value, $this->error_messages);
    }

	/**
	 * 
	 * Get comments by report id
	 * 
	 * @param int id - The report id
	 * 
	 * @return String XML or JSON string
	 */
	private function _get_comment_by_report_id($id) 
	{
		$json_comments = array();
        $ret_json_or_xml = '';
		$i = 0;
		
		//Check if comments are enabled by that deployments
		if (Kohana::config('settings.allow_comments'))
		{
			
			$incident_comments = array();
			if ($id)
			{
				$this->query = "SELECT id, incident_id, comment_author, comment_email, ";
				$this->query .= "comment_description,comment_rating,comment_date ";
				$this->query .= "FROM ".$this->table_prefix."`comment`" ;
				$this->query .= " WHERE `incident_id` = ".$this->db->escape_str($id)." AND `comment_active` = '1' ";
				$this->query .= "AND `comment_spam` = '0' ORDER BY `comment_date` ASC";
				$incident_comments = $this->db->query($this->query);
												
				if ($incident_comments->count() == 0)
					return $this->response(4);
				
				foreach ($incident_comments as $comment)
				{
					// Needs different treatment depending on the output
		            if ($this->response_type == 'json')
		            {
		                $json_comments[] = array("comment" => $comment);
		            } 
		            else
		            {
		                $json_comments['comment'.$i] = array("comment" => $comment);
		                $this->replar[] = 'comment'.$i;
		            }

		            $i++;
				}
				// Create the json array
		        $data = array(
		                "payload" => array(
		                    "domain" => $this->domain,
		                    "comments" => $json_comments
		                ),
		                "error" => $this->api_service->get_error_msg(0)
		        );

				$ret_json_or_xml = ($this->response_type == 'json')
					? $this->array_as_json($data)
					: $this->array_as_xml($data, $this->replar);

		        return $ret_json_or_xml;
			}
			else 
			{
				//prompt user for a valid ID
				return $this->response(1, "No report with that ID");
			}
		
		}
		else 
		{
			//prompt user about commenting not enabled on this deployment
			return $this->response(1, "Commenting is not activated on this deployment");
		}
	}

}
