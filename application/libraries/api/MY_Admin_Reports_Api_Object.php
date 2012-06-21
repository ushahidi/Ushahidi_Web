<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles GET request for KML via the API.
 *
 * @version 25 - Emmanuel Kala 2010-10-25
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

class Admin_Reports_Api_Object extends Api_Object_Core {

	public function __construct($api_service)
	{
		parent::__construct($api_service);
	}

	/**
	 *  Handles admin report task requests submitted via the API service
	 */
	public function perform_task()
	{
		// Authenticate the user
		if ( ! $this->api_service->_login(TRUE))
		{
			$this->set_error_message($this->response(2));
			return;
		}

		// by request
		if ($this->api_service->verify_array_index($this->request, 'by'))
		{
			$this->by = $this->request['by'];

			switch ($this->by)
			{
				case "approved" :
					$this->response_data = $this->_get_approved_reports();
					break;

				case "unapproved" :
					$this->response_data = $this->_get_unapproved_reports();
					break;

				case "verified" :
					$this->response_data = $this->_get_verified_reports();
					break;

				case "unverified" :
					$this->response_data = $this->_get_unverified_reports();
					break;

				default :
					$this->set_error_message(array(
						"error" => $this->api_service->get_error_msg(002)
					));
			}
			return;
		}

		// action request
		else if ($this->api_service->verify_array_index($this->request, 'action'))
		{
			$this->report_action();
			return;
		}
		else
		{
			$this->set_error_message(array("error" => $this->api_service->get_error_msg(001, 'by or action')));
			return;
		}
	}

	/**
	 * Handles report actions performed via the API service
	 */
	public function report_action()
	{
		$action = '';
		// Will hold the report action
		$incident_id = -1;
		// Will hold the ID of the incident/report to be acted upon

		// Authenticate the user
		if ( ! $this->api_service->_login())
		{
			$this->set_error_message($this->response(2));
			return;
		}

		// Check if the action has been specified
		if ( ! $this->api_service->verify_array_index($this->request, 'action'))
		{
			$this->set_error_message(array(
				"error" => $this->api_service->get_error_msg(001, 'action')
			));

			return;
		}
		else
		{
			$action = $this->request['action'];
		}

		// Route report actions to their various handlers
		switch ($action)
		{
			// Delete report
			case "delete" :
				$this->_delete_report();
				break;

			// Approve report
			case "approve" :
				$this->_approve_report();
				break;

			// Verify report
			case "verify" :
				$this->_verify_report();
				break;

			// Edit report
			case "edit" :
				$this->_edit_report();
				break;

			default :
				$this->set_error_message(array(
					"error" => $this->api_service->get_error_msg(002)
				));
		}
	}

