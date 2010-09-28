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

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->response_type = '';
        $this->query = '';
        $this->db = $this->api_actions->_get_db();
    }

        
    /**
 	 * Gets a list of comments
     * 
     * @param int item_id - the comment id
     * @param string incident_id, the incident id
     * @param string spammed 
     * @param string approved
     * 
     * @return array
 	 */

    public function _get_comment_list($item_id, $incident_id, 
            $spammed, $approved) 
    {
        $xml = new XMLWriter();
        $json = array();
        $json_item = array();

        $comments = new Database();

        $comments->select('*')->from('comment');

        if($item_id > 0) 
        {
            if($incident_id > 0) 
            {
                $comments->where(array('id' => $item_id, 
                    'incident_id' => $incident_id,
                    'comment_spam' => $spammed, 
                    'comment_active' => $approved));
            }
            else 
            {
                $comments->where(array('id' => $item_id, 
                        'comment_spam' => $spammed,
                        'comment_active' => $approved));
            }
        }
        else 
        {
            if($incident_id > 0) 
            {
                $comments->where(array('incident_id' => $incident_id,
                            'comment_spam' => $spammed,
                            'comment_active' => $approved));
            }
            else 
            {
                $comments->where(array('comment_spam' => $spammed,
                            'comment_active' => $approved));
            }
        }

        $comments = $comments->get();

        if($response_type == "xml") 
        {
            $xml->openMemory();
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('response');
            $xml->startElement('payload');
            $xml->startElement('comments');
        }

        foreach($comments as $list_item) 
        {
            if($this->response_type == "json") 
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
                $xml->writeElement('comment_date_gmt',
                        $list_item->comment_date_gmt);
                    
                $xml->endElement(); // comment
            }
        }

        if($this->response_type == "xml") 
        {
            $xml->endElement(); // comments
            $xml->endElement(); // payload
            $xml->endElement(); // response

            return $xml->outputMemory(true);
        }
        else 
        {
            $json = array("payload" => array("comments" => $json_item));

            return $this->_arrayAsJSON($json);
        }
    }


    /**
 	 * Spams / Unspams a comment
     * 
     * @param int item_id - the comment to spammed / unspammed
     * @param string spammed - the comment to be seen as spammed
     *
     * @return array
     */
    public function _spam_comment($item_id, $spammed,$response_type) 
    {
        $comment = ORM::factory('comment')->where('id', $item_id);

        if($comment->count_all() < 1) 
        {
            // Does not exist
            if($this->responseType == "json") 
            {
                return $this->_arrayAsJSON($this->_operationError(
                            "itemid does not exist"));
            }
            else 
            {
                    return $this->_arrayAsXML($this->
                            _operationError("itemid does not exist"));
            }
        }

        $comment = ORM::factory("comment", $item_id);

        $comment->comment_spam = $spammed;

        $comment->save();

        if($this->responseType == "json") 
        {
            return $this->_arrayAsJSON(
                    $this->_operationSuccess("operation successful"));
        }
        else 
        {
                return $this->_arrayAsXML(
                        $this->_operationSuccess("operation successful"));
        }
    }

    /**
 	 * Deletes a comment
 	 */

    public function _delete_comment($item_id,$response_type) 
    {
        $comment = ORM::factory('comment')->where('id', $item_id);

        if($comment->count_all() < 1) 
        {
            // Does not exist
            if($this->responseType == "json") 
            {
                return $this->_arrayAsJSON(
                        $this->_operationError("itemid does not exist"));
            }
            else 
            {
                return $this->_arrayAsXML(
                        $this->_operationError("itemid does not exist"));
            }
        }

        $comment = ORM::factory("comment", $item_id);

        $comment->delete();

        if($this->responseType == "json") 
        {
            return $this->_arrayAsJSON(
                    $this->_operationSuccess("operation successful"));
        }
        else 
        {
            return $this->_arrayAsXML(
                    $this->_operationSuccess("operation successful"));
        }
    }
    
    /**
 	 * Approves / Dissaproves a comment
 	 */

    public function _approve_comment($item_id, $approved,$response_type) 
    {
        $comment = ORM::factory('comment')->where('id', $item_id);

        if($comment->count_all() < 1) 
        {
            // Does not exist
            if($response_type == "json") 
            {
                return $this->_arrayAsJSON(
                        $this->_operationError("itemid does not exist"));
            }
            else 
            {
                return $this->_arrayAsXML(
                    $this->_operationError(
                        "itemid does not exist"));
            }
        }

        $comment = ORM::factory("comment", $item_id);

        $comment->comment_active = $approved;

        $comment->save();

        if($this->response_type == "json") 
        {
            return $this->_arrayAsJSON(
                $this->_operationSuccess("operation successful"));
        }
        else 
        {
            return $this->_arrayAsXML(
                $this->_operationSuccess("operation successful"));
        }
    }

}
