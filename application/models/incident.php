<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for reported Incidents
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Incident_Model extends ORM {
	/**
	 * One-to-may relationship definition
	 * @var array
	 */
	protected $has_many = array(
		'category' => 'incident_category',
		'media',
		'verify',
		'comment',
		'rating',
		'alert' => 'alert_sent',
		'incident_lang',
		'form_response',
		'cluster' => 'cluster_incident',
		'geometry'
	);

	/**
	 * One-to-one relationship definition
	 * @var array
	 */
	protected $has_one = array(
		'location',
		'incident_person',
		'user',
		'message',
		'twitter',
		'form'
	);

	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'incident';

	/**
	 * Prevents cached items from being reloaded
	 * @var bool
	 */
	protected $reload_on_wakeup   = FALSE;

	/**
	 * Gets a list of all visible categories
	 * @todo Move this to the category model
	 * @return array
	 */
	public static function get_active_categories()
	{
		// Get all active categories
		$categories = array();
		foreach
		(
			ORM::factory('category')
			    ->where('category_visible', '1')
			    ->find_all() as $category)
		{
			// Create a list of all categories
			$categories[$category->id] = array(
				$category->category_title, 
				$category->category_color
			);
		}
		return $categories;
	}

	/**
	 * Get the total number of reports
	 *
	 * @param boolean $approved - Only count approved reports if true
	 * @return int
	 */
	public static function get_total_reports($approved = FALSE)
	{
		return ($approved)
			? ORM::factory('incident')->where('incident_active', '1')->count_all()
			: ORM::factory('incident')->count_all();
	}

	/**
	 * Get the total number of verified or unverified reports
	 *
	 * @param boolean $verified - Only count verified reports if true, unverified if false
	 * @return int
	 */
	public static function get_total_reports_by_verified($verified = FALSE)
	{
		return ($verified)
			? ORM::factory('incident')->where('incident_verified', '1')->where('incident_active', '1')->count_all()
			: ORM::factory('incident')->where('incident_verified', '0')->where('incident_active', '1')->count_all();
	}

	/**
	 * Get the earliest report date
	 *
	 * @param boolean $approved - Oldest approved report timestamp if true (oldest overall if false)
	 * @return string
	 */
	public static function get_oldest_report_timestamp($approved = TRUE)
	{
		$result = ($approved)
			? ORM::factory('incident')->where('incident_active', '1')->orderby(array('incident_date'=>'ASC'))->find_all(1,0)
			: ORM::factory('incident')->where('incident_active', '0')->orderby(array('incident_date'=>'ASC'))->find_all(1,0);

		foreach($result as $report)
		{
			return strtotime($report->incident_date);
		}
	}

	/**
	 * Get the latest report date
	 * @return string
	 */
	public static function get_latest_report_timestamp($approved = TRUE)
	{
		$result = ($approved)
			? ORM::factory('incident')->where('incident_active', '1')->orderby(array('incident_date'=>'DESC'))->find_all(1,0)
			: ORM::factory('incident')->where('incident_active', '0')->orderby(array('incident_date'=>'DESC'))->find_all(1,0);

		foreach($result as $report)
		{
			return strtotime($report->incident_date);
		}
	}

	/**
	 * Get the number of reports by date for dashboard chart
	 *
	 * @param int $range No. of days in the past
	 * @param int $user_id
	 * @return array
	 */
	public static function get_number_reports_by_date($range = NULL, $user_id = NULL)
	{
		// Table Prefix
		$table_prefix = Kohana::config('database.default.table_prefix');

		// Database instance
		$db = new Database();

		$params = array();
		
		$db->select(
				'COUNT(id) as count',
				'DATE(incident_date) as date',
				'MONTH(incident_date) as month',
				'DAY(incident_date) as day',
				'YEAR(incident_date) as year'
			)
			->from('incident')
			->groupby('date')
			->orderby('incident_date', 'ASC');
		
		if (!empty($user_id))
		{
			$db->where('user_id', $user_id);
		}
		
		if (!empty($range))
		{
			// Use Database_Expression to sanitize range param
			$range_expr = new Database_Expression('incident_date  >= DATE_SUB(CURDATE(), INTERVAL :range DAY)', array(':range' => (int)$range));
			$db->where(
				$range_expr->compile()
			);
		}
		$query = $db->get();
		$result = $query->result_array(FALSE);

		$array = array();
		foreach ($result AS $row)
		{
			$timestamp = mktime(0, 0, 0, $row['month'], $row['day'], $row['year']) * 1000;
			$array["$timestamp"] = $row['count'];
		}

		return $array;
	}

	/**
	 * Gets a list of dates of all approved incidents
	 *
	 * @return array
	 */
	public static function get_incident_dates()
	{
		//$incidents = ORM::factory('incident')->where('incident_active',1)->incident_date->find_all();
		$incidents = ORM::factory('incident')->where('incident_active',1)->select_list('id', 'incident_date');
		$array = array();
		foreach ($incidents as $id => $incident_date)
		{
			$array[] = $incident_date;
		}
		return $array;
	}

	/**
	 * Checks if a specified incident id is numeric and exists in the database
	 *
	 * @param int $incident_id ID of the incident to be looked up
	 * @param bool $approved Whether to include un-approved reports
	 * @return bool
	 */
	public static function is_valid_incident($incident_id, $approved = TRUE)
	{
		$where = ($approved == TRUE) ? array("incident_active" => "1") : array("id >" => 0);
		return (intval($incident_id) > 0)
			? ORM::factory('incident')->where($where)->find(intval($incident_id))->loaded
			: FALSE;
	}

	/**
	 * Gets the reports that match the conditions specified in the $where parameter
	 * The conditions must relate to columns in the incident, location, incident_category
	 * category and media tables
	 *
	 * @param array $where List of conditions to apply to the query
	 * @param mixed $limit No. of records to fetch or an instance of Pagination
	 * @param string $order_field Column by which to order the records
	 * @param string $sort How to order the records - only ASC or DESC are allowed
	 * @return Database_Result
	 */
	public static function get_incidents($where = array(), $limit = NULL, $order_field = NULL, $sort = NULL, $count = FALSE)
	{
		// Get the table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');

		// To store radius parameters
		$radius = array();
		$having_clause = "";
		if (array_key_exists('radius', $where))
		{
			// Grab the radius parameter
			$radius = $where['radius'];

			// Delete radius parameter from the list of predicates
			unset ($where['radius']);
		}

		// Query
		// Normal query
		if (! $count)
		{
			$sql = 'SELECT DISTINCT i.id incident_id, i.incident_title, i.incident_description, i.incident_date, i.incident_mode, i.incident_active, '
				. 'i.incident_verified, i.location_id, l.country_id, l.location_name, l.latitude, l.longitude ';
		}
		// Count query
		else
		{
			$sql = 'SELECT COUNT(DISTINCT i.id) as report_count ';
		}
		
		// Check if all the parameters exist
		if (count($radius) > 0 AND array_key_exists('latitude', $radius) AND array_key_exists('longitude', $radius)
			AND array_key_exists('distance', $radius))
		{
			// Calculate the distance of each point from the starting point
			$sql .= ", ((ACOS(SIN(%s * PI() / 180) * SIN(l.`latitude` * PI() / 180) + COS(%s * PI() / 180) * "
				. "	COS(l.`latitude` * PI() / 180) * COS((%s - l.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance ";

			$sql = sprintf($sql, $radius['latitude'], $radius['latitude'], $radius['longitude']);

			// Set the "HAVING" clause
			$having_clause = "HAVING distance <= ".intval($radius['distance'])." ";
		}

		$sql .=  'FROM '.$table_prefix.'incident i '
			. 'LEFT JOIN '.$table_prefix.'location l ON (i.location_id = l.id) '
			. 'LEFT JOIN '.$table_prefix.'incident_category ic ON (ic.incident_id = i.id) '
			. 'LEFT JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) ';
		
		// Check if the all reports flag has been specified
		if (array_key_exists('all_reports', $where) AND $where['all_reports'] == TRUE)
		{
			unset ($where['all_reports']);
			$sql .= 'WHERE 1=1 ';
		}
		else
		{
			$sql .= 'WHERE i.incident_active = 1 ';
		}

		// Check for the additional conditions for the query
		if ( ! empty($where) AND count($where) > 0)
		{
			foreach ($where as $predicate)
			{
				$sql .= 'AND '.$predicate.' ';
			}
		}

		// Might need "GROUP BY i.id" do avoid dupes
		
		// Add the having clause
		$sql .= $having_clause;

		// Check for the order field and sort parameters
		if ( ! empty($order_field) AND ! empty($sort) AND (strtoupper($sort) == 'ASC' OR strtoupper($sort) == 'DESC'))
		{
			$sql .= 'ORDER BY '.$order_field.' '.$sort.' ';
		}
		else
		{
			$sql .= 'ORDER BY i.incident_date DESC ';
		}

		// Check if the record limit has been specified
		if ( ! empty($limit) AND is_int($limit) AND intval($limit) > 0)
		{
			$sql .= 'LIMIT 0, '.$limit;
		}
		elseif ( ! empty($limit) AND $limit instanceof Pagination_Core)
		{
			$sql .= 'LIMIT '.$limit->sql_offset.', '.$limit->items_per_page;
		}
		
		// Event to alter SQL
		Event::run('ushahidi_filter.get_incidents_sql', $sql);

		// Kohana::log('debug', $sql);
		return Database::instance()->query($sql);
	}

	/**
	 * Gets the comments for an incident
	 * @param int $incident_id Database ID of the incident
	 * @return mixed FALSE if the incident id is non-existent, ORM_Iterator if it exists
	 */
	public static function get_comments($incident_id)
	{
		if (self::is_valid_incident($incident_id))
		{
			$where = array(
				'comment.incident_id' => $incident_id,
				'comment_active' => '1',
				'comment_spam' => '0'
			);

			// Fetch the comments
			return ORM::factory('comment')
					->where($where)
					->orderby('comment_date', 'asc')
					->find_all();
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Given an incident, gets the list of incidents within a specified radius
	 *
	 * @param int $incident_id Database ID of the incident to be used to fetch the neighbours
	 * @param int $distance Radius within which to fetch the neighbouring incidents
	 * @param int $num_neigbours Number of neigbouring incidents to fetch
	 * @return mixed FALSE is the parameters are invalid, Result otherwise
	 */
	public static function get_neighbouring_incidents($incident_id, $order_by_distance = FALSE, $distance = 0, $num_neighbours = 100)
	{
		if (self::is_valid_incident($incident_id))
		{
			// Get the table prefix
			$table_prefix = Kohana::config('database.default.table_prefix');

			$incident_id = (intval($incident_id));

			// Get the location object and extract the latitude and longitude
			$location = self::factory('incident', $incident_id)->location;
			$latitude = $location->latitude;
			$longitude = $location->longitude;

			// Garbage collection
			unset ($location);

			// Query to fetch the neighbour
			$sql = "SELECT DISTINCT i.*, l.`latitude`, l.`longitude`, l.location_name, "
				. "((ACOS(SIN( :lat * PI() / 180) * SIN(l.`latitude` * PI() / 180) + COS( :lat * PI() / 180) * "
				. "	COS(l.`latitude` * PI() / 180) * COS(( :lon - l.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance "
				. "FROM `".$table_prefix."incident` AS i "
				. "INNER JOIN `".$table_prefix."location` AS l ON (l.`id` = i.`location_id`) "
				. "WHERE i.incident_active = 1 "
				. "AND i.id <> :incidentid ";

			// Check if the distance has been specified
			if (intval($distance) > 0)
			{
				$sql .= "HAVING distance <= :distance ";
			}

			// If the order by distance parameter is TRUE
			if ($order_by_distance)
			{
				$sql .= "ORDER BY distance ASC ";
			}
			else
			{
				$sql .= "ORDER BY i.`incident_date` DESC ";
			}

			// Has the no. of neigbours been specified
			if (intval($num_neighbours) > 0)
			{
				$sql .= "LIMIT :limit";
			}
		
			// Event to alter SQL
			Event::run('ushahidi_filter.get_neighbouring_incidents_sql', $sql);

			// Fetch records and return
			return Database::instance()->query($sql,
				array(':lat' => $latitude, ':lon' => $longitude, ':incidentid' => $incident_id, ':limit' => (int)$num_neighbours, ':distance' => (int)$distance)
			);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Sets approval of an incident
	 * @param int $incident_id
	 * @param int $val Set to 1 or 0 for approved or not approved
	 * @return bool
	 */
	public static function set_approve($incident_id,$val)
	{
		$incident = ORM::factory('incident',$incident_id);
		$incident->incident_active = $val;
		return $incident->save();
	}

	/**
	 * Sets incident as verified or not
	 * @param int $incident_id
	 * @param int $val Set to 1 or 0 for verified or not verified
	 * @return bool
	 */
	public static function set_verification($incident_id,$val)
	{
		$incident = ORM::factory('incident',$incident_id);
		$incident->incident_verified = $val;
		return $incident->save();
	}

	/**
	 * Overrides the default delete method for the ORM.
	 * Deletes all other content related to the incident - performs
	 * an SQL destroy
	 */
	public function delete()
	{
		// Delete Location
		ORM::factory('location')
			->where('id', $this->location_id)
			->delete_all();

		// Delete Categories
		ORM::factory('incident_category')
		    ->where('incident_id', $this->id)
		    ->delete_all();

		// Delete Translations
		ORM::factory('incident_lang')
		    ->where('incident_id', $this->id)
		    ->delete_all();

		// Delete Photos From Directory
		$photos = ORM::factory('media')
				      ->where('incident_id', $this->id)
				      ->where('media_type', 1)
				      ->find_all();
		
		foreach ($photos as $photo)
		{
			Media_Model::delete_photo($photo->id);
		}

		// Delete Media
		ORM::factory('media')
		    ->where('incident_id', $this->id)
		    ->delete_all();

		// Delete Sender
		ORM::factory('incident_person')
		    ->where('incident_id', $this->id)
		    ->delete_all();

		// Delete relationship to SMS message
		$updatemessage = ORM::factory('message')
						     ->where('incident_id', $this->id)
						     ->find();

		if ($updatemessage->loaded)
		{
			$updatemessage->incident_id = 0;
			$updatemessage->save();
		}

		// Delete Comments
		ORM::factory('comment')
			->where('incident_id', $this->id)
			->delete_all();
			
		// Delete ratings
		ORM::factory('rating')
			->where('incident_id', $this->id)
			->delete_all();

		$incident_id = $this->id;

		// Action::report_delete - Deleted a Report
		Event::run('ushahidi_action.report_delete', $incident_id);

		parent::delete();
	}

	/**
	 * Get url of this incident
	 * @return string
	 **/
	public function url()
	{
		return self::get_url($this);
	}
	
	/**
	 * Get url for the incident object passed
	 * @param object|int
	 * @return string
	 **/
	public static function get_url($incident)
	{
		if (is_object($incident))
		{
			$id = isset($incident->incident_id) ? $incident->incident_id : $incident->id;
		}
		elseif (intval($incident) > 0)
		{
			$id = intval($incident);
		}
		else
		{
			return false;
		}
		
		return url::site('reports/view/'.$id);
	}

}