	/**
	 * Generic function to get reports by given set of parameters
	 */
	private function _get_reports($where = '', $limit = '')
	{
		$ret_json_or_xml = '';
		// Will hold the JSON/XML string

		$json_reports = array();
		$json_report_media = array();
		$json_incident_media = array();
		$json_report_categories = array();

		//XML elements
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('response');
		$xml->startElement('payload');
		$xml->writeElement('domain', $this->domain);
		$xml->startElement('incidents');

		// Find reports/incidents
		$this->query = "SELECT i.id AS incidentid, i.incident_title AS incidenttitle, "
		    . "i.incident_description AS incidentdescription, i.incident_date AS incidentdate, "
		    . "i.incident_mode AS incidentmode, i.incident_active AS incidentactive, "
		    . "i.incident_verified AS incidentverified, l.id AS locationid, "
		    . "l.location_name AS locationname, l.latitude AS locationlatitude, "
		    . "l.longitude AS locationlongitude "
		    . "FROM " . $this->table_prefix . "incident AS i "
		    . "INNER JOIN " . $this->table_prefix . "location as l ON (l.id = i.location_id) "
		    . "$where $limit";

		$items = $this->db->query($this->query);

		$i = 0;

		//No record found.
		if ($items->count() == 0)
		{
			return $this->response(4);
		}

		foreach ($items as $item)
		{

			if ($this->response_type == 'json')
			{
				$json_report_media = array();
				$json_report_categories = array();
			}

			//build xml file
			$xml->startElement('incident');

			$xml->writeElement('id', $item->incidentid);
			$xml->writeElement('title', $item->incidenttitle);
			$xml->writeElement('description', $item->incidentdescription);
			$xml->writeElement('date', $item->incidentdate);
			$xml->writeElement('mode', $item->incidentmode);
			$xml->writeElement('active', $item->incidentactive);
			$xml->writeElement('verified', $item->incidentverified);
			$xml->startElement('location');
			$xml->writeElement('id', $item->locationid);
			$xml->writeElement('name', $item->locationname);
			$xml->writeElement('latitude', $item->locationlatitude);
			$xml->writeElement('longitude', $item->locationlongitude);
			$xml->endElement();
			$xml->startElement('categories');

			// Fetch categories
			$this->query = " SELECT c.category_title AS categorytitle, c.id AS cid "
			    . "FROM " . $this->table_prefix . "category AS c "
			    . "INNER JOIN " . $this->table_prefix . "incident_category AS ic ON (ic.category_id = c.id) "
			    . "WHERE ic.incident_id =" . $item->incidentid;

			$category_items = $this->db->query($this->query);

			foreach ($category_items as $category_item)
			{
				if ($this->response_type == 'json')
				{
					$json_report_categories[] = array(
					    "category" => array(
					    	"id" => $category_item->cid,
					    	"title" => $category_item->categorytitle
					));
				}
				else
				{
					$xml->startElement('category');
					$xml->writeElement('id', $category_item->cid);
					$xml->writeElement('title', $category_item->categorytitle);
					$xml->endElement();
				}
			}

			$xml->endElement();
			//end categories

			//fetch media associated with an incident
			$this->query = "SELECT m.id as mediaid, m.media_title AS mediatitle, m.media_type AS mediatype,"
			    . "m.media_link AS medialink, " . "m.media_thumb AS mediathumb "
			    . "FROM " . $this->table_prefix . "media AS m "
			    . "INNER JOIN " . $this->table_prefix . "incident AS i ON (i.id = m.incident_id) "
			    . "WHERE i.id =" . $item->incidentid;

			$media_items = $this->db->query($this->query);

			if (count($media_items) > 0)
			{
				$xml->startElement('mediaItems');
				foreach ($media_items as $media_item)
				{
					if ($this->response_type == 'json')
					{
						$json_incident_media[] = array(
							"id" => $media_item->mediaid,
							"type" => $media_item->mediatype,
							"link" => $media_item->medialink
						);
					}
					else
					{
						$xml->startElement('media');

						if ($media_item->mediaid != "")
						{
							$xml->writeElement('id', $media_item->mediaid);
						}

						if ($media_item->mediatitle != "")
						{
							$xml->writeElement('title', $media_item->mediatitle);
						}

						if ($media_item->mediatype != "")
						{
							$xml->writeElement('type', $media_item->mediatype);
						}

						if ($media_item->medialink != "")
						{
							$xml->writeElement('link', $media_item->medialink);
						}

						if ($media_item->mediathumb != "")
						{
							$xml->writeElement('thumb', $media_item->mediathumb);
						}

						$xml->endElement();
					}
				}

				$xml->endElement();
				// media

			}

			$xml->endElement();
			// end incident

			//needs different treatment depending on the output
			if ($this->response_type == 'json')
			{
				$json_reports[] = array(
					"incident" => $item,
					"categories" => $json_report_categories,
					"media" => $json_report_media
				);
			}

		}

		// Create the json array
		$data = array(
			"payload" => array(
				"domain" => $this->domain,
				"incidents" => $json_reports
			),
			"error" => $this->api_service->get_error_msg(0)
		);

		if ($this->response_type == 'json')
		{
			$ret_json_or_xml = $this->array_as_json($data);

			return $ret_json_or_xml;
		}
		else
		{
			$xml->endElement();
			//end incidents
			$xml->endElement();
			// end payload
			$xml->startElement('error');
			$xml->writeElement('code', 0);
			$xml->writeElement('message', 'No Error');
			$xml->endElement();
			//end error
			$xml->endElement();
			// end response

			return $xml->outputMemory(true);
		}

	}

