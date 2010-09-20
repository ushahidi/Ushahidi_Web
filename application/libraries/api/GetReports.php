<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles reports activities via the API.
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

class GetReports
{
    private $json_reports; // Hold items from sql query.
    private $data; // items to parse to JSON.
    private $items; // categories to parse to JSON.
    private $query; // Holds the SQL query
    private $replar; // assists in proper XML generation.
    private $db;
    private $domain;
    private $table_prefix;
    private $list_limit;
    private $api_actions;
    private $json_report_media;
    private $media_items;
    private $json_report_categories;
    private $get_categories;

    public function __construct()
    {
        $this->api_actions = new ApiActions;
        $this->get_categories = new GetCategories;
        $this->json_reports = array();
        $this->json_report_media = array();
        $this->json_report_categories = array();
        $this->media_items = array();
        $this->data = array();
        $this->items = array();
        $this->ret_json_or_xml = '';
        $this->query = '';
        $this->replar = array();
        $this->db = $this->api_actions->_get_db();
        $this->domain = $this->api_actions->_get_domain();
        $this->list_limit = $this->api_actions->_get_list_limit();
    }

    /**
 	 * Generic function to get reports by given set of parameters
 	 */
	public function _get_reports($where = '',$limit = '',$response_type)
    {
	  
		//XML elements
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('response');
		$xml->startElement('payload');
		$xml->writeElement('domain',$this->domain);
		$xml->startElement('incidents');

		//find incidents
		$this->query = "SELECT i.id AS incidentid,
                i.incident_title AS incidenttitle,"
                ."i.incident_description AS incidentdescription, "
				."i.incident_date AS incidentdate, "
				."i.incident_mode AS incidentmode, "
				."i.incident_active AS incidentactive, "
				."i.incident_verified AS incidentverified, "
				."l.id AS locationid, "
				."l.location_name AS locationname, "
				."l.latitude AS locationlatitude, "
				."l.longitude AS locationlongitude "
				."FROM ".$this->table_prefix."incident AS i "
                ."INNER JOIN ".$this->table_prefix.
                "location as l on l.id = i.location_id "."$where $limit";

		$this->items = $this->db->query($this->query);

		$i = 0;

		foreach ($this->items as $item)
        {

			if($response_type == 'json')
			{
				$this->json_report_media = array();
				$this->json_report_categories = array();
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
			$this->query = " SELECT c.category_title AS categorytitle, 
                c.id AS cid " . "FROM ".$this->table_prefix.
                "category AS c INNER JOIN ".
                $this->table_prefix."incident_category AS ic ON " .
				"ic.category_id = c.id WHERE ic.incident_id =".
                $item->incidentid;

			$category_items = $this->db->query( $this->query );

			foreach( $category_items as $category_item )
			{
				if($response_type == 'json')
                {
					$this->json_reports_categories[] = array(
                            "category"=> array(
							"id" => $category_item->cid,
							"title" => $category_item->categorytitle
						));
				} 
                else 
                {
					$xml->startElement('category');
					$xml->writeElement('id',$category_item->cid);
					$xml->writeElement('title',
                            $category_item->categorytitle );
					$xml->endElement();
				}
			}

			$xml->endElement();//end categories

			//fetch media associated with an incident
			$this->query = "SELECT m.id as mediaid, m.media_title AS 
                mediatitle, " .
				"m.media_type AS mediatype, m.media_link AS medialink, " .
				"m.media_thumb AS mediathumb FROM ".$this->table_prefix.
                "media AS m " . "INNER JOIN ".$this->table_prefix.
                "incident AS i ON i.id = m.incident_id " .
				"WHERE i.id =". $item->incidentid;

			$this->media_items = $this->db->query($this->query);

			if(count($this->media_items) > 0)
            {
				$xml->startElement('mediaItems');
				foreach ($this->media_items as $media_item)
                {
					if($this->response_type == 'json')
                    {
						$this->json_incident_media[] = array(
							"id" => $media_item->mediaid,
							"type" => $media_item->mediatype,
							"link" => $media_item->medialink
					    );
					} 
                    else 
                    {
					    $xml->startElement('media');
						
                        if( $media_item->mediaid != "" )
                        {
                            $xml->writeElement('id',$media_item->mediaid);
                        }

						if( $media_item->mediatitle != "" )
                        {
                            $xml->writeElement('title',
                                $media_item->mediatitle);
                        }

						if( $media_item->mediatype != "" )
                        {
                            $xml->writeElement('type',
                                $media_item->mediatype);
                        }

						if( $media_item->medialink != "" ) 
                        {
                            $xml->writeElement('link',
                                $media_item->medialink);
                        }

						if( $media_item->mediathumb != "" ) 
                        {
                            $xml->writeElement('thumb',
                                $media_item->mediathumb);
                        }

						$xml->endElement();
					}
				}

				$xml->endElement(); // media

			}

			$xml->endElement(); // end incident

			//needs different treatment depending on the output
			if($response_type == 'json')
			{
				$this->json_reports[] = array(
                    "incident" => $item, 
					"categories" => $this->json_report_categories, 
                    "media" => $this->json_report_media);
			}

		}

		//create the json array
		$this->data = array(
			"payload" => array(
            "domain" => $this->domain,
            "incidents" => $this->json_reports),
			"error" => $this->api_actions->_get_error_msg(0)
		);

		if($response_type == 'json')
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_JSON($this->data);

			return $this->ret_json_or_xml;
		} 
        else 
        {
			$xml->endElement(); //end incidents
			$xml->endElement(); // end payload
			$xml->startElement('error');
			$xml->writeElement('code',0);
			$xml->writeElement('message','No Error');
			$xml->endElement();//end error
			$xml->endElement(); // end response
			return $xml->outputMemory(true);
		}

	}

