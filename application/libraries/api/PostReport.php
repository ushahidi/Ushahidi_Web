<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles posting report to ushahidi via the API.
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

class PostReport
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
 	 * Submit a report
     *
     * @param string response_type - JSON or XML
     *
     * @return Array
 	 */
	public function _report($response_type)
    {
	    
		$reponse = array();
		$ret_value = $this->_submit();
		
        if($ret_value == 0 )
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "true"
                ),
				"error" => $this->api_actions->_get_error_msg(0)
			);
			
		} 
        else if( $ret_value == 1 ) 
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
				"error" => $this->api_actions->
                    _get_error_msg(003,'',$this->error_messages)
			);
		} 
        else 
        {
			$reponse = array(
				"payload" => array(
                    "domain" => $this->domain,
                    "success" => "false"
                ),
				"error" => $this->api_actions->_get_error_msg(004)
			);
		}

		if($response_type == 'json')
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_JSON($reponse);
		} 
        else 
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_XML($reponse, array());
		}

		return $this->ret_json_or_xml;
	}

	/**
 	 * The actual reporting -
     *
     * @return int
 	 */
	private function _submit() 
    {
		// setup and initialize form field names
		$form = array
		(
			'incident_title' => '',
			'incident_description' => '',
			'incident_date' => '',
			'incident_hour' => '',
			'incident_minute' => '',
			'incident_ampm' => '',
			'latitude' => '',
			'longitude' => '',
			'location_name' => '',
			'country_id' => '',
			'incident_category' => '',
			'incident_news' => array(),
			'incident_video' => array(),
			'incident_photo' => array(),
			'person_first' => '',
			'person_last' => '',
			'person_email' => ''
		);
		
		$this->messages = $form;
		
        // check, has the form been submitted, if so, setup validation
		if ($_POST) 
        {
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));

			//  Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('incident_title','required', 'length[3,200]');
			$post->add_rules('incident_description','required');
			$post->add_rules('incident_date','required','date_mmddyyyy');
			$post->add_rules('incident_hour','required','between[0,23]');
			//$post->add_rules('incident_minute','required','between[0,59]');

			if($this->api_actions->_verify_array_index(
                        $_POST, 'incident_ampm')) 
            {
				if ($_POST['incident_ampm'] != "am" && 
                        $_POST['incident_ampm'] != "pm") 
                {
					$post->add_error('incident_ampm','values');
			    }
			}

			$post->add_rules('latitude','required','between[-90,90]');	
			$post->add_rules('longitude','required','between[-180,180]');
			$post->add_rules('location_name','required', 'length[3,200]');
			$post->add_rules('incident_category','required',
                    'length[1,100]');

			// Validate Personal Information
			if (!empty($post->person_first)) 
            {
				$post->add_rules('person_first', 'length[3,100]');
			}

			if (!empty($post->person_last)) 
            {
				$post->add_rules('person_last', 'length[3,100]');
			}

			if (!empty($post->person_email)) 
            {
				$post->add_rules('person_email', 'email', 'length[3,100]');
			}

			// Test to see if things passed the rule checks
			if ($post->validate()) 
            {
				// SAVE LOCATION (***IF IT DOES NOT EXIST***)
				$location = new Location_Model();
				$location->location_name = $post->location_name;
				$location->latitude = $post->latitude;
				$location->longitude = $post->longitude;
				$location->location_date = date("Y-m-d H:i:s",time());
				$location->save();

				// SAVE INCIDENT
				$incident = new Incident_Model();
				$incident->location_id = $location->id;
				$incident->user_id = 0;
				$incident->incident_title = $post->incident_title;
				$incident->incident_description = $post->incident_description;

				$incident_date=explode("/",$post->incident_date);
				/**
		 		* where the $_POST['date'] is a value posted by form in
		 		* mm/dd/yyyy format
		 		*/
				$incident_date=$incident_date[2]."-".$incident_date[0]."-".
                    $incident_date[1];

				$incident_time = $post->incident_hour . ":" . 
                    $post->incident_minute . ":00 " . $post->incident_ampm;
				$incident->incident_date = $incident_date . " " .
                    $incident_time;
				$incident->incident_dateadd = date("Y-m-d H:i:s",time());
				$incident->save();

				// SAVE CATEGORIES
				//check if data is csv or a single value.
				$pos = strpos($post->incident_category,",");
				if($pos === false)
                {
					//for backward compactibility. will drop support for it in the future.
					if(@unserialize($post->incident_category)) 
                    {
						$categories = unserialize($post->incident_category);
					} 
                    else 
                    {
						$categories = array($post->incident_category);
					}
				} 
                else 
                {
					$categories = explode(",",$post->incident_category);
				}

				if(!empty($categories) && is_array($categories)) 
                {
					foreach($categories as $item)
                    {
						$incident_category = new Incident_Category_Model();
						$incident_category->incident_id = $incident->id;
						$incident_category->category_id = $item;
						$incident_category->save();
					}
				}

				// STEP 4: SAVE MEDIA
				// a. News
				if(!empty( $post->incident_news ) && is_array(
                            $post->incident_news)) {
					foreach($post->incident_news as $item) 
                    {
						if(!empty($item)) 
                        {
							$news = new Media_Model();
							$news->location_id = $location->id;
							$news->incident_id = $incident->id;
							$news->media_type = 4;		// News
							$news->media_link = $item;
							$news->media_date = date("Y-m-d H:i:s",time());
							$news->save();
						}
					}
				}

				// b. Video
				if( !empty( $post->incident_video) && is_array( 
                            $post->incident_video))
                {

					foreach($post->incident_video as $item) 
                    {
						if(!empty($item)) 
                        {
							$video = new Media_Model();
							$video->location_id = $location->id;
							$video->incident_id = $incident->id;
							$video->media_type = 2;		// Video
							$video->media_link = $item;
							$video->media_date = date("Y-m-d H:i:s",time());
							$video->save();
						}
					}
				}

				// c. Photos
				if(!empty($post->incident_photo))
                {
					$filenames = upload::save('incident_photo');
					$i = 1;
					foreach ($filenames as $filename) 
                    {
						$new_filename = $incident->id . "_" . $i . "_" .
                            time();

						// Resize original file... make sure its max 408px wide
						Image::factory($filename)->resize(408,248,
                                Image::AUTO)->save(
                                    Kohana::config('upload.directory',
                                        TRUE) . $new_filename . ".jpg");

						// Create thumbnail
						Image::factory($filename)->resize(70,41,
                                Image::HEIGHT)->save(
                                    Kohana::config('upload.directory',
                                        TRUE) . $new_filename . "_t.jpg");

						// Remove the temporary file
						unlink($filename);

						// Save to DB
						$photo = new Media_Model();
						$photo->location_id = $location->id;
						$photo->incident_id = $incident->id;
						$photo->media_type = 1; // Images
						$photo->media_link = $new_filename . ".jpg";
						$photo->media_thumb = $new_filename . "_t.jpg";
						$photo->media_date = date("Y-m-d H:i:s",time());
						$photo->save();
						$i++;
					}
				}

				// SAVE PERSONAL INFORMATION IF ITS FILLED UP
				if(!empty($post->person_first) || !empty(
                            $post->person_last))
                {
					$person = new Incident_Person_Model();
					$person->location_id = $location->id;
					$person->incident_id = $incident->id;
					$person->person_first = $post->person_first;
					$person->person_last = $post->person_last;
					$person->person_email = $post->person_email;
					$person->person_date = date("Y-m-d H:i:s",time());
					$person->save();
				}

				return 0; //success

			} 
            else 
            { 				
                // populate the error fields, if any
				$this->messages = arr::overwrite($this->messages, 
                        $post->errors('report'));

				foreach ($this->messages as $error_item => 
                        $error_description) 
                {
					if(!is_array($error_description)) 
                    {
						$this->error_messages .= $error_description;
						if($error_description != end($this->messages)) 
                        {
							$this->error_messages .= " - ";
   						}
  					}
				}

			    //FAILED!!!
			    return 1; //validation error
	  		}
		} 
        else 
        {
			return 2; // Not sent by post method.
		}
	}

}

