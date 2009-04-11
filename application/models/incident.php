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
 * @module     Incident Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Incident_Model extends ORM
{
	protected $has_many = array('category' => 'incident_category', 'media', 'verify', 'comment',
		'rating', 'alert' => 'alert_sent', 'incident_lang', 'form_response');
	protected $has_one = array('location','incident_person','user','message','twitter','form');
	
	// Database table name
	protected $table_name = 'incident';
	
	static function get_active_categories()
	{
	    // Get all active categories
        $categories = array();
        foreach (ORM::factory('category')
                 ->where('category_visible', '1')
                 ->find_all() as $category)
        {
            // Create a list of all categories
            $categories[$category->id] = array($category->category_title, $category->category_color);
        }
        return $categories;
	}
	
	private static function category_graph_text($sql, $category)
    {
        $db = new Database();
        $query = $db->query($sql);
        $graph_data = array();
        $graph = ", \"".  $category[0] ."\": { label: '". $category[0] ."', ";
        foreach ( $query as $month_count )
        {
            array_push($graph_data, "[" . $month_count->time * 1000 . ", " . $month_count->number . "]");
        }
        $graph .= "data: [". join($graph_data, ",") . "], ";
        $graph .= "color: '#". $category[1] ."' ";
        $graph .= " } ";
        return $graph;
    }
	
	static function get_incidents_by_interval($interval='month',$start_date=NULL,$end_date=NULL,$active='true',$media_type=NULL) 
	{
	    // get graph data
        // could not use DB query builder. It does not support parentheses yet
        $db = new Database();
        
        $select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-01')";
        $groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m')";
        if ($interval == 'day') {
            $select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-%d')";
            $groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m%d')";
        } elseif ($interval == 'hour') {
            $select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-%d %H:%M')";
            $groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m%d%H')";
        } elseif ($interval == 'week') {
            $select_date_text = "STR_TO_DATE(CONCAT(CAST(YEARWEEK(incident_date) AS CHAR), ' Sunday'), '%X%V %W')";
            $groupby_date_text = "YEARWEEK(incident_date)";
        }
        
        $date_filter = "";
        if ($start_date) {
            $date_filter .= ' AND incident_date >= "' . $start_date . '"';
        }
        if ($end_date) {
            $date_filter .= ' AND incident_date <= "' . $end_date . '"';
        }
        
        $active_filter = '1';
        if ($active == 'all' || $active == 'false') {
        	$active_filter = '0,1';
        }
        
        $joins = '';
        $general_filter = '';
        if (isset($media_type) && is_numeric($media_type)) {
        	$joins = 'INNER JOIN media ON media.incident_id = incident.id';
        	$general_filter = ' AND media.media_type IN ('. $media_type  .')';
        }
        
        $graph_data = array();
        $all_graphs = "{ ";
		
        $all_graphs .= "\"ALL\": { label: 'All Categories', ";
        $query_text = 'SELECT UNIX_TIMESTAMP(' . $select_date_text . ') 
		                     AS time, COUNT(*) AS number 
		                     FROM incident ' . $joins . '
		                     WHERE incident_active IN (' . $active_filter .')' . $general_filter .'
		                     GROUP BY ' . $groupby_date_text;
        $query = $db->query($query_text);

        foreach ( $query as $month_count )
        {
            array_push($graph_data, "[" . $month_count->time * 1000 . ", " . $month_count->number . "]");
        }
        $all_graphs .= "data: [". join($graph_data, ",") . "], ";
        $all_graphs .= "color: '#990000' ";
        $all_graphs .= " } ";
		
        foreach ( self::get_active_categories() as $index => $category)
        {
            $query_text = 'SELECT UNIX_TIMESTAMP(' . $select_date_text . ') 
							AS time, COUNT(*) AS number
						        FROM incident 
							INNER JOIN incident_category ON incident_category.incident_id = incident.id
							' . $joins . '
							WHERE incident_active IN (' . $active_filter . ') AND 
							      incident_category.category_id = '. $index . $general_filter . '
							GROUP BY ' . $groupby_date_text;
		    $graph_text = self::category_graph_text($query_text, $category);
			$all_graphs .= $graph_text;
		}
		
	    $all_graphs .= " } ";
	    return $all_graphs;
	}
}
