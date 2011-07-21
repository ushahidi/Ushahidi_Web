<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incidents_Api_Object
 *
 * This class handles reports activities via the API.
 *
 * @version 26 - Emmanuel Kala 2010-10-22
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

class Incidents_Api_Object extends Api_Object_Core {
	/**
	 * Record sorting order ASC or DESC
	 * @var string
	 */
	private $sort;
	
	/**
	 * Column name by which to order the records
	 * @var string
	 */
	private $order_field;
	
	/**
	 * Constructor
	 */
	public function __construct($api_service)
	{
		parent::__construct($api_service);
	}
    
	/**
	 * Implementation of abstract method in parent
	 *
	 * Handles the API task parameters
	 */
	public function perform_task()
	{
		// Check if the 'by' parameter has been specified
		if ( ! $this->api_service->verify_array_index($this->request, 'by'))
		{
			// Set "all" as the default method for fetching incidents
			$this->by = 'all';
		}
		else
		{
			$this->by = $this->request['by'];
		}
		
		// Check optional parameters
		$this->_check_optional_parameters();
		
		// Begin task switching
		switch ($this->by)
		{
			// Get all incidents
			case "all":
				$this->response_data = $this->_get_incidents();
			break;

			// Get specific incident by ID
			case "incidentid":
				if ( ! $this->api_service->verify_array_index($this->request, 'id'))
				{
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(001, 'id')
					));

					return;
				}
				else
				{
					$params = array('i.id = '.$this->check_id_value($this->request['id']));
					$this->response_data = $this->_get_incidents($params);
				}
			break;

