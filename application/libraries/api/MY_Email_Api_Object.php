<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for KML via the API.
 *
 * @version 25 - Emmanuel Kala 2010-10-27
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
class Email_Api_Object extends Api_Object_Core {

    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }

    /**
     * Empty declaration for OOP compliance
     */
    public function perform_task()
    {
        $this->_list_all_email_msgs();
    }

    /**
     * Handles actions for email messages
     */
    public function email_action()
    {
        if ( ! $this->api_service->verify_array_index($this->request, 'action'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001,'action')
            )); 
            return;
        }
        else
        {
            $this->by = $this->request['action'];
        }
        
        switch ($this->by)
        {
            case "d":
                $this->_delete_email_msg();
            break;
            
            case "s":
                $this->_spam_email_msg();
            break;

            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(001)
                ));
        }

    }

    /**
     * List first 20 email messages
     *
     * @return array
     */
    public function _list_all_email_msgs()
    {
        $ret_json_or_xml = ''; // Will hold the return JSON/XML string
  
        $items = ORM::factory('message')
            ->where('service_messageid', '2')
            ->where('message_type','1')
            ->orderby('message_date','desc')
            ->find_all($this->list_limit);

        // Set the no. of records fetched
        $this->record_count = $items->count();
        
        $json_categories = array();
        
        $i = 0;

        //No record found.
        if ($items->count() == 0)
        {
            $this->response_data = $this->response(4);
            return;
        }

        foreach ($items as $email)
        {
            if ( $response_type == 'json')
            {
                $json_categories[] = array("email" => $item);        
            }
            else
            {
                $json_categories['email'.$i] = array('email' => 
                        $twitter);
                $this->replar[] = 'email'.$i;
            }
        }
        
        // Create the json array
        $data = array("payload" => array(
            "domain" => $this->domain,
            "count" => $json_categories),
            "error" => $this->api_service->get_error_msg(0));

        if ($this->response_type == 'json') 
        {
            $ret_json_or_xml = $this->array_as_json($data);
        }
        else
        {
            $ret_json_or_xml = $this->array_as_xml($data, $this->replar);
        }

        $this->response_data = $ret_json_or_xml;

    }
    
    /**
     * Delete existing email message
     *
     * @return Array
     */
    public function _delete_email_msg()
    {
        $ret_val = 0; // Initialize a 0 return value; successful execution
        
        if ($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('action','required', 'alpha', 'length[1,1]');
            $post->add_rules('message_id','required','numeric');

            if ($post->validate())
            {
                $email_id = $post->message_id;
                $email = new Message_Model($email_id);
                if ($email->loaded == true)
                {
                    $email->delete();
                }
                else
                {
                    //email id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "Email ID does not exist.";
                    $this->ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Email ID is required.";
                $ret_value = 1;
            }

        }
        else
        {
            $ret_value = 3;
        }
        
        $this->response_data = $this->response($ret_value);
    }

    /**
     * Spam / Unspam existing email message
     *
     * @return Array
     */
    public function _spam_email_msg()
    {
        $ret_val = 0; // Initialize a 0 return value; successful execution
        
        if($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('action','required', 'alpha', 'length[1,1]');
            $post->add_rules('message_id','required','numeric');

            if ($post->validate())
            {
                $email_id = $post->message_id;
                $email = new Message_Model($email_id);
                if ($email->loaded == true)
                {
                    if ($email->message_level == '1')
                    {
                        $email->message_level = '99';
                    }
                    else
                    {
                        $email->message_level = '1';
                    }

                    $email->save();
                }
                else
                {
                    //email id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "Email ID does not exist.";
                    $ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Email ID is required.";
                $ret_value = 1;
            }

        }
        else
        {
            $ret_value = 3;
        }
        
        $this->response_data = $this->response($ret_value);
    }

}