	/**
	 * List unapproved reports
	 *
	 * @param string response - The response to return.XML or JSON
	 *
	 * @return array
	 */
	private function _get_unapproved_reports()
	{
		if ($_POST)
		{
			$where = "\nWHERE i.incident_active = 0 ";

			$where .= "ORDER BY i.id DESC ";

			$limit = "\nLIMIT 0, $this->list_limit";

			return $this->_get_reports($where, $limit);
		}
		else
		{
			return $this->response(3);
		}
	}

	/**
	 * List first 15 approved reports
	 *
	 * @return array
	 */
	public function _get_approved_reports()
	{
		if ($_POST)
		{
			$where = "\nWHERE i.incident_active = 1 ";

			$where .= "ORDER BY i.id DESC ";

			$limit = "\nLIMIT 0, $this->list_limit";

			return $this->_get_reports($where, $limit);
		}
		else
		{
			return $this->response(3);
		}

	}

	/**
	 * List first 15 approved reports
	 *
	 * @return array
	 */
	public function _get_verified_reports()
	{
		if ($_POST)
		{
			$where = "\nWHERE i.incident_verified = 1 ";

			$where .= "ORDER BY i.id DESC ";

			$limit = "\nLIMIT 0, $this->list_limit";

			return $this->_get_reports($where, $limit);
		}
		else
		{
			return $this->response(3);
		}

	}

	/**
	 * List first 15 approved reports
	 *
	 * @param string response_type - The response type to return XML or JSON
	 *
	 * @return array
	 */
	public function _get_unverified_reports()
	{
		if ($_POST)
		{
			$where = "\nWHERE i.incident_verified = 0 ";

			$where .= "ORDER BY i.id DESC ";

			$limit = "\nLIMIT 0, $this->list_limit";

			return $this->_get_reports($where, $limit);
		}
		else
		{
			return $this->response(3);
		}

	}

	/**
	 * Edit existing report
	 *
	 * @return array
	 */
	public function _edit_report()
	{
		print $this->_submit_report();
	}

	/**
	 * Delete existing report
	 *
	 * @param int incident_id - the id of the report to be deleted.
	 */
	private function _delete_report()
	{
		$form = array('incident_id' => '', );

		$ret_value = 0;
		// Return error value; start with no error

		$errors = $form;

		if ($_POST)
		{
			$post = Validation::factory($_POST);

			//  Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list
			// of checks, carried out in order
			$post->add_rules('incident_id', 'required', 'numeric');

			if ($post->validate(FALSE))
			{
				$incident_id = $post->incident_id;
				$update = new Incident_Model($incident_id);

				if ($update->loaded)
				{
					$update->delete();
				}
			}
			else
			{
				//TODO i18nize the string
				$this->error_messages .= "Incident ID is required.";
				$ret_value = 1;
			}
		}
		else
		{
			$ret_value = 3;
		}

		// Set the reponse info to be sent back to client
		$this->response_data = $this->response($ret_value, $this->error_messages);

	}

	/**
	 * Approve / unapprove an existing report
	 *
	 * @param int report_id - the id of the report to be approved.
	 *
	 * @return
	 */
	private function _approve_report()
	{
		$form = array('incident_id' => '', );

		$errors = $form;

		$ret_value = 0;
		// will hold the return value

		if ($_POST)
		{
			$post = Validation::factory($_POST);

			//  Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list
			// of checks, carried out in order
			$post->add_rules('incident_id', 'required', 'numeric');

			if ($post->validate())
			{
				$incident_id = $post->incident_id;
				$update = new Incident_Model($incident_id);

				if ($update->loaded == true)
				{
					if ($update->incident_active == 0)
					{
						$update->incident_active = '1';
					}
					else
					{
						$update->incident_active = '0';
					}

					// Tag this as a report that needs to be sent
					// out as an alert
					if ($update->incident_alert_status != '2')
					{
						// 2 = report that has had an alert sent
						$update->incident_alert_status = '1';
					}

					$update->save();
					reports::verify_approve($update);

				}
				else
				{
					//TODO i18nize the string
					//couldin't approve the report
					$this->error_messages .= "Couldn't approve the report id " . $post->incident_id;
					$ret_value = 1;
				}

			}
			else
			{
				//TODO i18nize the string
				$this->error_messages .= "Incident ID is required.";
				$ret_value = 1;
			}

		}
		else
		{
			$ret_value = 3;
		}

		// Set the response data
		$this->response_data = $this->response($ret_value, $this->error_messages);

	}

