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
class Sms_Api_Object extends Api_Object_Core {

    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }

    /**
     * Handles the API request
     */
    public function perform_task()
    {
        // List the SMS messages by default
        $this->_list_sms_msgs();
    }
    
    /**
     * Handles API action requests on an SMS
     */
    public function sms_action()
    {
        if ( ! $this->api_service->verify_array_index($this->request, 'action'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'action')
            ));
            
            return;
        }
        else
        {
            $this->by  = $this->request['action'];
        }
        
        switch ($this->by)
        {
            case "d":
                $this->_delete_sms_msg();
            break;
            
            case "s":
                $this->_spam_sms_msg();
            break;

            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(002)
                ));
        }
    }
    
    /**
     * List first 20 sms messages
     *
     */
    private function _list_sms_msgs()
    {
        $items = ORM::factory('message')
            ->where('service_id', '1')
            ->where('message_type','1')
            ->orderby('message_date','desc')
            ->find_all($this->list_limit);

        $json_categories = array();
        
        // Set the no. of records fetched
        $this->record_count = $items->count();
        
        $i = 0;

        //No record found.
        if ($items->count() == 0)
        {
            return $this->response(4);
        }

        foreach ($items as $sms)
        {
            if ( $this->response_type == 'json')
            {
                $json_categories[] = array("sms" => $item);        
            }
            else
            {
                $json_categories['sms'.$i] = array('sms' => $sms);
                $this->replar[] = 'sms'.$i;
            }
        }
        
        // Create the json array
        $data = array("payload" => array(
            "domain" => $this->domain,
            "count" => $json_categories),
            "error" => $this->api_service->get_error_msg(0));

        $this->response_data = ($this->response_type == 'json') 
            ? $this->array_as_xml($data)
            : $this->array_as_xml($data, $this->replar);
    }
   

    /**
     * Delete existing SMS message
     *
     */
    private function _delete_sms_msg()
    {
        $ret_value = 0; // Start off with successful execution
        
        if($_POST)
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
                $sms_id = $post->message_id;
                $sms = new Message_Model($sms_id);
                if ($sms->loaded == true)
                {
                    $sms->delete();
                }
                else
                {
                    //Comment id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "SMS ID does not exist.";
                    $ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "SMS ID is required.";
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
     * Spam / Unspam existing SMS message
     *
     * @return Array
     */
    public function _spam_sms_msg()
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
                $sms_id = $post->message_id;
                $sms = new Message_Model($sms_id);
                if ($sms->loaded == true)
                {
                    if ($sms->message_level == '1')
                    {
                        $sms->message_level = '99';
                    }
                    else
                    {
                        $sms->message_level = '1';
                    }

                    $sms->save();
                }
                else
                {
                    //twitter id doesn't exist in DB
                    //TODO i18nize the string
                    $this->error_messages .= "SMS ID does not exist.";
                    $this->ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "SMS ID is required.";
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

