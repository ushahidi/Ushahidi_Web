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

class AdminTwitter
{
    private $data;
    private $items;
    private $table_prefix;
    private $api_actions;
    private $response_type;
    private $domain;
    private $api_prvt_func;
    private $ret_value;
    private $list_limit;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->api_prvt_func = new ApiPrivateFunc;
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->response_type = '';
        $this->ret_value = 0;
        $this->domain = $this->api_actions->_get_domain();
        $this->list_limit = $this->api_actions->_get_list_limit();

    }

    /**
     * List first 15 twitter messages.
     *
     * @param string response_type - The response to type to return.
     *
     * @return array
     */
    public function _list_all_twitter_msgs($response_type)
    {
        $this->items = ORM::factory('message')
			->where('service_id', '3')
			->where('message_type','1')
			->orderby('message_date','desc')
			->find_all($this->list_limit);

        $json_categories = array();
        
        $i = 0;
        foreach ( $this->items as $twitter)
        {
            if ( $response_type == 'json')
            {
                $json_categories[] = array("twitter" => $item);        
            }
            else
            {
                $json_categories['twitter'.$i] = array('twitter' => 
                        $twitter);
                $this->replay[] = 'twitter'.$i;
            }
        }
        
        //create the json array
		$this->data = array("payload" => array(
            "domain" => $this->domain,
            "count" => $json_categories),
            "error" => $this->api_actions->_get_error_msg(0));

		if($response_type == 'json') 
        {
			$this->ret_json_or_xml = $this->api_actions
                ->_array_as_JSON($this->data);
		}
        else
        {
			$this->ret_json_or_xml = $this->api_actions
                ->_array_as_XML($this->data,$this->replar);
		}

		return $this->ret_json_or_xml;

    }

    /**
     * Delete existing SMS message
     *
     * @param string response_type.
     *
     * @return Array
     */
    public function _del_twitter_msg($response_type)
    {
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
                $twitter_id = $post->twitter_id;
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
                    $this->ret_value = 1;

                }
            }
            else
            {
                //TODO i18nize the string
                $this->error_messages .= "Twitter ID is required.";
                $this->ret_value = 1;
            }

        }
        else
        {
            $this->ret_value = 3;
        }
        
        return $this->api_actions->_response($this->ret_value,
                $response_type);

    }

}