    /**
 	 * Fetch all incidents
     * 
     * @param string orderfield - the order in which to order query output
     * @param string sort
 	 */
	public function _reports_by_all($orderfield,$sort,
            $limit,$response_type) 
    {

		$where = "\nWHERE i.incident_active = 1 ";
		
        $sortby = "\nGROUP BY i.id ORDER BY $orderfield $sort";
		
        $limit = "\nLIMIT 0, $limit";

		/* Not elegant but works */
		return $this->_get_reports($where.$sortby, $limit,$response_type);
	}

	/**
 	* get incident by id
 	*/
	public function _reports_by_id($id,$response_type)
    {
		$where = "\nWHERE i.id = $id AND i.incident_active = 1 ";
		
        $where .= "ORDER BY i.id DESC ";
		
        $limit = "\nLIMIT 0, $this->list_limit";
		
        return $this->_get_reports($where, $limit,$response_type);
	}

	/**
 	* Get the incidents by latitude and longitude.
 	* TODO // write necessary codes to achieve this.
 	*/
	public function _reports_by_lat_lon($lat, $orderfield,$long,$sort,
            $response_type)
    {
		
        $where = "\nWHERE l.latitude = $lat AND l.longitude = $long AND 
            i.incident_active = 1 ";
		
        $sortby = "\nORDER BY $orderfield $sort ";
		
        $limit = "\n LIMIT 0, $this->list_limit";
		
        return $this->_get_reports($where,$sortby,$limit,$response_type);
	}

	/**
 	* get the incidents by location id
 	*/
	public function _reports_by_location_id($locid,$orderfield,$sort,
            $response_type )
    {
		$where = "\nWHERE i.location_id = $locid AND i.incident_active = 1 ";
		
        $sortby = "\nGROUP BY i.id ORDER BY $orderfield $sort";
		
        $limit = "\nLIMIT 0, $this->list_limit";
		
        return $this->_get_reports($where.$sortby, $limit,$response_type);
	}

	/**
 	 * get the incidents by location name
 	 */
	public function _reports_by_location_name($locname,$orderfield,$sort,
            $response_type){
		
        $where = "\nWHERE l.location_name = '$locname' AND
				i.incident_active = 1 ";
		
        $sortby = "\nGROUP BY i.id ORDER BY $orderfield $sort";
		
        $limit = "\nLIMIT 0, $this->list_limit";
		
        return $this->_get_reports($where.$sortby, $limit,$response_type);
	}

	/**
 	 * get the incidents by category id
 	 */
	public function _reports_by_category_id($catid,$orderfield,$sort,
            $response_type)
    {
		// Needs Extra Join
		$join = "\nINNER JOIN ".$this->table_prefix."incident_category AS 
            ic ON ic.incident_id = i.id";
		
        $join .= "\nINNER JOIN ".$this->table_prefix."category AS c ON 
            c.id = ic.category_id ";
		
        $where = $join."\nWHERE c.id = $catid AND i.incident_active = 1";
		
        $sortby = "\nORDER BY $orderfield $sort";
		
        $limit = "\nLIMIT 0, $this->list_limit";
		
        return $this->_get_reports($where.$sortby, $limit,$response_type);
	}

