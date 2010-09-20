<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles activities regarding tagging media to existing report.
 *
 * @version 23 - Henry Addo 2010-09-20
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

class PostTagMedia
{
    private $data; // items to parse to JSON.
    private $items; // categories to parse to JSON.
    private $query; // Holds the SQL query
    private $replar; // assists in proper XML generation.
    private $db;
    private $domain;
    private $table_prefix;
    private $list_limit;
    private $ret_json_or_xml;
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
        $this->replar = array();
        $this->domain = $this->api_actions->_get_domain();
        $this->db = $this->api_actions->_get_db();

    }

    /**
 	 * Tag a news item to an incident.
     * 
     * @param int incidentid - The incident id.
     * @param string mediatype - The media type,video, picture,etc
     *
     * @return Array
 	 */
	public function _tag_media($incidentid, $mediatype, $reponse_type) 
    {
	    if ($_POST) 
        {
			//get the locationid for the incidentid
		    $locationid = 0;

			$this->query = "SELECT location_id FROM ".$this->table_prefix.
                "incident WHERE id=$incidentid";

			$this->items = $this->db->query($this->query);

			if(count($this->items) > 0)
            {
				$locationid = $this->items[0]->location_id;
			}

			$media = new Media_Model(); //create media model object

			$url = '';

			$post = Validation::factory(array_merge($_POST,$_FILES));

			if($mediatype == 2 || $mediatype == 4)
            {
				//require a url
				if(!$this->api_actions->_verify_array_index($_POST, 'url'))
                {
					if($this->response_type == 'json')
                    {
						json_encode(array(
                            "error" => $this->api_actions->
                                _get_error_msg(001, 'url'))
                        );
					} 
                    else 
                    {
						$err = array(
                                "error" => $this->api_actions->
                                    _get_error_msg(001,'url')
                        );

						return $this->api_actions->
                            _array_as_XML($err, array());
					}
				} 
                else 
                {
					$url = $_POST['url'];
					$media->media_link = $url;
				}
			} 
            else 
            {
			    if(!$this->api_actions->
                        _verify_array_index($_POST, 'photo'))
                {
					if($this->response_type == 'photo')
                    {
						json_encode(array(
                            "error" => $this->api_actions->
                                _get_error_msg(001, 'photo'))
                        );
					} 
                    else 
                    {
						$err = array(
                            "error" => $this->api_actions->
                                _get_error_msg(001, 'photo')
                        );

						return $this->api_actions->
                            _array_as_XML($err, array());
					}
				}

				$post->add_rules('photo', 'upload::valid', 
                        'upload::type[gif,jpg,png]', 'upload::size[1M]');

				if($post->validate())
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

			//optional title & description
			$title = '';
			if($this->api_actions->_verify_array_index($_POST, 'title'))
            {
				$title = $_POST['title'];
			}

			$description = '';
			if($this->api_actions->_verify_array_index(
                    $_POST, 'description'))
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

			//SUCESS!!!
			$ret = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "success" => "true"
                ),
                "error" => $this->api_actions->_get_error_msg(0)
            );

			if($this->response_type == 'json')
            {
				return json_encode($ret);
			} 
            else 
            {
			    return $this->api_actions->_array_as_XML($ret, array());
			}
		} 
        else 
        {
			if($this->response_type == 'json')
            {
				return json_encode(array(
                        "error" => $this->api_actions->
                        _get_error_msg(003)));
			} 
            else 
            {
				$err = array(
                    "error" => $this->api_actions->_get_error_msg(003)
                );

			    return $this->api_actions->_array_as_XML($err, array());
			}
		}
	}

}
