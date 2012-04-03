<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Markers Controller
 * Generates KML File
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Markers_Controller extends Template_Controller
{
    public $auto_render = TRUE;
    
    // Main template
    public $template = 'markers';
    
    function index( $category_id = 0, $start_date = NULL, $end_date = NULL )
    {       
        $placemarks = "";
        $style_map = "";
        
        
        // Do we have a category id to filter by?
        if ( is_numeric($category_id) && $category_id != 0 )
        {
            $filter = 'incident.incident_active = 1 AND incident_category.category_id = ' . $category_id;
            $filter_cat = 'category_visible = 1 AND id = ' . $category_id;
        }
        else
        {
            $filter = 'incident.incident_active = 1';
            $filter_cat = 'category_visible = 1';
            $category_id = 0;
        }
        
        // Do we have dates to filter by?
        if ( strptime($start_date, '%G-%m-%d') && strptime($end_date, '%G-%m-%d') )
        {
            $filter .= ' AND incident_date BETWEEN \'' . $start_date . ' 00:00:00\' AND  \'' . $end_date . ' 23:59:59\' ';
        }
        
        
        // Retrieve individual markers
        foreach (ORM::factory('incident')->join('incident_category', 'incident.id', 'incident_category.incident_id','INNER')->select('incident.*')->where($filter)->orderby('incident.incident_dateadd', 'desc')->find_all() as $marker)
        {
            $placemarks .= "<Placemark>\n";
            $placemarks .= "<name>" . htmlentities("<a href=\"" . url::base() . "reports/view/" . $marker->id . "\">" . $marker->incident_title . "</a>") . "</name>\n";
            $placemarks .= "<description>" . htmlentities($marker->incident_description) . "</description>\n";
            $placemarks .= "<Point>\n";
            $placemarks .= "<coordinates>" . htmlentities($marker->location->longitude . "," . $marker->location->latitude) . "</coordinates>\n";
            $placemarks .= "</Point>\n";
            if ( $category_id != 0 )
            {
                $placemarks .= "<styleUrl>#" . $category_id . "</styleUrl>\n";
            }
            else
            {
                foreach($marker->incident_category as $category) 
                {
                    $placemarks .= "<styleUrl>#" . $category->category->id . "</styleUrl>\n";
                    break 1;
                }
            }
            $placemarks .= "<TimeStamp>\n";
            $placemarks .= "<when>" . date('Y-m-d', strtotime($marker->incident_date)) . "T" . date('H:i:s', strtotime($marker->incident_date)) . "-05:00" . "</when>\n";
            $placemarks .= "</TimeStamp>\n";
            $placemarks .= "</Placemark>\n";
        }
        
        // Create Stylemap From individual categories
        foreach (ORM::factory('category')->where($filter_cat)->orderby('category_title', 'asc')->find_all() as $category)
        {
            $style_map .= "<Style id=\"" . $category->id  . "\">\n";
            $style_map .= "<IconStyle>\n";
            $style_map .= "<scale>0.5</scale>\n";
            $style_map .= "<Icon>\n";
            $style_map .= "<href>" . htmlentities(url::base() . "swatch/?c=" . $category->category_color . "&w=16&h=16&.png") . "</href>\n";
            $style_map .= "</Icon>\n";
            $style_map .= "</IconStyle>\n";
            $style_map .= "</Style>\n";
        }
        
        
        $this->template->placemarks = $placemarks;
        $this->template->style_map = $style_map;
            
    }
    
    
    // Generate KML for single point
    function single ( $incident_id = false )
    {
        $placemarks = "";
        $style_map = "";
        $filter = 'incident.incident_active = 1';
        
        $incident_id = (int) $incident_id;  
        
        // Retrieve individual markers
        foreach (ORM::factory('incident')->join('incident_category', 'incident.id', 'incident_category.incident_id','INNER')->select('incident.*')->where($filter)->orderby('incident.incident_dateadd', 'desc')->find_all() as $marker)
        {
            $placemarks .= "<Placemark>\n";
            $placemarks .= "<name>" . htmlentities("<a href=\"" . url::base() . "reports/view/" . $marker->id . "\">" . $marker->incident_title . "</a>") . "</name>\n";
            $placemarks .= "<description>" . htmlentities($marker->incident_description) . "</description>\n";
            $placemarks .= "<Point>\n";
            $placemarks .= "<coordinates>" . htmlentities($marker->location->longitude . "," . $marker->location->latitude) . "</coordinates>\n";
            $placemarks .= "</Point>\n";
            
            if ( $marker->id == $incident_id )
            {
                $placemarks .= "<styleUrl>#1</styleUrl>\n";
            }
            else
            {
                $placemarks .= "<styleUrl>#2</styleUrl>\n";
            }
            $placemarks .= "<TimeStamp>\n";
            $placemarks .= "<when>" . date('Y-m-d', strtotime($marker->incident_date)) . "T" . date('H:i:s', strtotime($marker->incident_date)) . "-05:00" . "</when>\n";
            $placemarks .= "</TimeStamp>\n";
            $placemarks .= "</Placemark>\n";
        }
        
        // Create Styles        
        $style_map .= "<Style id=\"1\">\n";
        $style_map .= "<IconStyle>\n";
        $style_map .= "<scale>0.5</scale>\n";
        $style_map .= "<Icon>\n";
        $style_map .= "<href>" . htmlentities(url::base() . "swatch/?c=CC0000&w=16&h=16&.png") . "</href>\n";
        $style_map .= "</Icon>\n";
        $style_map .= "</IconStyle>\n";
        $style_map .= "</Style>\n";
        
        $style_map .= "<Style id=\"2\">\n";
        $style_map .= "<IconStyle>\n";
        $style_map .= "<scale>0.5</scale>\n";
        $style_map .= "<Icon>\n";
        $style_map .= "<href>" . htmlentities(url::base() . "swatch/?c=FF9933&w=16&h=16&.png") . "</href>\n";
        $style_map .= "</Icon>\n";
        $style_map .= "</IconStyle>\n";
        $style_map .= "</Style>\n";
    
        
        $this->template->placemarks = $placemarks;
        $this->template->style_map = $style_map;
    }
}
