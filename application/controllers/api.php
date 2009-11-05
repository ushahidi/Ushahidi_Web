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
	
	private $db; //Database instance for queries
	private $list_limit; //number of records to limit response to - set in __construct
	private $responseType; //type of response, either json or xml as specified, defaults to json in set in __construct
	private $error_messages; // validation error messages
	private $messages = array(); // form validation error messages
	
	/**
	 * constructor
	*/
	function __construct(){
		$this->db = new Database;
		$this->list_limit = '20';
		$this->responseType = 'json';
	}
	
	/**
 	*
 	* determines what to do
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
			
			case "3dkml": //report/add an incident
				$ret = $this->_3dkml();
			break;
				
			case "tagnews": //tag a news item to an incident
			
			case "tagvideo": //report/add an incident
			
			case "tagphoto": //report/add an incident
				$incidentid = '';
				
				if(!$this->_verifyArrayIndex($request, 'id')) {
					$error = array("error" => 
					$this->_getErrorMsg(001, 'id'));
					break;
				} else {
					$incidentid = $request['id'];
				}	
				
				$mediatype = 0;
	
				if($task == "tagnews") $mediatype = 4;
					
				if($task == "tagvideo") $mediatype = 2;
					
				if($task == "tagphoto") $mediatype = 1;
					
				$ret = $this->_tagMedia($incidentid, $mediatype);
				
				break;
		
			case "apikeys":
				$by = '';
				if(!$this->_verifyArrayIndex($request, 'by')) {
					$error = array("error" => 
					$this->_getErrorMsg(001, 'by'));
					break;
				}else {
					$by = $request['by'];
				}
			
				switch($by) {
					case "google":
						$ret = $this->_apiKey('api_google');
						break;

					case "yahoo":
						$ret = $this->_apiKey('api_yahoo');
						break;

					case "microsoft":
						$ret = $this->_apiKey('api_live');
						break;
					
					default:
						$error = array("error" =>$this->_getErrorMsg(002));
				}
				break;
					
			case "categories": //retrieve all categories
				$ret = $this->_categories();
				break;

			case "version": //retrieve an ushahidi instance version number
				$ret = $this->_getVersionNumber();
				break;
				
			case "category": //retrieve categories
				$id = 0;
				
				if(!$this->_verifyArrayIndex($request, 'id')){
					$error = array("error" => 
					$this->_getErrorMsg(001, 'id'));
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
					$error = array("error" => 
					$this->_getErrorMsg(001, 'by'));
					break;
				} else {
					$by = $request['by'];
				}
		
				switch ($by){
					case "latlon": //latitude and longitude
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
				/**
				* 
				* there are several ways to get incidents by
				*/
				$by = '';
				$sort = 'asc';
				$orderfield = 'incidentid';
			
				if(!$this->_verifyArrayIndex($request, 'by')){
					$error = array("error" => $this->_getErrorMsg(001, 'by'));
					break;
				} else {
					$by = $request['by'];
				}
				/*IF we have an order by, 0=default=asc 1=desc */
				if($this->_verifyArrayIndex($request, 'sort')){
					if ( $request['sort'] == '1' ){
						$sort = 'desc';
					}
				}						
			
				/* Order field  */
				if($this->_verifyArrayIndex($request, 'orderfield')){
					switch ( $request['orderfield'] ){
						case 'id':
							$orderfield = 'incidentid';
							break;
						case 'locid':
							$orderfield = 'locationid';
							break;
						case 'date':
							$orderfield = 'incidentdate';
							break;
						default:
							/* Again... it's set but let's cast it in concrete */
							$orderfield = 'incidentid';
					}

				}
				switch ($by){
					case "all": // incidents
						$ret = $this->_incidentsByAll($orderfield, $sort);
						break;
				
					case "latlon": //latitude and longitude
						if(($this->_verifyArrayIndex($request, 'latitude')) && ($this->_verifyArrayIndex($request, 'longitude'))){
							$ret = $this->_incidentsByLatLon($request['latitude'],$orderfield,$request['longitude'],$sort);
						} else {
							$error = array("error" => $this->_getErrorMsg(001,'latitude or longitude'));
						}
						break;

					case "locid": //Location Id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_incidentsByLocitionId($request['id'], $orderfield, $sort);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'id'));
						}
						break;
			
					case "locname": //Location Name
						if(($this->_verifyArrayIndex($request, 'name'))){
							$ret = $this->_incidentsByLocationName($request['name'], $orderfield, $sort);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'name'));
						}
						break;
					
					case "catid": //Category Id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_incidentsByCategoryId($request['id'], $orderfield, $sort);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'id'));
						}
						break;
			
					case "catname": //Category Name
						if(($this->_verifyArrayIndex($request, 'name'))){
							$ret = $this->_incidentsByCategoryName($request['name'], $orderfield, $sort);
						} else {
							$error = array("error" => $this->_getErrorMsg(001, 'name'));
						}
                                                break;
                                        case "sinceid": //Since Id
                                                if(($this->_verifyArrayIndex($request,'id'))){
                                                        $ret = $this->_incidentsBySinceId($request['id'], $orderfield, $sort);
                                                } else {
                                                        $error = array("error" => $this->_getErrorMsg(001,'id'));
                                                }
                                                break;

					default:
						$error = array("error" => $this->_getErrorMsg(002));
				}
			
				break;
				
				
			case "sharing": //Sharing Data based on Permissions
				if( $this->_verifyArrayIndex($request, 'sharing_key') && $this->_verifyArrayIndex($request, 'sharing_site_name') && $this->_verifyArrayIndex($request, 'sharing_email') && $this->_verifyArrayIndex($request, 'sharing_url') && $this->_verifyArrayIndex($request, 'type') && $this->_verifyArrayIndex($request, 'session') ){
					$ret = $this->_sharing($request['type'], 
					$request['session'], 
					$request['sharing_key'], 
					$request['sharing_site_name'], 
					$request['sharing_email'],
					$request['sharing_url'],
					$request['sharing_data']);
				} else {
					$error = json_encode(array("error" => $this->_getErrorMsg(001,'Authentication Credentials')));
				}
				break;
		
			case "validate": //Validate Session
				if(!$this->_verifyArrayIndex($request,'session')){
					$error = array("error" => $this->_getErrorMsg(006, 'session'));
				} else {
					$ret = $this->_validate($request['session']);
				}
				break;
				
			case "statistics":
				if(!Kohana::config('settings.allow_stat_sharing')){
					$error = array("error" => $this->_getErrorMsg(005));
				} else {
					$ret = $this->_statistics();
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
		if($this->responseType == 'xml') header("Content-type: text/xml");
		
		print $ret;
		
		//END
	}
	
	/**
 	* Makes sure the appropriate key is there in a given array (POST or GET) and that it is set
 	*/
	function _verifyArrayIndex(&$ar, $index){
		if(isset($ar[$index]) && array_key_exists($index, $ar)){
			return true;
		} else {
			return false;
		}
	}
	
	/**
 	* returns an array error - array("code" => "CODE", "message" => "MESSAGE") based on the given code
	*/
	function _getErrorMsg($errcode, $param = '', $message=''){
		switch($errcode){
			case 0:
				return array("code" => "0", "message" => "No Error.");
			case 001:
				return array("code" => "001", "message" => "Missing Parameter - $param.");
			case 002:
				return array("code" => "002", "message" => "Invalid Parameter");
			case 003:
				return array("code" => "003", "message" => $message );
			case 004:
				return array("code" => "004", "message" => "Data was not sent by post method.");
			case 005:
				return array("code" => "005", "message" => "Access denied. Either your credentials are not valid or your request has been refused. ");
			case 006:
				return array("code" => "006", "message" => "Access denied. Your request has been understood, but denied due to access limits like time. Try Back Later");			
			default:
				return array("code" => "999", "message" => "Not Found.");
		}
	}
	
	/**
 	* generic function to get incidents by given set of parameters
 	*/
	function _getIncidents($where = '', $limit = ''){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_incidents = array(); //incidents to parse to json
		
		$media_items = array(); //incident media
		$json_incident_media = array(); //incident media
		
		$retJsonOrXml = ''; //will hold the json/xml string to return
		
		$replar = array(); //assists in proper xml generation
		
		// Doing this manaully. It was wasting my time trying modularize it.
		// Will have to visit this again after a good rest. I mean a good rest.
		
		//XML elements
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('response');
		$xml->startElement('payload');
		$xml->startElement('incidents');
		
		//find incidents
		$query = "SELECT i.id AS incidentid,i.incident_title AS incidenttitle," ."i.incident_description AS incidentdescription, i.incident_date AS " ."incidentdate, i.incident_mode AS incidentmode,i.incident_active AS " ."incidentactive, i.incident_verified AS incidentverified, l.id AS " ."locationid,l.location_name AS locationname,l.latitude AS " ."locationlatitude,l.longitude AS locationlongitude FROM incident AS i " ."INNER JOIN location as l on l.id = i.location_id ".
                    "$where $limit";
 
		$items = $this->db->query($query);
		$i = 0;
		foreach ($items as $item){
			
			if($this->responseType == 'json'){
				$json_incident_media = array();
			}
			
			//build xml file
			$xml->startElement('incident');
			
			$xml->writeElement('id',$item->incidentid);
			$xml->writeElement('title',$item->incidenttitle);
			$xml->writeElement('description',$item->incidentdescription);
			$xml->writeElement('date',$item->incidentdate);
			$xml->writeElement('mode',$item->incidentmode);
			$xml->writeElement('active',$item->incidentactive);
			$xml->writeElement('verified',$item->incidentverified);
			$xml->startElement('location');
			$xml->writeElement('id',$item->locationid);
			$xml->writeElement('name',$item->locationname);
			$xml->writeElement('latitude',$item->locationlatitude);
			$xml->writeElement('longitude',$item->locationlongitude);  	
			$xml->endElement();
			$xml->startElement('categories');
			
			//fetch categories
			$query = " SELECT c.category_title AS categorytitle, c.id AS cid " .
					"FROM category AS c INNER JOIN incident_category AS ic ON " .
					"ic.category_id = c.id WHERE ic.incident_id =".$item->incidentid." LIMIT 0 , 20";
			$category_items = $this->db->query( $query );
			
			foreach( $category_items as $category_item ){
				$xml->startElement('category');
				$xml->writeElement('id',$category_item->cid);
				$xml->writeElement('title',$category_item->categorytitle );
				$xml->endElement();
				
			}
			$xml->endElement();//end categories
			
			//fetch media associated with an incident
			$query = "SELECT m.id as mediaid, m.media_title AS mediatitle, " .
					"m.media_type AS mediatype, m.media_link AS medialink, " .
					"m.media_thumb AS mediathumb FROM media AS m " .
					"INNER JOIN incident AS i ON i.id = m.incident_id " .
					"WHERE i.id =". $item->incidentid."  LIMIT 0 , 20";
			
			$media_items = $this->db->query($query);
			
			if(count($media_items) > 0){
				$xml->startElement('mediaItems');
				foreach ($media_items as $media_item){
					if($this->responseType == 'json'){
						$json_incident_media[] = $media_item;
					} else {
						$xml->startElement('media');
						if( $media_item->mediaid != "" ) $xml->writeElement('id',$media_item->mediaid);	
						if( $media_item->mediatitle != "" ) $xml->writeElement('title',$media_item->mediatitle); 		
						if( $media_item->mediatype != "" ) $xml->writeElement('type',$media_item->mediatype);
						if( $media_item->medialink != "" ) $xml->writeElement('link',$media_item->medialink);
						if( $media_item->mediathumb != "" ) $xml->writeElement('thumb',$media_item->mediathumb);
						$xml->endElement();
					}
				}
				$xml->endElement(); // media
				
			}
			$xml->endElement(); // end incident
			
			//needs different treatment depending on the output
			if($this->responseType == 'json'){
				$json_incidents[] = array("incident" => $item, "media" => $json_incident_media);
			}
			
		}
		
		//create the json array
		$data = array(
			"payload" => array("incidents" => $json_incidents),
			"error" => $this->_getErrorMsg(0)
		);
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($data);
			return $retJsonOrXml;
		} else {
			$xml->endElement(); //end incidents
			$xml->endElement(); // end payload
			$xml->startElement('error');
			$xml->writeElement('code',0);
			$xml->writeElement('message','No Error');
			$xml->endElement();//end error
			$xml->endElement(); // end response
			return $xml->outputMemory(true);
		}
		
		//return $retJsonOrXml;
	}
	
	/**
	* return KML for 3d "geo spatial temporal" map
	* FIXME: This could probably be done in less than >5 foreach loops
	*/
	function _3dkml(){
		$kml = '<?xml version="1.0" encoding="UTF-8"?>
		<kml xmlns="http://earth.google.com/kml/2.2">
		<Document>
		<name>Ushahidi</name>'."\n";
		
		// Get the categories that each incident belongs to
		$incident_categories = $this->_incidentCategories();
		
		// Get category colors in this format: $category_colors[id] = color
		$categories = json_decode($this->_categories());
		$categories = $categories->payload->categories;
		$category_colors = array();
		foreach($categories as $category) {
			$category_colors[$category->category->id] = $category->category->color;
		}
		
		// Finally, grab the incidents
		$incidents = json_decode($this->_getIncidents('WHERE incident_active=1'));
		$incidents = $incidents->payload->incidents;
		
		// Calculate times for relative altitudes (This is the whole idea behind 3D maps)
		$incident_times = array();
		foreach($incidents as $inc_obj) {
			$incident = $inc_obj->incident;
			$incident_times[$incident->incidentid] = strtotime($incident->incidentdate);
		}
		
		// All times to be adjusted according to max altitude.
		
		$max_altitude = 10000;
		$newest = 0;
		foreach($incident_times as $incident_id => $timestamp) {
			if(!isset($oldest)) $oldest = $timestamp;
			$incident_times[$incident_id] -= $oldest;
			if($newest < $incident_times[$incident_id]) $newest = $incident_times[$incident_id];
		}
		
		foreach($incident_times as $incident_id => $timestamp) {
			$incident_altitude[$incident_id] = 0;
			if($newest != 0) $incident_altitude[$incident_id] = floor(($timestamp / $newest) * $max_altitude);
		}
		
		// Compile KML and output
		foreach($incidents as $inc_obj) {
			
			$incident = $inc_obj->incident;
			
			$category_id = $incident_categories[$incident->incidentid][0]; // Could be multiple categories. Pick the first one.
			$hex_color = $category_colors[$category_id];
			// Color for KML is not the traditional HTML Hex of (rrggbb). It's (aabbggrr). aa = alpha or transparency
			$color = 'FF'.$hex_color{4}.$hex_color{5}.$hex_color{2}.$hex_color{3}.$hex_color{0}.$hex_color{1};
			
			$kml .= '<Placemark>
				<name>'.$incident->incidenttitle.'</name>
				<description>'.$incident->incidentdescription.'</description>
				<Style>
					<IconStyle>
						<Icon>
							<href>'.url::base().'media/img/color_icon.php?c='.$hex_color.'</href>
						</Icon>
					</IconStyle>
					<LineStyle>
						<color>'.$color.'</color>
						<width>2</width>
					</LineStyle>
				</Style>
				<Point>
					<extrude>1</extrude>
					<altitudeMode>relativeToGround</altitudeMode>
					<coordinates>'.$incident->locationlongitude.','.$incident->locationlatitude.','.$incident_altitude[$incident->incidentid].'</coordinates>
				</Point>
			</Placemark>'."\n";
			
		}
		
		$kml .= '</Document>
		</kml>';
		
		return $kml;
	}
	
	/**
 	* report an incident
 	*/
	function _report(){
		$retJsonOrXml = array();
		$reponse = array();
		$ret_value = $this->_submit();	
		if($ret_value == 0 ){
			$reponse = array(
				"payload" => array("success" => "true"),
				"error" => $this->_getErrorMsg(0)
			);
			//return;
			//return $this->_incidentById();
		} else if( $ret_value == 1 ) {
			$reponse = array(
				"payload" => array("success" => "false"),
				"error" => $this->_getErrorMsg(003,'',$this->error_messages)
			);
		} else {
			$reponse = array(
				"payload" => array("success" => "false"),
				"error" => $this->_getErrorMsg(004)
			);
		}
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($reponse);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($reponse, array());
		}
		
		return $retJsonOrXml;
	}
	
	/**
 	* the actual reporting - ***must find a cleaner way to do this than duplicating code verbatim - modify report***
 	*/
	function _submit() {		
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
		//copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$this->messages = $form;		
		// check, has the form been submitted, if so, setup validation
		if ($_POST) {
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
			
			if($this->_verifyArrayIndex($_POST, 'incident_ampm')) {
				if ($_POST['incident_ampm'] != "am" && $_POST['incident_ampm'] != "pm") {
					$post->add_error('incident_ampm','values');
				}
			}
			
			$post->add_rules('latitude','required','between[-90,90]');	// Validate for maximum and minimum latitude values
			$post->add_rules('longitude','required','between[-180,180]');// Validate for maximum and minimum longitude values
			$post->add_rules('location_name','required', 'length[3,200]');
			$post->add_rules('incident_category','required','length[1,100]');
			
			// Validate Personal Information
			if (!empty($post->person_first)) {
				$post->add_rules('person_first', 'length[3,100]');
			}
			
			if (!empty($post->person_last)) {
				$post->add_rules('person_last', 'length[3,100]');
			}
			
			if (!empty($post->person_email)) {
				$post->add_rules('person_email', 'email', 'length[3,100]');
			}
			
			// Test to see if things passed the rule checks
			if ($post->validate()) {
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
				$incident_date=$incident_date[2]."-".$incident_date[0]."-".$incident_date[1];
					
				$incident_time = $post->incident_hour . ":" . $post->incident_minute . ":00 " . $post->incident_ampm;
				$incident->incident_date = $incident_date . " " . $incident_time;
				$incident->incident_dateadd = date("Y-m-d H:i:s",time());
				$incident->save();
				
				// SAVE CATEGORIES
				//check if data is csv or a single value.
				$pos = strpos($post->incident_category,",");
				if( $pos === false ) {
					//for backward compactibility. will drop support for it in the future. 
					if( @unserialize( $post->incident_category) ) { 
						$categories = unserialize( $post->incident_category);
					} else {
						$categories = array( $post->incident_category );
					}
				} else { 
					$categories = explode(",",$post->incident_category);	
				}
	 
				if(!empty($categories) && is_array($categories)) {
					foreach($categories as $item){
						$incident_category = new Incident_Category_Model();
						$incident_category->incident_id = $incident->id;
						$incident_category->category_id = $item;
						$incident_category->save();
					}
				}
				
				// STEP 4: SAVE MEDIA
				// a. News
				if(!empty( $post->incident_news ) && is_array($post->incident_news)) { 
					foreach($post->incident_news as $item) {
						if(!empty($item)) {
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
				if( !empty( $post->incident_video) && is_array( $post->incident_video)){ 

					foreach($post->incident_video as $item) {
						if(!empty($item)) {
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
				if( !empty($post->incident_photo)){
					$filenames = upload::save('incident_photo');
					$i = 1;
					foreach ($filenames as $filename) {
						$new_filename = $incident->id . "_" . $i . "_" . time();
					
						// Resize original file... make sure its max 408px wide
						Image::factory($filename)->resize(408,248,Image::AUTO)->save(Kohana::config('upload.directory', TRUE) . $new_filename . ".jpg");
					
						// Create thumbnail
						Image::factory($filename)->resize(70,41,Image::HEIGHT)->save(Kohana::config('upload.directory', TRUE) . $new_filename . "_t.jpg");
					
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
				if(!empty($post->person_first) || !empty($post->person_last)){
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
				
			} else { // No! We have validation errors, we need to show the form again, with the errors
				// populate the error fields, if any
				$this->messages = arr::overwrite($this->messages, $post->errors('report'));

				foreach ($this->messages as $error_item => $error_description) {
					if( !is_array( $error_description ) ) {
						$this->error_messages .= $error_description;
						if( $error_description != end( $this->messages ) ) {
							$this->error_messages .= " - ";
   						}
  					}
				}
								
			//FAILED!!!
			return 1; //validation error
	  		}
		} else {
			return 2; // Not sent by post method.
		}
	}
	
	/**
 	* Tag a news item to an incident
 	*/
	function _tagMedia($incidentid, $mediatype) {
		if ($_POST) {
			//get the locationid for the incidentid
			$locationid = 0;
			
			$query = "SELECT location_id FROM incident WHERE id=$incidentid";
			
			$items = $this->db->query($query);
			if(count($items) > 0){
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
						$err = array("error" => $this->_getErrorMsg(001,'url'));
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
			$ret = array("payload" => array("success" => "true"),"error" => $this->_getErrorMsg(0));
			
			if($this->responseType == 'json'){
				return json_encode($ret);
			} else {
			return $this->_arrayAsXML($ret, array());
			}
		} else {
			if($this->responseType == 'json'){
				return json_encode(array("error" => $this->_getErrorMsg(003)));
			} else {
				$err = array("error" => $this->_getErrorMsg(003));
			return $this->_arrayAsXML($err, array());
			}
		}
	}
	
	/**
 	* get a list of categories
	*/
	function _categories(){

		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_categories = array(); //incidents to parse to json
		
		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$query = "SELECT id, category_title AS title, category_description AS 
				description, category_color AS color FROM `category` WHERE 
				category_visible = 1 ORDER BY id DESC";

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
	
	/**
 	* get a single category
 	*/
	function _category($id){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_categories = array(); //incidents to parse to json
		
		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$query = "SELECT id, category_title, category_description, 
				category_color FROM `category` WHERE category_visible = 1 
				AND id=$id ORDER BY id DESC";

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
	
	/**
	* get a list of incident categories
	* returns an array
	* FIXME: Might as well add functionality to return this in the API
	*
	* Return format: array[incident_id][] = category_id;
	*
	*/
	function _incidentCategories(){
		$query = "SELECT incident_id, category_id FROM `incident_category` ORDER BY id DESC";
		$items = $this->db->query($query);
		$data = array();
		foreach ($items as $item){
			$data[$item->incident_id][] = $item->category_id;
		}
		return $data;
	}
	
	/**
 	* get a list of locations
 	*/
	function _getLocations($where = '', $limit = ''){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_locations = array(); //incidents to parse to json
		
		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$query = "SELECT id, location_name AS name, country_id , latitude, 
				longitude FROM `location` $where $limit ";

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

	/**
 	* get api keys
 	*/
	function _apiKey($service){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_apikey = array(); //api string to parse to json	
		$retJsonOrXml = ''; //will hold the json/xml string to return

		//find incidents
		$query = "SELECT id AS id, $service AS apikey FROM `settings`
			ORDER BY id DESC ;";

		$items = $this->db->query($query);
		$i = 0;
		
		$replar = array(); //assists in proper xml generation
		
		foreach ($items as $item){
			//needs different treatment depending on the output
			if($this->responseType == 'json'){
				$json_services[] = array("service" => $item);
			} else {
				$json_services['service'.$i] = array("service" => $item) ;
				$replar[] = 'service'.$i;
			}
			
			$i++;
		}
		
		//create the json array
		$data = array("payload" => array("services" => $json_services),"error" => $this->_getErrorMsg(0));
		
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($data);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($data, $replar);
		}

		return $retJsonOrXml;
	}

	/**
 	* get an ushahidi instance version number
 	*/
	function _getVersionNumber(){
		$data = array();
		$json_version = array();
		$retJsonOrXml = '';
		$version = Kohana::config('version.ushahidi_version');

		if($this->responseType == 'json'){
			$json_version[] = array("version" => $version);
		}else{
			$json_version['version0'] = array("version" => $version);
			$replar[] = 'version0';
		}

		//create the json array
		$data = array("payload" => array("version" => $json_version),"error" => $this->_getErrorMsg(0));

		if($this->responseType = 'json') {
			$retJsonOrXml = $this->_arrayAsJSON($data);
		}else{
			$retJsonOrXml = $this->_arrayAsXML($data,$replar);
		}
		
		return $retJsonOrXml;
	}
	
	/**
 	* get a single location by id
 	*/
	function _locations(){
		$where = "\n WHERE location_visible = 1 ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getLocations($where, $limit);
	}
	
	/**
 	* get a single location by id
 	*/
	function _locationById($id) {
		$where = "\n WHERE location_visible = 1 AND id=$id ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getLocations($where, $limit);
	}
	
	/**
 	* get a single location by id
 	*/
	function _locationByCountryId($id){
		$where = "\n WHERE location_visible = 1 AND country_id=$idi ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getLocations($where, $limit);
	}	
	
	/**
 	* country query abstraction
 	*/
	function _getCountries($where = '', $limit = ''){
		$items = array(); //will hold the items from the query
		$data = array(); //items to parse to json
		$json_countries = array(); //incidents to parse to json
			
		$retJsonOrXml = ''; //will hold the json/xml string to return
	
		//find incidents
		$query = "SELECT id, iso, country as `name`, capital 
			FROM `country` $where $limit";
	
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
		$data = array("payload" => array("countries" => $json_countries),"error" => $this->_getErrorMsg(0));
			
		if($this->responseType == 'json'){
			$retJsonOrXml = $this->_arrayAsJSON($data);
		} else {
			$retJsonOrXml = $this->_arrayAsXML($data, $replar);
		}
	
		return $retJsonOrXml;
	}
	
	/**
 	* get a list of countries
 	*/
	function _countries(){
		$where = "ORDER by id DESC ";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getCountries($where, $limit);
	}
	
	/**
 	* get a country by name
 	*/
	function _countryByName($name){
		$where = "\n WHERE country = '$name' ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getCountries($where, $limit);
	}
	
	/**
 	* get a country by id
 	*/
	function _countryById($id){
		$where = "\n WHERE id=$id ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getCountries($where, $limit);
	}
	
	/**
 	* get a country by iso
 	*/
	function _countryByIso($iso){
		$where = "\n WHERE iso='$iso' ";
		$where .= "ORDER by id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getCountries($where, $limit);
	}
	
	/**
 	* Fetch all incidents
 	*/
	function _incidentsByAll($orderfield,$sort) {
		$where = "\nWHERE i.incident_active = 1 ";
		$sortby = "\nORDER BY i.id DESC";
		$limit = "\nLIMIT 0, $this->list_limit";
		/* Not elegant but works */
		return $this->_getIncidents($where.$sortby, $limit);
	}
	
	/**
 	* get incident by id
 	*/
	function _incidentById($id){
		$where = "\nWHERE i.id = $id AND i.incident_active = 1 ";
		$where .= "ORDER BY i.id DESC ";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where, $limit);
	}
	
	/**
 	* get the incidents by latitude and longitude.
 	* TODO // write necessary codes to achieve this.
 	*/
	function _incidentsByLatLon($lat, $orderfield,$long,$sort){
		$where = "\nWHERE l.latitude = $lat AND l.longitude = $long AND i.incident_active = 1 ";
		$sortby = "\nORDER BY $orderfield $sort ";
		$limit = "\n LMIT 0, $this->list_limit";
		return $this->_getIncidents($where,$sortby,$limit);		
	}
	
	/**
 	* get the incidents by location id
 	*/
	function _incidentsByLocitionId($locid,$orderfield,$sort){
		$where = "\nWHERE i.location_id = $locid AND i.incident_active = 1 ";
		$sortby = "\nORDER BY $orderfield $sort";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where.$sortby, $limit);
	}
	
	/**
 	* get the incidents by location name
 	*/
	function _incidentsByLocationName($locname,$orderfield,$sort){
		$where = "\nWHERE l.location_name = '$locname' AND 
				i.incident_active = 1 ";
		$sortby = "\nORDER BY $orderfield $sort";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where.$sortby, $limit);
	}
	
	/**
 	* get the incidents by category id
 	*/
	function _incidentsByCategoryId($catid,$orderfield,$sort){
		// Needs Extra Join
		$join = "\nINNER JOIN incident_category AS ic ON ic.incident_id = i.id"; 
		$join .= "\nINNER JOIN category AS c ON c.id = ic.category_id ";
		$where = $join."\nWHERE c.id = $catid AND i.incident_active = 1";
		$sortby = "\nORDER BY $orderfield $sort";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where.$sortby, $limit);
	}
	
	/**
 	* get the incidents by category name
 	*/
	function _incidentsByCategoryName($catname,$orderfield,$sort){
		// Needs Extra Join
		$join = "\nINNER JOIN incident_category AS ic ON ic.incident_id = i.id"; 
		$join .= "\nINNER JOIN category AS c ON c.id = ic.category_id";
		$where = $join."\nWHERE c.category_title = '$catname' AND 
				i.incident_active = 1";
		$sortby = "\nORDER BY $orderfield $sort";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where.$sortby, $limit);
        }

        /**
         * get the incidents by since an incidents was updated
         */
        function _incidentsBySinceId($since_id,$orderfield,$sort){
                // Needs Extra Join
		$join = "\nINNER JOIN incident_category AS ic ON ic.incident_id = i.id"; 
		$join .= "\nINNER JOIN category AS c ON c.id = ic.category_id";
		$where = $join."\nWHERE i.id > $since_id AND 
				i.incident_active = 1";
		$sortby = "\nORDER BY $orderfield $sort";
		$limit = "\nLIMIT 0, $this->list_limit";
		return $this->_getIncidents($where.$sortby, $limit);

        }
	
	/**
 	* Instance to Instance Sharing of Data
 	* Access Limits: Hourly
 	*/
	function _sharing($request_type, $sharing_session, $sharing_key, $sharing_site_name, 
		$sharing_email, $sharing_url, $sharing_data = ""){
		$sharing = new Sharing();	// New Sharing Object
		switch($request_type){
			case "notify": 		// Handle New Share Request
				$return_array = $sharing->share_edit($sharing_session, $sharing_key, $sharing_site_name, 
				$sharing_email, $sharing_url);
				if ( $return_array["success"] === TRUE ){
					$data = array("payload" => array("success" => "true"),"error" => $this->_getErrorMsg(0));
				} else {
					$data = array("payload" => array("success" => "false"),"error" => $this->_getErrorMsg(003, '', $return_array["debug"]));	// Request Failed
				}
				break;
					
			case "request": 	// Handle Request For Data
				$return_array = $sharing->share_send($sharing_session, $sharing_key, $sharing_site_name, $sharing_email, $sharing_url);
				if ( $return_array["success"] === TRUE ){
					$data = array("payload" => array("success" => "true"),"error" => $this->_getErrorMsg(0));
				} else {
					$data = array("payload" => array("success" => "false"),"error" => $this->_getErrorMsg(003, '', $return_array["debug"]));	// Request Failed
				}
				break;
					
			case "incoming": 	// Handle Incoming Data
				$return_array = $sharing->share_incoming($sharing_session, $sharing_key, $sharing_site_name, 
				$sharing_email, $sharing_url, $sharing_data);			
				if ( $return_array["success"] === TRUE ){
					$data = array("payload" => array("success" => "true"),"error" => $this->_getErrorMsg(0));
				} else {
					$data = array("payload" => array("success" => "false"),"error" => $this->_getErrorMsg(003, '', $return_array["debug"]));	// Request Failed
				}
				break;				
					
				default:
					$data = array("payload" => array("success" => "false"),"error" => $this->_getErrorMsg(002));	// Invalid Request	
		}
		return $this->_arrayAsJSON($data);
	}
	
	
	/**
 	* Validate Session ID against URL that sent it
 	*/
	function _validate($session){
		$sharing = new Sharing();
		if ($sharing->share_validate($session)){
			$data = array("payload" => array("success" => "true"),"error" => $this->_getErrorMsg(0));
		} else {
			$data = array("payload" => array("success" => "false"),"error" => $this->_getErrorMsg(005));	// Request Failed
		}
		return $this->_arrayAsJSON($data);
	}
	
	/**
 	* Provide statistics for the instance
 	*/
	function _statistics(){
		
		$messages_total = 0;
		$messages_services = array();
		$services = ORM::factory('service')->find_all();
		foreach ($services as $service) {
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
		$incidents_unapproved = ORM::factory('incident')->where('incident_active', '0')->count_all();
		$incidents_approved = $incidents_total - $incidents_unapproved;
		$incomingmedia_total = ORM::factory('feed_item')->count_all();
		$categories_total = ORM::factory('category')->count_all();
		$locations_total = ORM::factory('location')->count_all();
		
		//print_r($messages_services);
		
		$data = array(
			'incidents'=>array(
				'total'=>$incidents_total,
				'approved'=>$incidents_approved,
				'unapproved'=>$incidents_unapproved
			),
			'incoming_media'=>array(
				'total_feed_items'=>$incomingmedia_total
			),
			'categories'=>array(
				'total'=>$categories_total
			),
			'locations'=>array(
				'total'=>$locations_total
			),
			'messages'=>$messages_stats,
			
		
		);
		
		if($this->responseType == 'json'){
			return $this->_arrayAsJSON($data);
		} else {
			return $this->_arrayAsXML($data);
		}
	}
	
	/**
 	* starting point
 	*/
	public function index(){
		//switch task
		$this->switchTask();
	}
	
	/**
 	* Creates a JSON response given an array
 	*/
	function _arrayAsJSON($data){
		return json_encode($data);
	}
	
	/**
 	* converts an object to an array
 	*/
	function _object2array($object) {
		if (is_object($object)) {
			foreach ($object as $key => $value) {
				$array[$key] = $value;
			}
		} else {
			$array = $object;
		}
		return $array;
	}
	
	/**
 	* Creates a XML response given an array
 	* CREDIT TO: http://snippets.dzone.com/posts/show/3391
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
 						
			$xml->writeElement($key, $value);
 		}	
	}
	
	/**
 	* Creates a XML response given an array
 	* CREDIT TO: http://snippets.dzone.com/posts/show/3391
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
