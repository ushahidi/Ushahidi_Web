<?php
class Api_Controller extends Controller {
	var $db; //Database instance for queries
	var $list_limit; //number of records to limit response to - set in __construct
	var $responseType; //type of response, either json or xml as specified, defaults to json in set in __construct
	
	/*
	determines what to do
	*/
	function switchTask(){
		$task = ''; //holds the task to perform as requested
		$ret = ''; //return value
		$request = array();
		
		//determine if we are using GET or POST
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$request =& $_GET;
		} else {
			$request =& $_POST;
		}
		
		//make sure we have a task to work with
		if(!$this->_verifyArrayIndex($request, 'task')){
			echo json_encode(array("error" => $this->_getErrorMsg(001, 'task')));
			return;
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
			case "incidents":
				/*
				there are several ways to get incidents by
				*/
				$by = '';
				
				if(!$this->_verifyArrayIndex($request, 'by')){
					$ret = json_encode(array("error" => $this->_getErrorMsg(001, 'by')));
					break;
				} else {
					$by = $request['by'];
				}
				
				switch ($by){
					case "latlon": //latitude and longitude
						if(($this->_verifyArrayIndex($request, 'latitude')) && ($this->_verifyArrayIndex($request, 'longitude'))){
							$ret = $this->_incidentsByLatLon($request['latitude'], $request['longitude']);
						} else {
							$ret = json_encode(array("error" => $this->_getErrorMsg(001, 'latitude or longitude')));
						}
						break;
					case "address": //address
						if(($this->_verifyArrayIndex($request, 'address'))){
							$ret = $this->_incidentsByAddress($request['address']);
						} else {
							$ret = json_encode(array("error" => $this->_getErrorMsg(001, 'address')));
						}
						break;
					case "locid": //Location Id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_incidentsByLocitionId($request['id']);
						} else {
							$ret = json_encode(array("error" => $this->_getErrorMsg(001, 'id')));
						}
						break;
					case "locname": //Location Name
						if(($this->_verifyArrayIndex($request, 'name'))){
							$ret = $this->_incidentsByLocationName($request['name']);
						} else {
							$ret = json_encode(array("error" => $this->_getErrorMsg(001, 'name')));
						}
						break;
					case "catid": //Category Id
						if(($this->_verifyArrayIndex($request, 'id'))){
							$ret = $this->_incidentsByCategoryId($request['id']);
						} else {
							$ret = json_encode(array("error" => $this->_getErrorMsg(001, 'id')));
						}
						break;
					case "catname": //Category Name
						if(($this->_verifyArrayIndex($request, 'name'))){
							$ret = $this->_incidentsByCategoryName($request['name']);
						} else {
							$ret = json_encode(array("error" => $this->_getErrorMsg(001, 'name')));
						}
						break;
					default:
						$ret = json_encode(array("error" => $this->_getErrorMsg(002)));
				}
				
				break;
			default:
				$ret = json_encode(array("error" => $this->_getErrorMsg(999)));
				break;
		}
		
		//create the response depending on the kind that was requested
		
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

		//find incidents
		$query = "SELECT i.id AS incidentid,l.id AS locationid, l.location_name AS locationname, 
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
				//, "media" => $json_incident_media
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
			$retJsonOrXml = $this->_arrayAsXML($data);
		}
		
		return $retJsonOrXml;
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
	function _write(XMLWriter $xml, $data){
		foreach($data as $key => $value){
			if(is_a($value, 'stdClass')){
				//echo 'convert to an array';
				$value = $this->_object2array($value);
			}
			
         	if(is_array($value)){
	            $xml->startElement($key);
              	$this->_write($xml, $value);
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
	function _arrayAsXML($data){
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('response');

		$this->_write($xml, $data);

		$xml->endElement();
	  	return $xml->outputMemory(true);
	}

}
?>