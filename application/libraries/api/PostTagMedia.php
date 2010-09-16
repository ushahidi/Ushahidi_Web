<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles activities regarding tagging media to existing report.
 *
 * @version 22 - David Kobia 2010-08-30
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

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->query = '';
        $this->replar = array();
        $this->domain = $this->api_actions->_get_domain();
        $this->db = $this->api_actions->_get_db();

    }

    /**
 	* Tag a news item to an incident
 	*/
	public function _tagMedia($incidentid, $mediatype, $reponse_type) 
    {
	    if ($_POST) 
        {
			//get the locationid for the incidentid
			$locationid = 0;

			$query = "SELECT location_id FROM ".$this->table_prefix."incident WHERE id=$incidentid";

			$items = $this->db->query($query);
			if(count($items) > 0)
            {
				$locationid = $items[0]->location_id;
			}

			$media = new Media_Model(); //create media model object

			$url = '';

			$post = Validation::factory(array_merge($_POST,$_FILES));

			if($mediatype == 2 || $mediatype == 4)
            {
				//require a url
				if(!$this->_verifyArrayIndex($_POST, 'url'))
                {
					if($this->responseType == 'json')
                    {
						json_encode(array("error" => $this->_getErrorMsg(001, 'url')));
					} 
                    else 
                    {
						$err = array("error" => $this->_getErrorMsg(001,'url'));
						return $this->_arrayAsXML($err, array());
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
			    if(!$this->_verifyArrayIndex($_POST, 'photo'))
                {
					if($this->responseType == 'photo')
                    {
						json_encode(array("error" => $this->_getErrorMsg(001, 'photo')));
					} 
                    else 
                    {
						$err = array("error" => $this->_getErrorMsg(001, 'photo'));
						return $this->_arrayAsXML($err, array());
					}
				}

				$post->add_rules('photo', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[1M]');

				if($post->validate())
                {
					//assuming this is a photo
					$filename = upload::save('photo');
					$new_filename = $incidentid . "_" . $i . "_" . time();

					// Resize original file... make sure its max 408px wide
					Image::factory($filename)->resize(408,248,Image::AUTO)->save(Kohana::config('upload.directory', TRUE) . $new_filename . ".jpg");

					// Create thumbnail
					Image::factory($filename)->resize(70,41,Image::HEIGHT)->save(Kohana::config('upload.directory', TRUE) . $new_filename . "_t.jpg");

					// Remove the temporary file
					unlink($filename);

					$media->media_link = $new_filename . ".jpg";
					$media->media_thumb = $new_filename . "_t.jpg";
				}
			}

			//optional title & description
			$title = '';
			if($this->_verifyArrayIndex($_POST, 'title'))
            {
				$title = $_POST['title'];
			}

			$description = '';
			if($this->_verifyArrayIndex($_POST, 'description'))
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
			$ret = array("payload" => array("domain" => $this->domain,"success" => "true"),"error" => $this->_getErrorMsg(0));

			if($this->responseType == 'json')
            {
				return json_encode($ret);
			} 
            else 
            {
			    return $this->_arrayAsXML($ret, array());
			}
		} 
        else 
        {
			if($this->responseType == 'json')
            {
				return json_encode(array("error" => $this->_getErrorMsg(003)));
			} 
            else 
            {
				$err = array("error" => $this->_getErrorMsg(003));
			    return $this->_arrayAsXML($err, array());
			}
		}
	}

}