			// Get incidents by latitude and longitude
			case "latlon":
				if ($this->api_service->verify_array_index($this->request, 'latitude')
					AND $this->api_service->verify_array_index($this->request, 'longitude'))
				{ 
					// Build out the parameters
					$params = array(
						'l.latitude = '.$this->check_id_value($this->request['latitude']),
						'l.longitude ='.$this->check_id_value($this->request['longitude'])
					);
					
					// Fetch the incidents
					$this->response_data = $this->_get_incidents($params);
				}
				else
				{
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(001, 'latitude or longitude')
					));
					
					return;
				}
			break;

			// Get incidents by location id
			case "locid":
				if ( ! $this->api_service->verify_array_index($this->request, 'id'))
				{
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(001, 'id')
					));

					return;
				}
				else
				{
					$params = array(
						'i.location_id = '.$this->check_id_value($this->request['id'])
					);
					
					$this->response_data = $this->_get_incidents($params);
				}
			break;

			// Get incidents by location name
			case "locname":
				if ( ! $this->api_service->verify_array_index($this->request, 'name'))
				{
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(001, 'name') 
					));

					return;
				}
				else
				{
					$params = array(
						'l.location_name = "'.$this->request['name'].'"'
					);
					
					$this->response_data = $this->_get_incidents($params);
				}
			break;

			// Get incidents by category id
			case "catid":
				if ( ! $this->api_service->verify_array_index($this->request, 'id'))
				{
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(001, 'id')
					));

					return;
				}
				else
				{
					$category_id = $this->check_id_value($this->request['id']);
					$params = array(
						'c.id = '.$category_id,
						'c.category_visible = 1'
					);
					
					$this->response_data = $this->_get_incidents($params);
				}
			break;

			// Get incidents by category name
			case "catname":
				if ( ! $this->api_service->verify_array_index($this->request, 'name'))
				{
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(001, 'name')
					));
					return;
				}
				else
				{
					$params = array(
						'c.category_title LIKE "%'.$this->request['name'].'%"',
						'c.category_visible = 1'
					);
					
					$this->response_data = $this->_get_incidents($params);
				}
			break;

			// Get the number of reports in each category
			case "catcount":
				$this->response_data = $this->_get_incident_counts_per_category();
			break;

			// Get incidents greater than a specific incident_id in the DB
			case "sinceid":
				if ( ! $this->api_service->verify_array_index($this->request, 'id'))
				{
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(001, 'id')
					));

					return;
				}
				else
				{
					$params = array(
						'i.id > '.$this->check_id_value($this->request['id'])
					);
					
					$this->response_data = $this->_get_incidents($params);
				}
			break;

			// Get incidents less that a specific incident_id
			case "maxid":
				if ( ! $this->api_service->verify_array_index($this->request, 'id'))
				{
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(001, 'id')
					));

					return;
				}
				else
				{
					$params = array(
						'i.id < '.$this->check_id_value($this->request['id'])
					);
					
					$this->response_data = $this->_get_incidents($params);
				}
			break;

			// Get incidents based on a box using two lat,lon coords
			case "bounds":
				$this->response_data = $this->_get_incidents_by_bounds($this->request['sw'],$this->request['ne'],$this->request['c']);
			break;

			// Error therefore set error message 
			default:
				$this->set_error_message(array(
					"error" => $this->api_service->get_error_msg(002)
				));
		}
	}

	/**
	 * Checks for optional parameters in the request and sets the values
	 * in the respective class members
	 */
	private function _check_optional_parameters()
	{
		// Check if the sort parameter has been specified
		if ($this->api_service->verify_array_index($this->request, 'sort'))
		{
			$this->sort = ($this->request['sort'] == '0') ? 'ASC' : 'DESC';
		}
		else
		{
			$this->sort = 'DESC';
		}

		// Check if the limit parameter has been specified
		if ($this->api_service->verify_array_index($this->request, 'limit'))
		{
			$this->set_list_limit($this->request['limit']);
		}               

		// Check if the orderfield parameter has been specified
		if ($this->api_service->verify_array_index($this->request, 'orderfield'))
		{
			switch ($this->request['orderfield'])
			{
				case "incidentid":
					$this->order_field = 'i.id';
				break;

				case "locationid":
					$this->order_field = 'i.location_id';
				break;

				case "incidentdate":
					$this->order_field = 'i.incident_date';
				break;

				default:
					$this->order_field = 'i.incident_date';
			}
		}
		else
		{
			$this->order_field = 'i.incident_date';
		}
	}

	/**
	 * Generic function to get reports by given set of parameters
	 *
	 * @param string $where SQL where clause
	 * @param int $limit No. of records to return - set to 20 by default
	 * @return string XML or JSON string
	 */
	public function _get_incidents($where = array())
	{
		// STEP 1.
		// Get the incidents
		$items = Incident_Model::get_incidents($where, $this->list_limit, $this->order_field, $this->sort);
		
		//No record found.
		if ($items->count() == 0)
		{
			return $this->response(4, $this->error_messages);
		}
		
		// Records found - proceed
		
		// Set the no. of records returned
		$this->record_count = $items->count();
		
		// Will hold the XML/JSON string to return
		$ret_json_or_xml = '';

		$json_reports = array();
		$json_report_media = array();
		$json_report_categories = array();
		$json_incident_media = array();
		$upload_path = str_replace("media/uploads/", "", Kohana::config('upload.relative_directory')."/");
		
		//XML elements
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('response');
		$xml->startElement('payload');
		$xml->writeElement('domain',$this->domain);
		$xml->startElement('incidents');
		
		// Records found, proceed
		// Store the incident ids
		$incidents_ids = array();
		foreach ($items as $item)
		{
			$incident_ids[] = $item->incident_id;
		}
		
		// 
		// STEP 2.
		// Fetch the incident categories
		// 
		$this->query = "SELECT c.category_title AS categorytitle, ic.incident_id, "
					. "c.id AS cid "
					. "FROM ".$this->table_prefix."category AS c "
					. "INNER JOIN ". $this->table_prefix."incident_category AS ic ON ic.category_id = c.id "
					. "WHERE ic.incident_id IN (".implode(',', $incident_ids).")";
		
		// Execute the query
		$incident_categories = $this->db->query($this->query);
		
		// To hold the incident category items
		$category_items = array();
		
		// Temporary counter
		$i = 1;
		
		// Fetch items into array
		foreach ($incident_categories as $incident_category)
		{
			$category_items[$incident_category->incident_id][$i]['cid'] = $incident_category->cid;
			$category_items[$incident_category->incident_id][$i]['categorytitle'] = $incident_category->categorytitle;
			$i++;
		}
		
		// Free temporary variables from memory
		unset ($incident_categories);
		
		// 
		// STEP 3.
		// Fetch the media associated with all the incidents
		// 
		$this->query = "SELECT i.id AS incident_id, m.id AS mediaid, m.media_title AS mediatitle, "
					. "m.media_type AS mediatype, m.media_link AS medialink, m.media_thumb AS mediathumb "
					. "FROM ".$this->table_prefix."media AS m " 
					. "INNER JOIN ".$this->table_prefix."incident AS i ON i.id = m.incident_id "
					. "WHERE i.id IN (".implode(",", $incident_ids).")";
		
		$media_items_result = $this->db->query($this->query);
		
		// To store the fetched media items
		$media_items = array();
		
		// Reset the temporary counter
		$i = 1;
		
		// Fetch items into array
		foreach ($media_items_result as $media_item)
		{
			$media_items[$media_item->incident_id][$i]['mediaid'] = $media_item->mediaid;
			$media_items[$media_item->incident_id][$i]['mediatitle'] = $media_item->mediatitle;
			$media_items[$media_item->incident_id][$i]['mediatype'] = $media_item->mediatype;
			$media_items[$media_item->incident_id][$i]['medialink'] = $media_item->medialink;
			$media_items[$media_item->incident_id][$i]['mediathumb'] = $media_item->mediathumb;
			$i++;
		}
		
		// Free temporary variables
		unset ($media_items_result, $i);
		
		foreach ($items as $item)
		{
			// Build xml file
			$xml->startElement('incident');

			$xml->writeElement('id',$item->incident_id);
			$xml->writeElement('title',$item->incident_title);
			$xml->writeElement('description',$item->incident_description);
			$xml->writeElement('date',$item->incident_date);
			$xml->writeElement('mode',$item->incident_mode);
			$xml->writeElement('active',$item->incident_active);
			$xml->writeElement('verified',$item->incident_verified);
			$xml->startElement('location');
			$xml->writeElement('id',$item->location_id);
			$xml->writeElement('name',$item->location_name);
			$xml->writeElement('latitude',$item->latitude);
			$xml->writeElement('longitude',$item->longitude);
			$xml->endElement();
			$xml->startElement('categories');

			$json_report_categories[$item->incident_id] = array();
			
			// Check if the incident id exists
			if (isset($category_items[$item->incident_id]))
			{
				foreach ($category_items[$item->incident_id] as $category_item)
				{
					if ($this->response_type == 'json')
					{
						$json_report_categories[$item->incident_id][] = array(
							"category"=> array(
								"id" => $category_item['cid'],
								"title" => $category_item['categorytitle']
							)
						);
					} 
					else 
					{
						$xml->startElement('category');
						$xml->writeElement('id',$category_item['cid']);
						$xml->writeElement('title', $category_item['categorytitle'] );
						$xml->endElement();
					}
				}
			}

			// End categories
			$xml->endElement();
			
			$json_report_media[$item->incident_id] = array();
			
			if (count($media_items) > 0)
			{
				if (isset($media_items[$item->incident_id]) AND count($media_items[$item->incident_id]) > 0)
				{
					$xml->startElement('mediaItems');

					foreach ($media_items[$item->incident_id] as $media_item)
					{
						if ($media_item['mediatype'] != 1)
						{
							$upload_path = "";
						}

						$url_prefix = url::base().Kohana::config('upload.relative_directory').'/';
						if($this->response_type == 'json')
						{
							$json_report_media[$item->incident_id][] = array(
								"id" => $media_item['mediaid'],
								"type" => $media_item['mediatype'],
								"link" => $upload_path.$media_item['medialink'],
								"thumb" => $upload_path.$media_item['mediathumb'],
							);

							// If we are look at certain types of media, add some fields
							if($media_item['mediatype'] == 1)
							{
								// Grab that last key up there
								$add_to_key = key($json_report_media[$item->incident_id]) + 1;

								// Give a full absolute URL to the image 
								$json_report_media[$item->incident_id][$add_to_key]["thumb_url"] =  $url_prefix.$upload_path.$media_item['mediathumb'];

								$json_report_media[$item->incident_id][$add_to_key]["link_url"] = $url_prefix.$upload_path.$media_item['medialink'];
							}
						} 
						else 
						{
							$xml->startElement('media');

							if( $media_item['mediaid'] != "" )
							{
								$xml->writeElement('id',$media_item['mediaid']);
							}
						
							if( $media_item['mediatitle'] != "" )
							{
								$xml->writeElement('title', $media_item['mediatitle']);
							}
						
							if( $media_item['mediatype'] != "" )
							{
								$xml->writeElement('type', $media_item['mediatype']);
							}
						
							if( $media_item['medialink'] != "" ) 
							{
								$xml->writeElement('link', $upload_path.$media_item['medialink']);
							}
						
							if( $media_item['mediathumb'] != "" ) 
							{
								$xml->writeElement('thumb', $upload_path.$media_item['mediathumb']);
							}
						
							if( $media_item['mediathumb'] != "" AND $media_item['mediatype'] == 1 )
							{
								$add_to_key = key($json_report_media[$item->incident_id]) + 1;
							
								$xml->writeElement('thumb_url', $url_prefix.$upload_path.$media_item['mediathumb']);

								$xml->writeElement('link_url', $url_prefix.$upload_path.$media_item['medialink']);
	                        }
							$xml->endElement();
						}
					}
					$xml->endElement(); // Media
				}
			}
			
			$xml->endElement(); // End incident
			
			// Check for response type
			if ($this->response_type == 'json')
			{
				$json_reports[] = array(
					"incident" => array(
						"incidentid" => $item->incident_id,
						"incidenttitle" => $item->incident_title,
						"incidentdescription" => $item->incident_description,
						"incidentdate" => $item->incident_date,
						"incidentmode" => $item->incident_mode,
						"incidentactive" => $item->incident_active,
						"incidentverified" => $item->incident_verified,
						"locationid" => $item->location_id,
						"locationname" => $item->location_name,
						"locationlatitude" => $item->latitude,
						"locationlongitude" => $item->longitude
					), 
					"categories" => $json_report_categories[$item->incident_id], 
					"media" => $json_report_media[$item->incident_id]
				);
			}
		}
		
		// Create the JSON array
		$data = array(
			"payload" => array(
				"domain" => $this->domain,
				"incidents" => $json_reports
			),
			"error" => $this->api_service->get_error_msg(0)
		);
		
		if ($this->response_type == 'json')
		{
			return $this->array_as_json($data);

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
	 * Returns the number of reports in each category
	 */
	private function _get_incident_counts_per_category()
	{       
		$this->query = 'SELECT category_id, COUNT(category_id) AS reports FROM '.$this->table_prefix.'incident_category '
					. 'WHERE incident_id IN (SELECT id FROM '.$this->table_prefix.'incident WHERE incident_active = 1) '
					. 'GROUP BY category_id';

		$items = $this->db->query($this->query);

		$category_counts = array();

		foreach ($items as $item)
		{
			$category_counts[] = array('category_id' => $item->category_id, 'reports' => $item->reports);
		}

		$this->query = 'SELECT COUNT(id) AS total_count FROM '.$this->table_prefix.'incident WHERE incident_active = 1;';

		$count = $this->db->query($this->query);

		foreach($count as $c)
		{
			$total_count = $c->total_count;
			break;
		}

		//create the json array
		$data = array(
			"payload" => array(
				"domain" => $this->domain,
				"category_counts" => $category_counts,
				"total_reports" => $total_count
			),
			"error" => $this->api_service->get_error_msg(0)
		);

		// Return data
		$this->response_data =  ($this->response_type == 'json')
			? $this->array_as_json($data)
			: $this->array_as_xml($data, $replar);

		echo $this->response_data;
	}
	
    
	/**
	 * Get incidents within a certain lat,lon bounding box
	 *
	 * @param double $sw is the southwest lat,lon of the box
	 * @param double $ne is the northeast lat,lon of the box
	 * @param int $c is the categoryid
	 * @return string XML or JSON string containing the fetched incidents
	 */
	private function _get_incidents_by_bounds($sw, $ne, $c = 0)
	{
		// Break apart location variables, if necessary
		$southwest = array();
		if (isset($sw))
		{
			$southwest = explode(",",$sw);
		}

		$northeast = array();
		if (isset($ne))
		{
			$northeast = explode(",",$ne);
		}
		
		// To hold the parameters
		$params = array();
		if ( count($southwest) == 2 AND count($northeast) == 2 )
		{
			$lon_min = (float) $southwest[0];
			$lon_max = (float) $northeast[0];
			$lat_min = (float) $southwest[1];
			$lat_max = (float) $northeast[1];
			
			// Add parameters
			array_push($params,
				'l.latitude >= '.$lat_min,
				'l.latitude <= '.$lat_max,
				'l.longitude >= '.$lon_min,
				'l.longitude <= '.$lon_max
			);
		}
		
		// Fix for pulling categories using the bounding box
		// Credits to Antonoio Lettieri http://github.com/alettieri
		// Check if the specified category id is valid
		if (Category_Model::is_valid_category($c))
		{
			array_push($params, 'c.id = '.$c);
		}
		
		return $this->_get_incidents($params);
        
    }

	/**
	 * Gets the number of approved reports
	 * 
	 * @param string response_type - XML or JSON
	 * @return string
	 */
	public function get_incident_count()
	{
		$json_count = array();

		$this->query = 'SELECT COUNT(*) as count FROM '.$this->table_prefix.'incident WHERE incident_active = 1';

		$items = $this->db->query($this->query);

		foreach ($items as $item)
		{
			$count = $item->count;
			break;
		}

		if ($this->response_type == 'json')
		{
			$json_count[] = array("count" => $count);
		}
		else
		{
			$json_count['count'] = array("count" => $count);
			$this->replar[] = 'count';
		}

		// Create the JSON array
		$data = array(
				"payload" => array(
				"domain" => $this->domain,
				"count" => $json_count
			),
			"error" => $this->api_service->get_error_msg(0)
		);

		$this->response_data = ($this->response_type == 'json')
			? $this->array_as_json($data)
			: $this->array_as_xml($data, $this->replar);
	}
    
	/**
	 * Get an approximate geographic midpoint of al approved reports.
	 *
	 * @param string $response_type - XML or JSON
	 * @return string
	 */
	public function get_geographic_midpoint()
	{
		$json_latlon = array();

		$this->query = 'SELECT AVG( latitude ) AS avglat, AVG( longitude ) 
					AS avglon FROM '.$this->table_prefix.'location WHERE id IN 
					(SELECT location_id FROM '.$this->table_prefix.'incident WHERE 
					incident_active = 1)';
        
		$items = $this->db->query($this->query);

		foreach ($items as $item)
		{
			$latitude = $item->avglat;
			$longitude = $item->avglon;
			break;
		}
		
		if ($this->response_type == 'json')
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
		
		// Create the JSON array
		$data = array(
			"payload" => array(
				"domain" => $this->domain,
				"geographic_midpoint" => $json_latlon
			),
			"error" => $this->api_service->get_error_msg(0)
		);

		// Return data
		$this->response_data =  ($this->response_type == 'json')
			? $this->array_as_json($data)
			: $this->array_as_xml($data, $replar);
	}
}
