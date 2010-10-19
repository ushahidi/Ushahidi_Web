<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for KML via the API.
 *
 * @version 24 - Henry Addo 2010-09-27
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

require_once('ApiActions.php');

class AdminComment
{
    private $data;
    private $items;
    private $table_prefix;
    private $api_actions;
    private $response_type;
    private $list_limit;
    private $domain;
    private $api_prvt_func;
    private $error_messages;
    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->api_prvt_func = new ApiPrivateFunc;
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->response_type = '';
        $this->query = '';
        $this->error_messages = '';
        $this->ret_value = 0;
        $this->db = $this->api_actions->_get_db();
        $this->list_limit = $this->api_actions->_get_list_limit();
        $this->domain = $this->api_actions->_get_domain();
    }

        
    /**
 	 * Gets a list of comments by
     * 
     * @param string status - List comments by status.
     * @param string response_type - XML or JSON
     * 
     * @return array
 	 */
    private function _get_comment_list($where, $limit = '',
            $response_type) 
    {
       
        $xml = new XMLWriter();
        $json = array();
        $json_item = array();

        $this->query = "SELECT * FROM comment $where $limit";
        
        $this->items = $this->db->query($this->query);
        
        if($response_type == "xml") 
        {
            $xml->openMemory();
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('response');
            $xml->startElement('payload');
            $xml->writeElement('domain',$this->domain);
            $xml->startElement('comments');
        }
        
        foreach($this->items as $list_item) 
        {
            if($response_type == "json") 
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

        if($response_type == "xml") 
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

            return $this->api_actions->_array_as_JSON($json);
        }
    }

    /**
     * List all comments marked as spam
     *
     * @return array
     */
    public function _get_spam_comments($response_type)
    {
        $where = "\nWHERE comment_spam = 1";
        $where .= "\nORDER BY comment_date DESC";
        $limit = "\nLIMIT 0, $this->list_limit";

        return $this->_get_comment_list($where, $limit,$response_type); 
    }

    /**
     * List all comments submited to Ushahidi
     * 
     * @param string response_type - The format of the response needed. 
     * XML or JSON
     *
     * @return array
     */
    public function _get_all_comments($response_type)
    {
        $where = "\nWHERE comment_spam = 0";
        $where .= "\nORDER BY comment_date DESC";
        $limit = "\nLIMIT 0, $this->list_limit";

        return $this->_get_comment_list($where, $limit,$response_type); 
    }
    
    /**
     * List all approved comments
     *
     * @param string response_type - The format of the response needed.
     * XML or JSON
     *
     * @return array
     */
    public function _get_approved_comments($response_type)
    {
        $where = "\nWHERE comment_active = 1 AND comment_spam = 0";
        $where .= "\nORDER BY comment_date DESC";
        $limit = "\nLIMIT 0, $this->list_limit";

        return $this->_get_comment_list($where, $limit,$response_type); 

    }
            
    /**
     * List all pending comments
     * 
     * @param string response_type - The format of the response needed.
     * XML or JSON
     *
     * @return array
     */
    public function _get_pending_comments($response_type)
    {
        $where = "\nWHERE comment_active = 0 AND comment_spam = 0";
        $where .= "\nORDER BY comment_date DESC";
        $limit = "\nLIMIT 0, $this->list_limit";

        return $this->_get_comment_list($where, $limit,$response_type); 
    }

    /**
 	 * Spams / Unspams a comment
     * 
     * @param string response_type - The response to return. XML or JSON
     *
     * @return array
     */
    public function _spam_comment($response_type) 
    {
        
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
                    $this->ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Comment ID is required.";
                $this->ret_value = 1;
            }

        }
        else
        {
            $this->ret_value = 3;
        }
        
        return $this->api_actions->_response($this->ret_value,
                $response_type,$this->error_messages);

    }

    /**
 	 * Deletes a comment
     *
     * @param string response_type - The type of response to return XML or 
     * JSON.
     *
     * @return Array
 	 */
    public function _delete_comment($response_type) 
    {
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
                $this->ret_value = 1;
            }

        }
        else
        {
            $this->ret_value = 3;
        }
        
        return $this->api_actions->_response($this->ret_value,
                $response_type,$this->error_messages);
    }
    
    /**
 	 * Approves / Dissaproves a comment
     *
     * @param string response_type - The resposne format to return.XML 
     * or JSON
     *
     * @return Array
 	 */
    public function _approve_comment($response_type) 
    {
        $form = array
        (
            'action' => '',
            'comment_id' => '',
        );

        $errors = $form;
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
                    $this->ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Comment ID is required.";
                $this->ret_value = 1;
            }

        }
        else
        {
            $this->ret_value = 3;
        }
        
        return $this->api_actions->_response($this->ret_value,
                $response_type,$this->error_messages);
    }

}
