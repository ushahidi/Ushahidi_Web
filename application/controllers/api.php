<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles API requests.
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

class Api_Controller extends Controller 
{
    private $api_objects;
    private $task;
    private $request;
    private $error;
    private $ret;

    public function __construct()
    {
        $this->api_objects = new ApiObjects();
        $this->task = "";
        $this->request = array();
        $this->error = array();
        $this->ret = "";
    }

    /**
 	* Starting point
 	*/
	public function index()
    {
		//switch task
		$this->_switch_task();
	}
    
    /**
     * Determine which api method to call
     */
    public function _switch_task()
    {
     
        //determine if we are using GET or POST
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $this->request = $_GET;
        }
        else 
        {
            $this->request = $_POST;
        }
        
        // make sure we have a task to work with
        if(!$this->api_objects->api_actions->_verify_array_index(
                    $this->request, 'task'))
        {
        
            $this->error = array(
                    "error" => $this->api_objects->
                    api_actions->_get_error_msg(
                        001, 'task'));
            
            $this->task = "";
        }

        else
        {
            
            $this->task = $this->request['task'];
        }

        //response type
        if(!$this->api_objects->api_actions->_verify_array_index(
                    $this->request, 'resp'))
        {
            $this->response_type = "json";
        }
        else
        {
            $this->response_type = $this->request['resp'];
        }

