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

class Twitter_Api_Object extends Api_Object_Core {

    public function __construct($api_service)
    {
        parent::__construct($api_service);  
    }  

    /**
     * List all twitter messages by default
     */
    public function perform_task()
    {
        $this->_list_twitter_msgs();
    }
    
    /**
     * Handles actions for twitter messages
     */
    public function twitter_action()
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
                $this->_delete_twitter_msg();
            break;
            
            case "s":
                $this->_spam_twitter_msg();
            break;

            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(001)
                ));
        }
    }
    
    /**
     * List first 15 twitter messages.
     *
     * @return array
     */
    private function _list_twitter_msgs()
    {
        $ret_json_or_xml = ''; // Will hold the return JSON/XML string
  
        $items = ORM::factory('message')
            ->where('service_id', '3')
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
            $this-response_data = $this->response(4);
            return;
        }

        foreach ($items as $twitter)
        {
            if ( $response_type == 'json')
            {
                $json_categories[] = array("twitter" => $item);        
            }
            else
            {
                $json_categories['twitter'.$i] = array('twitter' => 
                        $twitter);
                $this->replar[] = 'twitter'.$i;
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
     * Delete existing Twitter message
     *
     * @return Array
     */
    private function _delete_twitter_msg()
    {
        $ret_value = 0;
        
        if ($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('action','required', 'alpha', 'length[1,1]');
            $post->add_rules('message_id.*','required','numeric');

            if ($post->validate())
            {
                $twitter_id = $post->message_id;
                $sms = new Message_Model($twitter_id);
                if ($sms->loaded == true)
                {
                    $sms->delete();
                }
                else
                {
                    //Comment id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "Twitter ID does not exist.";
                    $ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Twitter ID is required.";
                $ret_value = 1;
            }

        }
        else
        {
            $ret_value = 3;
        }
        
        $this->response_data = $this->response($ret_value, 
            $this->error_messages);

    }

    /**
     * Spam / Unspam existing email message
     *
     * @return Array
     */
    public function _spam_twitter_msg()
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
                $twitter_id = $post->message_id;
                $twitter = new Message_Model($twitter_id);
                if ($twitter->loaded == true)
                {
                    if ($twitter->message_level == '1')
                    {
                        $twitter->message_level = '99';
                    }
                    else
                    {
                        $twitter->message_level = '1';
                    }

                    $twitter->save();
                }
                else
                {
                    //twitter id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "Twitter ID does not exist.";
                    $this->ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Twitter ID is required.";
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