	/**
 	 * get the incidents by category name
 	 */
	public function _reports_by_category_name($catname,$orderfield,$sort,
            $response_type)
    {
		// Needs Extra Join
		$join = "\nINNER JOIN ".$this->table_prefix."incident_category AS 
            ic ON ic.incident_id = i.id";

		$join .= "\nINNER JOIN ".$this->table_prefix."category AS c ON 
            c.id = ic.category_id";
		
        $where = $join."\nWHERE c.category_title = '$catname' AND
				i.incident_active = 1";
		
        $sortby = "\nORDER BY $orderfield $sort";
		
        $limit = "\nLIMIT 0, $this->list_limit";
		
        return $this->_get_reports($where.$sortby, $limit,$response_type);
    }

    /**
     * get the incidents by since an incidents was updated
     */
    public function _reports_by_since_id($since_id,$orderfield,$sort,
            $response_type)
    {
                // Needs Extra Join
		$join = "\nINNER JOIN ".$this->table_prefix."incident_category AS 
            ic ON ic.incident_id = i.id";
		$join .= "\nINNER JOIN ".$this->table_prefix.
            "category AS c ON c.id = ic.category_id";
		$where = $join."\nWHERE i.id > $since_id AND
				i.incident_active = 1";
		$sortby = "\nGROUP BY i.id ORDER BY $orderfield $sort";
		$limit = "\nLIMIT 0, $this->list_limit";
		
        return $this->_get_reports($where.$sortby, $limit,$response_type);

    }

    /**
	 * Gets the number of approved reports
     * 
     * @param string response_type - XML or JSON
     *
     * @return string
	 */
	public function _get_report_count($response_type)
    {
	
		$json_count = array();
		$this->query = 'SELECT COUNT(*) as count FROM '.
            $this->table_prefix.
            'incident WHERE incident_active = 1';

		$this->items = $this->db->query($this->query);

		foreach ($this->items as $item)
        {
			$count = $item->count;
			break;
		}

		if($response_type == 'json')
        {
			$json_count[] = array("count" => $count);
		}
        else
        {
			$json_count['count'] = array("count" => $count);
			$this->replar[] = 'count';
		}

		//create the json array
		$this->data = array("payload" => array(
                "domain" => $this->domain,
                "count" => $json_count),
                "error" => $this->api_actions->_get_error_msg(0));

		if($response_type == 'json') 
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_JSON($this->data);
		}
        else
        {
			$this->ret_json_or_xml = $this->api_actions
                ->_array_as_XML($this->data,$this->replar);
		}

		return $this->ret_json_or_xml;
	}
    
    /**
     * Get an approximate geographic midpoint of al approved reports.
     *
     * @params string response_type - XML or JSON
     *
     * @return string
     */
    public function _get_geographic_midpoint($response_type)
    {
        $json_latlon = array();

        $this->query = 'SELECT AVG( latitude ) AS avglat, AVG( longitude ) 
            AS avglon FROM '.$this->table_prefix.'location WHERE id IN 
            (SELECT location_id FROM '.$this->table_prefix.'incident WHERE 
             incident_active = 1)';
		
        $this->items = $this->db->query($this->query);

		foreach ($this->items as $item)
        {
			$latitude = $item->avglat;
			$longitude = $item->avglon;
			break;
		}

		if($response_type == 'json')
        {
			$json_latlon[] = array(
                    "latitude" => $latitude, 
                    "longitude" => $longitude
            );
		}
        else
        {
			$json_latlon['geographic_midpoint'] = array(
                    "latitude" => $latitude, 
                    "longitude" => $longitude
            );

			$replar[] = 'geographic_midpoint';
		}

		//create the json array
		$this->data = array("payload" => array(
                    "domain" => $this->domain,
                    "geographic_midpoint" => $json_latlon),
                "error" => $this->api_actions->_get_error_msg(0)
        );

		if($response_type == 'json') 
        {
			$this->ret_json_or_xml = $this->api_actions->
                _array_as_JSON($this->data);
		}
        else
        {
			$this->ret_json_or_xml =$this->api_actions
                ->_array_as_XML($this->data,$this->replar);
		}

		return $this->ret_json_or_xml;
    }

}