        // Get the various tasks
        $this->_get_tasks($this->task);

    }

    /**
     * Get various task passed via the URL.
     *
     * @param string task - the task to be passed.
     */
    public function _get_tasks( $task )
    {
        switch($task)
        {
            case "report":
                $this->ret = $this->api_objects->post_reports
                ->_report($this->response_type);
            break;

            	case "3dkml": //report/add an incident
				$this->ret = $this->api_objects->kml
                    ->_3dkml($this->response_type);
			break;

			case "tagnews": //tag a news item to an incident

			case "tagvideo": //report/add an incident

			case "tagphoto": //report/add an incident
				$incidentid = '';

				if(!$this->api_objects->api_actions
                        ->_verify_array_index($this->request, 'id')) 
                {
					$error = array("error" =>
					$this->api_objects->api_actions
                        ->_get_error_msg(001, 'id'));
					break;
				} 
                else 
                {
					$incidentid = $this->request['id'];
				}

				$mediatype = 0;
                
				if($task == "tagnews") $mediatype = 4;

				if($task == "tagvideo") $mediatype = 2;

				if($task == "tagphoto") $mediatype = 1;
                
				$this->ret = $this->api_objects->tag_media
                    ->_tag_media($incidentid, $mediatype,
                            $this->response_type);
                
				break;

            // retrieve all categories
            case "categories": 
                $this->ret = $this->api_objects->categories->_categories(
                        $this->response_type);
                
            break;
            
            //retrieve api keys
            case "apikeys":
                $by = '';
                if(!$this->api_objects->api_actions->_verify_array_index(
                            $this->request,'by'))
                {
                    $this->error = array("errror" =>
                            $this->api_objects->
                                api_actions->_get_error_msg(001, 'by'));
                    break;
                }
                else 
                {
                    $by = $this->request['by'];
                }
                switch($by)
                {
                    case "google":
                        $this->ret = $this->api_objects->api_key->
                        _api_key('api_google',$this->response_type);
                    break;

                    case "yahoo":
                        $this->ret = $this->api_objects->api_key->
                            _api_key('api_yahoo',$this->response_type);
                    break;

                    case "microsoft":
                        $this->ret = $this->api_objects->api_key->
                        _api_key('api_live',$this->response_type);
                    break;

                    default:
                        $this->error = array("error" => 
                                $this->api_objects->api_actions->
                                _get_error_msg(002));
                }

            break;

            case "incidents": //retrieve reports
				/**
				 *
				 * there are several ways to get incidents by
				 */
				$by = '';
				
                $sort = 'DESC';
				
                $orderfield = 'incidentid';

				if(!$this->api_objects->api_actions->_verify_array_index(
                            $this->request,'by'))
                {
                    $this->error = array("errror" =>
                            $this->api_objects->
                                api_actions->_get_error_msg(001, 'by'));
                    break;
                }
                else 
                {
                    $by = $this->request['by'];
                }

				/*IF we have an order by, 0=asc 1=default=desc */
				if($this->api_objects->api_actions->_verify_array_index(
                            $this->request, 'sort'))
                {
					if ( $this->request['sort'] == '0' )
					{
						$sort = 'ASC';
					}
					elseif ( $this->request['sort'] == '1' )
					{
						$sort = 'DESC';
					}
				}

                /*Specify how many incidents to return */
				if($this->api_objects->api_actions->_verify_array_index(
                            $this->request, 'limit'))
                {

					if ( $this->request['limit'] > 0 )
                    {
						$limit = $this->request['limit'];
					} 
                    else 
                    {
						$limit = 20;
					}
				// Make limit variable optional
				} 
                else 
                {
					$limit = 20;
				}

				/* Order field  */
				if($this->api_objects->api_actions->_verify_array_index(
                            $this->request, 'orderfield'))
                {
					switch ( $this->request['orderfield'] )
                    {
						case 'incidentid':
							$orderfield = 'i.id';
							break;
						case 'locationid':
							$orderfield = 'l.location_id';
							break;
						case 'incidentdate':
							$orderfield = 'i.incident_date';
							break;
						default:
					/* Again... it's set but let's cast it in concrete */
							$orderfield = 'i.id';
					}

				}
				switch ($by){
					case "all": // incidents

						$this->ret = $this->api_objects->get_reports
                            ->_reports_by_all($orderfield, $sort, $limit,
                                $this->response_type );

						break;

					case "latlon": //latitude and longitude
						if(($this->api_objects->api_actions->
                            _verify_array_index(
                                $this->request, 'latitude')) AND 
                                ($this->api_objects->api_actions->
                                 _verify_array_index(
                                     $this->request, 'longitude')))
                        {
							$ret = $this->api_object->get_reports->
                                _reports_by_lat_lon(
                                        $this->request['latitude'],
                                        $orderfield,
                                        $this->request['longitude'],
                                        $sort,
                                        $this->response_type
                                );
						} 
                        else 
                        {
							$this->error = array(
                                "error" => $this->api_objects->
                                    api_actions->_get_error_msg(
                                        001,'latitude or longitude')
                                );
						}
						break;

					case "locid": //Location Id
						if(($this->api_objects->api_actions->
                                    _verify_array_index($this->request,
                                        'id')))
                        {
							$this->ret = $this->api_objects
                                ->get_reports->_reports_by_location_id(
                                    $this->request['id'], 
                                    $orderfield, $sort,
                                    $this->response_type 
                            );

						} 
                        else 
                        {
							$this->error = array(
                                "error" => $this->api_objects->api_actions->
                                    _get_error_msg(001, 'id'));
						}
						break;

					case "locname": //Location Name
						if(($this->api_objects->api_actions->
                                    _verify_array_index(
                                        $this->request, 'name')))
                        {
							$this->ret = $this->api_objects->get_reports->
                                _reports_by_location_name(
                                    $this->request['name'], 
                                    $orderfield, $sort,
                                    $this->response_type
                            );
                                
						} 
                        else 
                        {
							$this->error = array(
                                "error" => $this->api_objects->api_actions
                                ->_get_error_msg(001, 'name')
                            );
						}
						break;

					case "catid": //Category Id
						if(($this->api_objects->api_actions->
                                    _verify_array_index($this->request,
                                        'id')))
                        {
							$this->ret = $this->api_objects->get_reports
                                ->_reports_by_category_id(
                                        $this->request['id'], 
                                        $orderfield, 
                                        $sort,$this->response_type
                                );

						} 
                        else 
                        {
							$this->error = array(
                                    "error" => $this->api_objects
                                ->api_actions->_get_error_msg(001, 'id'));
						}
						break;

					case "catname": //Category Name
						if(($this->api_objects->api_actions
                                    ->_verify_array_index(
                                        $this->request, 'name')))
                        {
							$this->ret = $this->api_objects->get_reports
                                ->_reports_by_category_name(
                                        $this->request['name'], 
                                        $orderfield, 
                                        $sort,
                                        $this->response_type);
						} 
                        else 
                        {
							$this->error = array(
                                    "error" => $this->api_objects->
                                        api_actions->_get_error_msg(001,
                                        'name')
                                );
						}
                    break;
                    
                    case "sinceid": //Since Id
                        if(($this->api_objects->api_actions->
                                    _verify_array_index(
                                        $this->request,'id')))
                        {
                            $this->ret = $this->api_objects->get_reports
                                ->_reports_by_since_id(
                                    $this->request['id'], 
                                    $orderfield, $sort,
                                    $this->response_type
                                );
                        } 
                        else 
                        {
                            $this->error = array(
                                "error" => $this->api_objects
                                ->api_actions->_get_error_msg(001,'id'));
                        }
                        break;
                    

					default:
						$this->error = array(
                                "error" => $this->api_objects
                                ->api_actions->_get_error_msg(002)
                        );
				}

				break;


            // retrieve categories by id
            case "category":
                $id = 0;
                
                if(!$this->api_objects->api_actions
                    ->_verify_array_index(
                        $this->request, 'id'))
                {
                    $this->error = array( "error" => 
                        $this->api_objects->api_actions
                        ->_get_error_msg(001,'id')
                    );
                    break;
                }
                else
                {
                    $id = $this->request['id'];
                }
                
                $this->ret = $this->api_objects->categories
                    ->_category($id,$this->response_type);
            break;

            case "locations": //retrieve locations
				$this->ret = $this->api_objects->locations->_locations(
                        $this->response_type);
				break;

			case "location": //retrieve locations
				$by = '';

				if(!$this->api_objects->api_actions
                        ->_verify_array_index($this->request, 'by'))
                {
					$this->error = array("error" =>
					$this->api_objects->api_actions
                        ->_get_error_msg(001, 'by')
                    );
					break;
				} 
                else 
                {
					$by = $this->request['by'];
				}

				switch ($by)
                {
					case "latlon": //latitude and longitude
						break;

					case "locid": //id
						if(($this->api_objects->api_actions->
                                _verify_array_index($this->request, 'id')))
                        {
							$this->ret = $this->api_objects->locations->
                                _location_by_id($this->request['id'],
                                        $this->response_type);
						} 
                        else 
                        {
							$this->error = array(
                                "error" => $this->api_objects->api_actions->
                                    _get_error_msg(001, 'id'));
						}
						break;

					case "country": //id
						if(($this->api_objects->api_actions->
                                _verify_array_index($this->request,'id')))
                        {
							$this->ret = $this->api_objects->locations->
                                _location_by_country_id(
                                        $this->request['id'],
                                        $this->response_type);
						} else {
							$this->error = array("error" => $this->
                                    api_objects->api_actions->
                                    _get_error_msg(001, 'id'));
						}
						break;

					default:
						$this->error = array(
                            "error" => $this->api_objects->api_actions->
                            _get_error_msg(002));
				}

				break;


            case "countries": //retrieve countries
			    $this->ret = $this->api_objects->countries->_countries(
                        $this->response_type);
				break;

			case "country": //retrieve countries
				$by = '';

				if(!$this->api_objects->api_actions->
                        _verify_array_index(
                            $this->request, 'by'))
                {
					$this->error = array("error" => 
                            $this->api_objects->api_actions->
                            _get_error_msg(001, 'by'));
					break;
				} else {
					$by = $this->request['by'];
				}

				switch ($by)
                {

					case "countryid": //id
						if(($this->api_objects->api_actions->
                                    _verify_array_index(
                                        $this->request, 'id')))
                        {
						    $this->ret = $this->api_objects->
                                countries->_country_by_id(
                                $this->request['id'],$this->response_type);
						} else {
						    $this->error = array("error" => 
                                    $this->api_objects->api_actions->
                                    _get_error_msg(001, 'id'));
						}
						break;

					case "countryname": //name
						if(($this->api_objects->api_actions->
                                    _verify_array_index(
                                        $this->request, 'name')))
                        {
							$this->ret = $this->api_objects->countries->
                                _country_by_name($this->request['name'],
                                        $this->response_type);
						} 
                        else 
                        {
						    $this->error = array(
                                "error" => $this->api_objects->
                                api_actions->_get_error_msg(001, 'name'));
						}
						break;

					case "countryiso": //name
						if(($this->api_objects->api_actions->
                                    _verify_array_index(
                                        $this->request, 'iso')))
                        {
							$this->ret = $this->api_objects->countries->
                                _country_by_iso($this->request['iso'],
                                        $this->response_type);
						} 
                        else 
                        {
							$this->error = array(
                                "error" => $this->api_objects->
                                api_actions->_get_error_msg(001, 'iso'));
						}
						break;

					default:
						$this->error = array(
                            "error" => $this->api_objects->api_actions->
                            _get_error_msg(002));
				}

				break;
                
                // retrieve an ushahidi instance version
            case "version": 
			    $this->ret = $this->api_objects->get_system->
                        _get_version_number($this->response_type);
				break;
            
            //retrieve the geographic midpoint of incidents
			case "geographicmidpoint": 				
               $this->ret = $this->api_objects
                    ->get_reports
                    ->_get_geographic_midpoint($this->response_type);
				break;
            
             //retrieve the number of approved incidents
			case "incidentcount":				
                $this->ret = $this->api_objects
                    ->get_reports->_get_report_count($this->response_type);
				break;
            
            //retrieve lat and lon for map centre
			case "mapcenter": 				
                $this->ret = $this->api_objects->private_func
                    ->_map_center($this->response_type);
				break;

            default:
                $this->error = array(
                    "error" => $this->api_objects->api_actions->
                        _get_error_msg(999));

        }

        //create the response depending on the kind that was requested
        
        if(!empty($this->error) || count( $this->error ) > 0 )
        {
            if( $this->response_type == 'json')
            {
                $this->ret = json_encode($this->error);
            }
            else
            {
                
                $this->ret = $this->api_objects->api_actions->
                    _array_as_XML($this->error, 
                    array());
            }
            
        }
        
        //avoid caching
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the pas

        if($this->response_type == 'xml') 
        {
            
            header("Content-type: text/xml");
        }
        
		print $this->ret;

		//END
    }

}
