<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Viddler_Api_Object
 *
 * This class handles Viddler video API functions
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

class Viddler_Api_Object extends Api_Object_Core {
    
    protected $replar;
    
    protected $action;
    
    protected $id;
    
    protected $vid;
    
    protected $response;

    public function __construct($api_service)
    {
        $this->replar = array();
        parent::__construct($api_service);
        
        $this->action = $this->request['action'];
        $this->id = (isset($this->request['id'])) ? $this->request['id'] : FALSE;
        $this->vid = new viddlerevents;
    }

    /**
     * Implementation of abstract method declared in superclass
     */
    public function perform_task()
    {	
    	switch ($this->action){
    		
    		// Upload a video
    		case 'upload':
    			$this->_upload_video();
    		break;
    		
    		// Get videos for a report or all reports
    		case 'get':
    			$this->_get_videos();
    		break;
    		
    		// Proper action not set
    		default:
    			// System information mainly obtained through use of callback
		        // Therefore set the default response to "not found"
		        $this->set_error_message(array(
		            "error" => $this->api_service->get_error_msg(999)
		        ));
    	}
    }
    
    public function _upload_video()
    {
    	$upload = $this->vid->upload_video($this->id);
    	
    	if($upload)
    	{
    		$this->response = array("payload" => array(
                    "domain" => $this->domain,
                    "success" => "true"
                ),
                "error" => $this->api_service->get_error_msg(0));
		}else{
			$this->response = array("payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
                "error" => $this->api_service->get_error_msg(011));
		}
    	$this->response_data = $this->respond();
    }
    
    public function _get_videos()
    {
    	$videos = $this->vid->get_videos($this->id);
		$response = array();
		
		foreach($videos as $video) {
			$response[(int)$video->incident_id][(string)$video->viddler_id] = $video->url;
		}
		
		$response = array(
			"payload" => array(
				"domain" => $this->domain,
				"videos" => $response
			),
			"error" => $this->api_service->get_error_msg(0)
		);
		
		$this->response = $response;
		
		$this->response_data = $this->respond();
    }
    
    public function respond()
    {
    	 if ($this->response_type == 'json')
        {
            return json_encode($this->response);
        } 
        else 
        {
            return $this->array_as_xml($this->response, array());
        }
    }
    
    
    
}