	/**
	 * Verify or unverify an existing report
	 * @param int report_id - the id of the report to be verified
	 * unverified.
	 */
	private function _verify_report()
	{
		$form = array('incident_id' => '', );

		$ret_value = 0;
		// Will hold the return value; start off with a "no error" value

		if ($_POST)
		{
			$post = Validation::factory($_POST);

			//  Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of
			//checks, carried out in order
			$post->add_rules('incident_id', 'required', 'numeric');

			if ($post->validate())
			{
				$incident_id = $post->incident_id;
				$update = new Incident_Model($incident_id);

				if ($update->loaded == true)
				{
					if ($update->incident_verified == '1')
					{
						$update->incident_verified = '0';
					}
					else
					{
						$update->incident_verified = '1';
					}
					$update->save();

					reports::verify_approve($update);
				}
				else
				{
					//TODO i18nize the string
					$this->error_messages .= "Could not verify this report " . $post->incident_id;
					$ret_value = 1;
				}

			}
			else
			{
				//TODO i18nize the string
				$this->error_messages .= "Incident ID is required.";
				$ret_value = 1;
			}

		}
		else
		{
			$ret_value = 3;
		}

		$this->response_data = $this->response($ret_value, $this->error_messages);

	}

	/**
	 * The actual reporting -
	 *
	 * @return int
	 */
	private function _submit_report()
	{
		// setup and initialize form field names
		$form = array(
		    'location_id' => '',
		    'incident_id' => '',
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
		    'person_email' => '',
		    'incident_active ' => '',
		    'incident_verified' => ''
		);

		$errors = $form;

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite 
			// $_POST fields with our own things
			$post = array_merge($_POST, $_FILES);
			$post['incident_category'] = explode(',', $post['incident_category']);

			// Action::report_submit_admin - Report Posted
			Event::run('ushahidi_action.report_submit_admin', $post);

			// Test to see if things passed the rule checks
			if (reports::validate($post))
			{
				// Yes! everything is valid
				$location_id = $post->location_id;

				// STEP 1: SAVE LOCATION
				$location = new Location_Model($location_id);
				reports::save_location($post, $location);

				// STEP 2: SAVE INCIDENT
				$incident_id = $post->incident_id;
				$incident = new Incident_Model($incident_id);
				reports::save_report($post, $incident, $location->id);

				// STEP 2b: Record Approval/Verification Action
				reports::verify_approve($incident);

				// STEP 2c: SAVE INCIDENT GEOMETRIES
				reports::save_report_geometry($post, $incident);

				// STEP 3: SAVE CATEGORIES
				reports::save_category($post, $incident);

				// STEP 4: SAVE MEDIA
				reports::save_media($post, $incident);

				// STEP 5: SAVE PERSONAL INFORMATION
				reports::save_personal_info($post, $incident);

				// Action::report_edit - Edited a Report
				Event::run('ushahidi_action.report_edit', $incident);

				// Success
				return $this->response(0);
			}
			else
			{
				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));

				foreach ($errors as $error_item => $error_description)
				{
					if ( ! is_array($error_description))
					{
						$this->error_messages .= $error_description;
						if ($error_description != end($errors))
						{
							$this->error_messages .= " - ";
						}
					}
				}

				//FAILED!!! //validation error
				return $this->response(1, $this->error_messages);
			}
		}
		else
		{
			// Not sent by post method.
			return $this->response(3);

		}
	}

}
