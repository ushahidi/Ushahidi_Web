<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles API requests.
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

class Api_Controller extends Controller {
	var $db; //Database instance for queries
	var $list_limit; //number of records to limit response to - set in __construct
	var $responseType; //type of response, either json or xml as specified, defaults to json in set in __construct
	
	/*
	determines what to do
	*/
	function switchTask(){
		$task = ""; //holds the task to perform as requested
		$ret = ""; //return value
		$request = array();
		$error = array();
		
		//determine if we are using GET or POST
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$request =& $_GET;
		} else {
			$request =& $_POST;
		}
		
		//make sure we have a task to work with
		if(!$this->_verifyArrayIndex($request, 'task')){
			$error = array("error" => $this->_getErrorMsg(001, 'task'));
			$task = "";
		} else {
			$task = $request['task'];
		}
		
		//response type
		if(!$this->_verifyArrayIndex($request, 'resp')){
			$this->responseType = 'json';
		} else {
			$this->responseType = $request['resp'];
		}
		
		switch($task){
			case "report": //report/add an incident
				$ret = $this->_report();
				break;
				
			case "tagnews": //tag a news item to an incident
			case "tagvideo": //report/add an incident
			case "tagphoto": //report/add an incident
				$incidentid = '';
				
				if(!$this->_verifyArrayIndex($request, 'incidentid')){
					$error = array("error" => $this->_getErrorMsg(001, 'incidentid'));
					break;
				} else {
					$incidentid = $request['incidentid'];
				}
				
				
				$mediatype = 0;
				if($task == "tagnews")
					$mediatype = 4;
					
				if($task == "tagvideo")
					$mediatype = 2;
					
				if($task == "tagphoto")
					$mediatype = 1;
					
				$ret = $this->_tagMedia($incidentid, $mediatype);
				
				break;
				
			case "categories": //retrieve categories
				$ret = $this->_categories();
				break;
				
			case "category": //retrieve categories
				$id = 0;
				
				if(!$this->_verifyArrayIndex($request, 'id')){
					$error = array("error" => $this->_getErrorMsg(001, 'id'));
					break;
				} else {
					$id = $request['id'];
				}
				
				$ret = $this->_category($id);
				break;
				
			case "locations": //retrieve locations
				$ret = $this->_locations();
				break;
				
			case "location": //retrieve locations
				$by = '';
				
				if(!$this->_verifyArrayIndex($request, 'by')){
					$error = array("error" => $this->_getErrorMsg(001, 'by'));
					break;
				} else {
					$by = $request['by'];
				}
				
				switch ($by){
					case "latlon": //latitude and longitude
						//
						break;
					case "locid": //id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_locationById($request['id']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'id'));
						}
						break;
					case "country": //id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_locationByCountryId($request['id']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'id'));
						}
						break;
					default:
						$error = array("error" => $this->_getErrorMsg(002));
				}
				
				break;
				
			case "countries": //retrieve countries
				$ret = $this->_countries();
				break;
				
			case "country": //retrieve countries
				$by = '';
				
				if(!$this->_verifyArrayIndex($request, 'by')){
					$error = array("error" => $this->_getErrorMsg(001, 'by'));
					break;
				} else {
					$by = $request['by'];
				}
				
				switch ($by){
					case "countryid": //id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_countryById($request['id']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'id'));
						}
						break;
					case "countryname": //name
						if(($this->_verifyArrayIndex($request, 'name'))){
							$ret = $this->_countryByName($request['name']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'name'));
						}
						break;
					case "countryiso": //name
						if(($this->_verifyArrayIndex($request, 'iso'))){
							$ret = $this->_countryByIso($request['iso']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'iso'));
						}
						break;
					default:
						$error = array("error" => $this->_getErrorMsg(002));
				}
				
				break;
				
			case "incidents": //retrieve incidents
				/*
				there are several ways to get incidents by
				*/
				$by = '';
				
				if(!$this->_verifyArrayIndex($request, 'by')){
					$error = array("error" => $this->_getErrorMsg(001, 'by'));
					break;
				} else {
					$by = $request['by'];
				}
				
				switch ($by){
					case "latlon": //latitude and longitude
						if(($this->_verifyArrayIndex($request, 'latitude')) && ($this->_verifyArrayIndex($request, 'longitude'))){
							$ret = $this->_incidentsByLatLon($request['latitude'], $request['longitude']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'latitude or longitude'));
						}
						break;
					case "address": //address
						if(($this->_verifyArrayIndex($request, 'address'))){
							$ret = $this->_incidentsByAddress($request['address']);
						} else {
							$error = json_encode(array("error" => $this->_getErrorMsg(001, 'address')));
						}
						break;
					case "locid": //Location Id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_incidentsByLocitionId($request['id']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'id'));
						}
						break;
					case "locname": //Location Name
						if(($this->_verifyArrayIndex($request, 'name'))){
							$ret = $this->_incidentsByLocationName($request['name']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'name'));
						}
						break;
					case "catid": //Category Id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_incidentsByCategoryId($request['id']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'id'));
						}
						break;
					case "catname": //Category Name
						if(($this->_verifyArrayIndex($request, 'name'))){
							$ret = $this->_incidentsByCategoryName($request['name']);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'name'));
						}
						break;
					default:
						$error = array("error" => $this->_getErrorMsg(002));
				}
				
				break;
			default:
				$error = array("error" => $this->_getErrorMsg(999));
				break;
		}
		
		//create the response depending on the kind that was requested
		if(!empty($error) || count($error) > 0){
			if($this->responseType == 'json'){
				$ret = json_encode($error);
			} else {
				$ret = $this->_arrayAsXML($error, array());
			}
		}
		
		//avoid caching
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		$mime = "";
		if($this->responseType == 'xml'){
			header("Content-type: text/xml");
		}
		
		print $ret;
		
		//END
	}
	
	/*
	Makes sure the appropriate key is there in a given array (POST or GET) and that it is set
	*/
	function _verifyArrayIndex(&$ar, $index){
		if(isset($ar[$index]) && array_key_exists($index, $ar)){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	returns an array error - array("code" => "CODE", "message" => "MESSAGE") based on the given code
	*/
	function _getErrorMsg($errcode, $param = ''){
		switch($errcode){
			case 0:
				return array("code" => "0", "message" => "No Error");
			case 001:
				return array("code" => "001", "message" => "Missing Parameter - $param");
			case 002:
				return array("code" => "002", "message" => "Invalid Parameter");
			case 003:
				return array("code" => "003", "message" => "Form Post Failed");
			default:
				return array("code" => "999", "message" => "Not Found");
		}
	}
	
	/*
	generic function to get incidents by given set of parameters
	*/
	function _getIncidents($where = '', $limit = ''){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_incidents = array(); //incidents to parse to json
		
		$media_items = array(); //incident media
		$json_incident_media = array(); //incident media
		
		$retJsonOrXml = ''; //will hold the json/xml string to return
		
		$replar = array(); //assists in proper xml generation

		//find incidents
		$query = "SELECT i.id AS incidentid,i.incident_title AS incidenttitle, i.incident_description AS incidentdescription,l.id AS locationid, l.location_name AS locationname, 
			c.id AS categoryid, c.category_title AS categorytitle  
			FROM `incident` AS i 
			INNER JOIN `location` as l ON l.id = i.location_id 
			INNER JOIN `incident_category` AS ic ON ic.incident_id = i.id
			INNER JOIN `category` c ON ic.category_id = c.id
			$where
			$limit";

		$items = $this->db->query($query);
		$i = 0;
		
		foreach ($items as $item){
			//get the incident's associted media
			$query = "SELECT m.id as id, m.media_title AS title, m.media_type AS type, m.media_link AS link, m.media_thumb AS thumb
				FROM media AS m INNER JOIN incident AS i ON i.id = m.incident_id WHERE i.id = $item->incidentid";
			
			$media_items = $this->db->query($query);
			
			if($this->responseType == 'json'){
				$json_incident_media = array();
			} else {
				$json_incident_media = array();

			}
			
			if(count($media_items) > 0){
				$j = 0;
				foreach ($media_items as $media_item){
					if($this->responseType == 'json'){
						$json_incident_media[] = $media_item;
					} else {
						$json_incident_media['mediaitem'.$j] = $media_item;
					}
					$j++;
				}
			}
			
			//needs different treatment depending on the output
			if($this->responseType == 'json'){
				$json_incidents[] = array("incident" => $item, "media" => $json_incident_media);
			} else {
				$json_incidents['incident'.$i] = array("incident" => $item, "media" => $json_incident_media) ;
				//$replar[] = 'incident'.$i;
			}
			
			$i++;
		}
		
		//create the json array
		$data = array(
			"payload" => array("incidents" => $json_incidents),
			"error" => $this->_getErrorMsg(0)
		);
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($data);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($data, $replar);
		}
		
		return $retJsonOrXml;
	}
	
	/*
	report an incident
	*/
	function _report(){
		$retJsonOrXml = array();
		$reponse = array();
		
		if($this->_submit()){
			
			$reponse = array(
				"payload" => array("success" => "true"),
				"error" => $this->_getErrorMsg(0)
			);
			
			//return;
			//return $this->_incidentById();
			
		} else {
			$reponse = array(
				"payload" => array("success" => "false"),
				"error" => $this->_getErrorMsg(003)
			);
		}
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($reponse);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($reponse, array());
		}
		
		return $retJsonOrXml;
	}
	
	/*
	the actual reporting - ***must find a cleaner way to do this than duplicating code verbatim - modify report***
	*/
	function _submit()
	{		
	    
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
			$post->add_rules('incident_hour','required','between[1,12]');
			//$post->add_rules('incident_minute','required','between[0,59]');
			
			if($this->_verifyArrayIndex($_POST, 'incident_ampm')){
				if ($_POST['incident_ampm'] != "am" && $_POST['incident_ampm'] != "pm")
				{
					$post->add_error('incident_ampm','values');
		        }
			}
	        
			$post->add_rules('latitude','required','between[-90,90]');		// Validate for maximum and minimum latitude values
			$post->add_rules('longitude','required','between[-180,180]');	// Validate for maximum and minimum longitude values
			$post->add_rules('location_name','required', 'length[3,200]');
			$post->add_rules('incident_category','required','numeric');
			
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
	        if ($post->validate()) //
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
				
				$incident_date=split("/",$post->incident_date);
				// where the $_POST['date'] is a value posted by form in mm/dd/yyyy format
					$incident_date=$incident_date[2]."-".$incident_date[0]."-".$incident_date[1];
					
				$incident_time = $post->incident_hour . ":" . $post->incident_minute . ":00 " . $post->incident_ampm;
				$incident->incident_date = $incident_date . " " . $incident_time;
				$incident->incident_dateadd = date("Y-m-d H:i:s",time());
				$incident->save();
				
				// SAVE CATEGORIES
				if(!empty($post->incident_category) && is_array($post->incident_category)){
					foreach($post->incident_category as $item)
					{
						$incident_category = new Incident_Category_Model();
						$incident_category->incident_id = $incident->id;
						$incident_category->category_id = $item;
						$incident_category->save();
					}
				}
				
				// STEP 4: SAVE MEDIA
				// a. News
				if(!empty( $post->incident_news ) && 
				    is_array($post->incident_news)){ 
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
				if( !empty( $post->incident_video) && 
				    is_array( $post->incident_video)){ 

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
				if( !empty($post->incident_photo))){
				    $filenames = upload::save('incident_photo');
				    $i = 1;
				    foreach ($filenames as $filename) {
					    $new_filename = $incident->id . "_" . $i . "_" . time();
					
					    // Resize original file... make sure its max 408px wide
					    Image::factory($filename)->resize(408,248,Image::AUTO)
						    ->save(Kohana::config('upload.directory', TRUE) . $new_filename . ".jpg");
					
					    // Create thumbnail
					    Image::factory($filename)->resize(70,41,Image::HEIGHT)
						    ->save(Kohana::config('upload.directory', TRUE) . $new_filename . "_t.jpg");
					
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
				if(!empty($post->person_first) || 
				    !empty($post->person_last)){ 
	                
	                $person = new Incident_Person_Model();
				    $person->location_id = $location->id;
				    $person->incident_id = $incident->id;
				    $person->person_first = $post->person_first;
				    $person->person_last = $post->person_last;
				    $person->person_email = $post->person_email;
				    $person->person_date = date("Y-m-d H:i:s",time());
				    $person->save();
				}
				
				return true;
	            
	        }
	
            // No! We have validation errors, we need to show the form again, with the errors
	        else   
			{
				//FAILED!!!
				return false;
	        }
	    }		
		else
		{
			return false;
		}
		
	}
	
	/*
	Tag a news item to an incident
	*/
	function _tagMedia($incidentid, $mediatype){
		if ($_POST) //
	    {

			//get the locationid for the incidentid
			$locationid = 0;
			
			$query = "SELECT location_id FROM incident WHERE id=$incidentid";
			
			$items = $this->db->query($query);
			if(count($items) > 0)
			{
				$locationid = $items[0]->location_id;
			}
			
			$media = new Media_Model(); //create media model object
			
			$url = '';
			
			$post = Validation::factory(array_merge($_POST,$_FILES));
			
			if($mediatype == 2 || $mediatype == 4){
				//require a url
				if(!$this->_verifyArrayIndex($_POST, 'url')){
					if($this->responseType == 'json'){
						json_encode(array("error" => $this->_getErrorMsg(001, 'url')));
					} else {
						$err = array("error" => $this->_getErrorMsg(001, 'url'));
						return $this->_arrayAsXML($err, array());
					}
				} else {
					$url = $_POST['url'];
					$media->media_link = $url;
				}
			} else {
				if(!$this->_verifyArrayIndex($_POST, 'photo')){
					if($this->responseType == 'photo'){
						json_encode(array("error" => $this->_getErrorMsg(001, 'photo')));
					} else {
						$err = array("error" => $this->_getErrorMsg(001, 'photo'));
						return $this->_arrayAsXML($err, array());
					}
				}
				
				$post->add_rules('photo', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[1M]');
				
				if($post->validate()){
					//assuming this is a photo
					$filename = upload::save('photo');
					$new_filename = $incidentid . "_" . $i . "_" . time();
								
					// Resize original file... make sure its max 408px wide
					Image::factory($filename)->resize(408,248,Image::AUTO)
							->save(Kohana::config('upload.directory', TRUE) . $new_filename . ".jpg");
								
					// Create thumbnail
					Image::factory($filename)->resize(70,41,Image::HEIGHT)
							->save(Kohana::config('upload.directory', TRUE) . $new_filename . "_t.jpg");
								
					// Remove the temporary file
					unlink($filename);
								
					$media->media_link = $new_filename . ".jpg";
					$media->media_thumb = $new_filename . "_t.jpg";
				}
			}
			
			//optional title & description
			$title = '';
			if($this->_verifyArrayIndex($_POST, 'title')){
				$title = $_POST['title'];
			}
			
			$description = '';
			if($this->_verifyArrayIndex($_POST, 'description')){
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
				"payload" => array("success" => "true"),
				"error" => $this->_getErrorMsg(0)
			);
			
			if($this->responseType == 'json'){
				return json_encode($ret);
			} else {
				return $this->_arrayAsXML($ret, array());
			}
	    }		
		else
		{
			if($this->responseType == 'json'){
				return json_encode(array("error" => $this->_getErrorMsg(003)));
			} else {
				$err = array("error" => $this->_getErrorMsg(003));
				return $this->_arrayAsXML($err, array());
			}
			
		}
	}
	
	/*
	get a list of categories
	*/
	function _categories(){

		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_categories = array(); //incidents to parse to json
		
		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$query = "SELECT id, category_title, category_description, category_color FROM `category` WHERE category_visible = 1 ";

		$items = $this->db->query($query);
		$i = 0;
		
		$replar = array(); //assists in proper xml generation
		
		foreach ($items as $item){
			
			//needs different treatment depending on the output
			if($this->responseType == 'json'){
				$json_categories[] = array("category" => $item);
			} else {
				$json_categories['category'.$i] = array("category" => $item) ;
				$replar[] = 'category'.$i;
			}
			
			$i++;
		}
		
		//create the json array
		$data = array(
			"payload" => array("categories" => $json_categories),
			"error" => $this->_getErrorMsg(0)
		);
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($data);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($data, $replar);
		}

		return $retJsonOrXml;
	}
	
	/*
	get a single category
	*/
	function _category($id){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_categories = array(); //incidents to parse to json
		
		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$query = "SELECT id, category_title, category_description, category_color FROM `category` WHERE category_visible = 1 AND id=$id";

		$items = $this->db->query($query);
		$i = 0;
		
		$replar = array(); //assists in proper xml generation
		
		foreach ($items as $item){
			
			//needs different treatment depending on the output
			if($this->responseType == 'json'){
				$json_categories[] = array("category" => $item);
			} else {
				$json_categories['category'.$i] = array("category" => $item) ;
				$replar[] = 'category'.$i;
			}
			
			$i++;
		}
		
		//create the json array
		$data = array(
			"payload" => array("categories" => $json_categories),
			"error" => $this->_getErrorMsg(0)
		);
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($data);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($data, $replar);
		}

		return $retJsonOrXml;
	}
	
	/*
	get a list of locations
	*/
	function _getLocations($where = '', $limit = ''){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_locations = array(); //incidents to parse to json
		
		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$query = "SELECT id, location_name, country_id, latitude, longitude FROM `location` $where $limit ";

		$items = $this->db->query($query);
		$i = 0;
		
		$replar = array(); //assists in proper xml generation
		
		foreach ($items as $item){
			
			//needs different treatment depending on the output
			if($this->responseType == 'json'){
				$json_locations[] = array("location" => $item);
			} else {
				$json_locations['location'.$i] = array("location" => $item) ;
				$replar[] = 'location'.$i;
			}
			
			$i++;
		}
		
		//create the json array
		$data = array(
			"payload" => array("locations" => $json_locations),
			"error" => $this->_getErrorMsg(0)
		);
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($data);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($data, $replar);
		}

		return $retJsonOrXml;
	}
	
	/*
	get a single location by id
	*/
	function _locations(){
		$where = "\n WHERE location_visible = 1 ";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getLocations($where, $limit);
	}
	
	/*
	get a single location by id
	*/
	function _locationById($id){
		$where = "\n WHERE location_visible = 1 AND id=$id";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getLocations($where, $limit);
	}
	
	/*
	get a single location by id
	*/
	function _locationByCountryId($id){
		$where = "\n WHERE location_visible = 1 AND country_id=$id";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getLocations($where, $limit);
	}	
	
	/*
	country query abstraction
	*/
	function _getCountries($where = '', $limit = ''){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_countries = array(); //incidents to parse to json
		
		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$query = "SELECT id, iso, country as `name`, capital FROM `country` $where $limit";

		$items = $this->db->query($query);
		$i = 0;
		
		$replar = array(); //assists in proper xml generation
		
		foreach ($items as $item){
			
			//needs different treatment depending on the output
			if($this->responseType == 'json'){
				$json_countries[] = array("country" => $item);
			} else {
				$json_countries['country'.$i] = array("country" => $item) ;
				$replar[] = 'country'.$i;
			}
			
			$i++;
		}
		
		//create the json array
		$data = array(
			"payload" => array("countries" => $json_countries),
			"error" => $this->_getErrorMsg(0)
		);
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($data);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($data, $replar);
		}

		return $retJsonOrXml;
	}
	
	/*
	get a list of countries
	*/
	function _countries(){
		$where = "";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getCountries($where, $limit);
	}
	
	/*
	get a country by name
	*/
	function _countryByName($name){
		$where = "\n WHERE country = '$name'";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getCountries($where, $limit);
	}
	
	/*
	get a country by id
	*/
	function _countryById($id){
		$where = "\n WHERE id=$id";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getCountries($where, $limit);
	}
	
	/*
	get a country by iso
	*/
	function _countryByIso($iso){
		$where = "\n WHERE iso='$iso'";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getCountries($where, $limit);
	}
	
	/*
	get incident by id
	*/
	function _incidentById($id){
		$where = "\nWHERE i.id = $id AND i.incident_active = 1";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where, $limit);
	}
	
	/*
	get the incidents by latitude and longitude
	*/
	function _incidentsByLatLon($lat, $long){
		
	}
	
	/*
	get the incidents by address
	*/
	function _incidentsByAddress($address){
		
	}
	
	/*
	get the incidents by location id
	*/
	function _incidentsByLocitionId($locid){
		$where = "\nWHERE i.location_id = $locid AND i.incident_active = 1";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where, $limit);
	}
	
	/*
	get the incidents by location name
	*/
	function _incidentsByLocationName($locname){
		$where = "\nWHERE l.location_name = '$locname' AND i.incident_active = 1";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where, $limit);
	}
	
	/*
	get the incidents by category id
	*/
	function _incidentsByCategoryId($catid){
		$where = "\nWHERE c.id = $catid AND i.incident_active = 1";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where, $limit);
	}
	
	/*
	get the incidents by category name
	*/
	function _incidentsByCategoryName($catname){
		$where = "\nWHERE c.category_title = '$catname' AND i.incident_active = 1";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where, $limit);
	}
	
	/*
	constructor
	*/
	function __construct(){
		$this->db = new Database;
		$this->list_limit = '20';
		$this->responseType = 'json';
	}
	
	/*
	starting point
	*/
	public function index(){
		//switch task
		$this->switchTask();
	}
	
	/*
	Creates a JSON response given an array
	*/
	function _arrayAsJSON($data){
		return json_encode($data);
	}
	
	/*
	converts an object to an array
	*/
	function _object2array($object) {
	    if (is_object($object)) {
	        foreach ($object as $key => $value) {
	            $array[$key] = $value;
	        }
	    }
	    else {
	        $array = $object;
	    }
	    return $array;
	}
	
	/*
	Creates a XML response given an array
	CREDIT TO: http://snippets.dzone.com/posts/show/3391
	*/
	function _write(XMLWriter $xml, $data, $replar = ""){
		foreach($data as $key => $value){
			if(is_a($value, 'stdClass')){
				//echo 'convert to an array';
				$value = $this->_object2array($value);
			}
			
         	if(is_array($value)){
         		$toprint = true;
				
         		if(in_array($key, $replar)){
					//move up one level
					$keys = array_keys($value);
					$key = $keys[0];
					
					$value = $value[$key];
				}
				
	            $xml->startElement($key);
		        $this->_write($xml, $value, $replar);
	            $xml->endElement();

             	continue;
         	}
         	//echo $key.' - '.$value."::";
         	
         	$xml->writeElement($key, $value);
     	}	
	}
	
	/*
	Creates a XML response given an array
	CREDIT TO: http://snippets.dzone.com/posts/show/3391
	*/
	function _arrayAsXML($data, $replar = array()){
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('response');

		$this->_write($xml, $data, $replar);

		$xml->endElement();
	  	return $xml->outputMemory(true);
	}

}
