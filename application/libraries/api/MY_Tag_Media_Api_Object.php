<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles activities regarding tagging media to existing report.
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

class Tag_Media_Api_Object extends Api_Object_Core {
    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }
    
    /**
     * Handler for the API task request
     */
    public function perform_task()
    {
        if ( ! $this->api_service->verify_array_index($this->request, 'id'))
        {
            $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'id')
            ));
            return;
        }
        else
        {
            // Get the media type
            $media_type = $this->_get_media_type(strtolower($this->api_service->get_task_name()));

            // Tag the media and set the response data
            $this->response_data = $this->_tag_media($this->check_id_value($this->request['id']), $media_type);
        }
    }
    
    /**
     * Get the media type id
     *
     * @param $task_name Name of the task
     */
    private function _get_media_type($task_name)
    {
        switch ($task_name)
        {
            case "tagnews":
                return 4;
            case "tagvideo":
                return 2;
            case "tagphoto":
                return 1;
            default:
                return 0;
        }
    }

    /**
     * Tag a news item to an incident.
     * 
     * @param int incidentid - The incident id.
     * @param string mediatype - The media type,video, picture,etc
     *
     * @return Array
     */
    private function _tag_media($incidentid, $mediatype) 
    {
        if ($_POST) 
        {

            // Check if incident ID exist 
            $incidentid_exist = Incident_Model::is_valid_incident($incidentid);

            if(!$incidentid_exist) 
            {
                return $this->set_error_message(array("error" => 
                    $this->api_service->get_error_msg(012)));
            }

            // Get the locationid for the incidentid
            $locationid = 0;

            $items = ORM::factory('incident')->select(array('location_id'))->where(array('incident.id' => $incidentid))->find();

            if ($items->count_all() > 0)
            {
                $locationid = $items->location_id;
            }

            $media = new Media_Model(); //create media model object

            $url = '';

            $post = Validation::factory(array_merge($_POST, $_FILES));

            if ($mediatype == 2 OR $mediatype == 4)
            {
                //require a url
                if ( ! $this->api_service->verify_array_index($this->request, 'url'))
                {
                    return $this->set_error_message(array("error" => 
                        $this->api_service->get_error_msg(001, 'url')));
                } 
                else 
                {
                    $url = $this->request['url'];
                    $media->media_link = $url;
                }
            } 
            else 
            {
                if ( ! $this->api_service->verify_array_index($this->request, 'photo'))
                {
                    $this->set_error_message(array("error" => 
                        $this->api_service->get_error_msg(001),'photo'));
                }

                $post->add_rules('photo', 'upload::valid', 
                    'upload::type[gif,jpg,png]', 'upload::size[1M]');

                if ($post->validate(FALSE))
                {
                    //assuming this is a photo
                    $filename = upload::save('photo');
                    $new_filename = $incidentid . "_" . $i . "_" . time();

                    // Resize original file... make sure its max 408px wide
                    Image::factory($filename)->resize(408,248,Image::AUTO)->
                        save(Kohana::config('upload.directory', TRUE) .
                            $new_filename . ".jpg");

                    // Create thumbnail
                    Image::factory($filename)->resize(70,41,Image::HEIGHT)->
                        save(Kohana::config('upload.directory', TRUE) .
                                $new_filename . "_t.jpg");

                    // Remove the temporary file
                    unlink($filename);

                    $media->media_link = $new_filename . ".jpg";
                    $media->media_thumb = $new_filename . "_t.jpg";
                }
            }

            // Optional title & description
            $title = '';
            
            if ($this->api_service->verify_array_index($_POST, 'title'))
            {
                $title = $_POST['title'];
            }

            $description = '';
            
            if ($this->api_service->verify_array_index($_POST, 'description'))
            {
                $description = $_POST['description'];
            }

            $media->location_id = $locationid;
            $media->incident_id = $incidentid;
            $media->media_type = $mediatype;
            $media->media_title = $title;
            $media->media_description = $description;
            $media->media_date = date("Y-m-d H:i:s",time());

            $media->save(); //save the thing

            // SUCESS!!!
            $ret = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "success" => "true"
                ),
                "error" => $this->api_service->get_error_msg(0)
            );
            return $this->set_error_message($ret);
        } 
        else 
        {
            return $this->set_error_message(array("error" => 
                $this->api_service->get_error_msg(003)));
        }
    }
}
