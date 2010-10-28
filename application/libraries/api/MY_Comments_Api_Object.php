<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for KML via the API.
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
        $this->_get_comments();
    }
    
    /**
     * Handles comment listing API requests
     */
    public function get_comments()
    {
        if ( ! $this->api_service->verify_array_index($this->request, 'by'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'by')
            ));
            return;
        }
        else
        {
            $this->by = $this->request['by'];
        }
        
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
            
            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(002)
                ));
        }
        
    }


    /**
     * Handles comment action API requests
     */    
    public function comment_action()
    {
        if ( ! $this->verify_array_index($this->request, 'action'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'action')
            ));
            return;
        }
        else
        {
            $this->by = $this->request['action'];
        }
        
        switch ($this->by)
        {
            case "a":
                $this->_approve_comment();
            break;
            
            case "u":
                //$this->_upapprove_commment();
            break;
            
            case "d":
                $this->_delete_comment();
            break;
            
            case "s":
                $this->_spam_comment();
            break;
            
            case "n":               
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
        
        if ($this->response_type == "xml") 
        {
            $xml->openMemory();
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('response');
            $xml->startElement('payload');
            $xml->writeElement('domain',$this->domain);
            $xml->startElement('comments');
        }
        
        foreach ($this->items as $list_item) 
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

            return $this->array_as_xml($json);
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
        $ret_val = 0;
        
        if ($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('action','required', 'alpha', 'length[1,1]');
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
        $ret_value = 0;
        
        if ($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('action','required', 'alpha', 'length[1,1]');
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
            'action' => '',
            'comment_id' => '',
        );

        $errors = $form;
        
        $ret_val = 0;
        
        if($_POST)
        {
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
            // Add some rules, the input field, followed by a list of 
            //checks, carried out in order
            $post->add_rules('action','required', 'alpha', 'length[1,1]');
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

}
