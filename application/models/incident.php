<?php defined('SYSPATH') or die('No direct script access.');

/**
* Report/Incidents Table Model
*/

class Incident_Model extends ORM
{
	protected $has_many = array('category' => 'incident_category', 'media', 'verify', 'comment', 'rating', 'alert' => 'alert_sent', 'incident_lang');
	protected $has_one = array('location','incident_person','user','message');
	
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
	
	static function get_incidents_by_interval($interval='month',$start_date=NULL,$end_date=NULL) 
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
        
        $graph_data = array();
        $all_graphs = "{ ";
		
        $all_graphs .= "\"ALL\": { label: 'All Categories', ";
        $query_text = 'SELECT UNIX_TIMESTAMP(' . $select_date_text . ') 
		                     AS time, COUNT(*) AS number 
		                     FROM incident 
		                     WHERE incident_active = 1
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
							WHERE incident_active = 1 AND incident_category.category_id = '. $index .'
							GROUP BY ' . $groupby_date_text;
		    $graph_text = self::category_graph_text($query_text, $category);
			$all_graphs .= $graph_text;
		}
		
	    $all_graphs .= " } ";
	    return $all_graphs;
	}
}
