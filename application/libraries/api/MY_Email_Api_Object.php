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
        
    }

    /**
     * List first 20 email messages
     *
     * @return array
     */
    public function list_all_email_msgs()
    {
    }

    
    /**
     * Delete existing SMS message
     *
     * @return Array
     */
    public function delete_email_msg()
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
            $post->add_rules('message_id.*','required','numeric');

            if ($post->validate())
            {
                $email_id = $post->twitter_id;
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
        
        return $this->response($ret_value);
    }

}

