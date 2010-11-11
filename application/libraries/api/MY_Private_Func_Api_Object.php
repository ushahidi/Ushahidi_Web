<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles private functions that not accessbile by the public 
 * via the API.
 *
 * @version 24 - Emmanuel Kala 2010-10-25
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

class Private_Func_Api_Object extends Api_Object_Core {

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
     * FrontlineSMS Key Validation
     *
     * @param string app_key FrontlineSMS Key
     * @return bool, false if authentication fails
     */
    public function _chk_key($app_key = 0)
    {
        // Is this a valid FrontlineSMS Key?
        $keycheck = ORM::factory('settings', 1)
            ->where('frontlinesms_key', $app_key)
            ->find();

        if ($keycheck->loaded)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Log user in.
     *
     * @param string $username User's username.
     * @param string $password User's password.
     * @return int user_id, false if authentication fails
     */
    public function _login($username, $password)
    {
        $auth = Auth::instance();

        // Is user previously authenticated?
        if ($auth->logged_in())
        {
            return $auth->get_user()->id;
        }
        else
        {
            // Attempt a login
            if ($auth->login($username, $password))
            {
                return $auth->get_user()->id;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Provide statistics for the deployment
     * 
     */
    public function statistics()
    {

        $messages_total = 0;
        $messages_services = array();
        $services = ORM::factory('service')->find_all();
        
        foreach ($services as $service) 
        {
            $message_count = ORM::factory('message')
                ->join('reporter','message.reporter_id','reporter.id')
                ->where('service_id', $service->id)
                ->where('message_type', '1')
                ->count_all();
            
            $service_name = $service->service_name;
            
            $messages_stats[$service_name] = $message_count;
            
            $messages_total += $message_count;
        }

        $messages_stats['total'] = $messages_total;

        $incidents_total = ORM::factory('incident')->count_all();
        $incidents_unapproved = ORM::factory('incident')->
            where('incident_active', '0')->count_all();
            
        $incidents_approved = $incidents_total - $incidents_unapproved;
        $incomingmedia_total = ORM::factory('feed_item')->count_all();
        $categories_total = ORM::factory('category')->count_all();
        $locations_total = ORM::factory('location')->count_all();

        $data = array(
                'incidents' => array(
                'total' => $incidents_total,
                'approved' => $incidents_approved,
                'unapproved' => $incidents_unapproved
            ),

            'incoming_media' => array(
                'total_feed_items' => $incomingmedia_total
            ),

            'categories' => array(
                'total' => $categories_total
            ),

            'locations' => array(
                'total' => $locations_total
            ),

            'messages' => $messages_stats,


        );

        $this->response_data = ($this->response_type == 'json')
            ? $this->array_as_json($data)
            : $this->array_as_xml($data);

    }

    /**
     * Receive SMS's via FrontlineSMS or via Mobile Phone Native Apps
     *
     * @return string
     */
    public function sms()
    {
        $reponse = array();

        // Validate User
        // Should either be authenticated or have app_key
        $username = isset($this->request['username']) ? $this->request['username'] : "";
        $password = isset($this->request['password']) ? $this->request['password'] : "";

        $app_key = isset($this->request['key']) ? $this->request['key'] : "";

        if ( $user_id = $this->_login($username, $password) ||
            $this->_chk_key($app_key) )
        {
            // Process POST
            // setup and initialize form field names
            $form = array
            (
                'message_from' => '',
                'message_description' => '',
                'message_date' => ''
            );

            /**
             * Instantiate Validation, 
             * use $post, so we don't overwrite $_POST fields with our 
             * own things
             */
            $post = Validation::factory($request);

            //  Add some filters
            $post->pre_filter('trim', TRUE);

            /**
             * Add some rules, the input field, followed by a list of 
             * checks, carried out in order.
             */
            $post->add_rules('message_from', 'required', 'numeric',
                    'length[6,20]');
            $post->add_rules('message_description', 'required', 
                    'length[3,300]');
            $post->add_rules('message_date', 'date_mmddyyyy');

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Validates so Save Message
                $services = new Service_Model();
                $service = $services->where('service_name','SMS')->find();
                if (!$service)
                    return;

                $reporter = ORM::factory('reporter')
                    ->where('service_id', $service->id)
                    ->where('service_account', $post->message_from)
                    ->find();

                if (!$reporter->loaded == TRUE)
                {
                    // get default reporter level (Untrusted)
                    $level = ORM::factory('level')
                        ->where('level_weight', 0)
                        ->find();

                    $reporter->service_id = $service->id;
                    $reporter->level_id = $level->id;
                    $reporter->service_userid = null;
                    $reporter->service_account = $post->message_from;
                    $reporter->reporter_first = null;
                    $reporter->reporter_last = null;
                    $reporter->reporter_email = null;
                    $reporter->reporter_phone = null;
                    $reporter->reporter_ip = null;
                    $reporter->reporter_date = date('Y-m-d');
                    $reporter->save();
                }

                // Save Message
                $message = new Message_Model();
                $message->parent_id = 0;
                $message->incident_id = 0;
                $message->user_id = ( $user_id ) ? $user_id : 0;
                $message->reporter_id = $reporter->id;
                $message->message_from = $post->message_from;
                $message->message_to = null;
                $message->message = $post->message_description;
                $message->message_type = 1; // Inbox
                
                $message->message_date = (isset($post->message_date)
                    AND !empty($post->message_date))
                    ? $post->message_date 
                    : date("Y-m-d H:i:s",time());
                    
                $message->service_messageid = null;
                $message->save();

                // success!
                $reponse = array(
                    "payload" => array(
                        "domain" => $this->domain,
                        "success" => "true"
                    ),
                    "error" => $this->api_service->get_error_msg(0)
                );

            }

            else
            {
                // Required parameters are missing or invalid
                $reponse = array(
                    "payload" => array(
                        "domain" => $this->domain,
                        "success" => "false"
                    ),
                    "error" => $this->api_service->get_error_msg(002)
                );
            }

        }

        else
        {
            // Authentication Failed. Invalid User or App Key
            $reponse = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
                "error" => $this->api_service->get_error_msg(005)
            );
        }
        
        // Set the response data
        $this->response_data = ($response_type == 'json')
            ? $this->array_as_json($reponse)
            : $this->array_as_xml($reponse, array());
    }



    /**
     * Get the latitude and longitude for the default centre of the map.
     *
     * @return string
     */
    public function map_center()
    {
        $json_mapcenters = array(); //lat and lon string to parse to json

        // Find incidents
        $this->query = "SELECT default_lat AS latitude, default_lon AS 
            longitude FROM `".$this->table_prefix."settings`
            ORDER BY id DESC ;";

        $items = $this->db->query($this->query);
        
        // Set the no. of records fetched
        $this->record_count = $items->count();
        
        $i = 0;

        foreach ($items as $item)
        {
            // Needs different treatment depending on the output
            if($this->response_type == 'json')
            {
                $json_mapcenters[] = array("mapcenter" => $item);
            } 
            else 
            {
                $json_mapcenters['mapcenter'.$i] = array(
                        "mapcenter" => $item) ;
                        
                $this->replar[] = 'mapcenter'.$i;
            }

            $i++;
        }

        // Create the json array
        $data = array("payload" => array(
                    "domain" => $this->domain,
                    "mapcenters" => $json_mapcenters
                ),
                "error" => $this->api_service->get_error_msg(0));

        // Set the response data
        $this->response_data =($this->response_type == 'json')
            ? $this->array_as_json($data)
            : $this->array_as_xml($data, $this->replar);
    }

}
