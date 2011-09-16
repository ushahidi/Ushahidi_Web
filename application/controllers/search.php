<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Search controller
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

class Search_Controller extends Main_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
    /**
  	 * Build a search query with relevancy
     * Stop word control included
     */
	public function index($page = 1) 
	{
		$this->template->content = new View('search');

		$search_query = "";
		$keyword_string = "";
		$where_string = "";
		$plus = "";
		$or = "";
		$search_info = "";
		$html = "";
		$pagination = "";
        
		// Stop words that we won't search for
		// Add words as needed!!
		$stop_words = array('the', 'and', 'a', 'to', 'of', 'in', 'i', 'is', 'that', 'it', 
			'on', 'you', 'this', 'for', 'but', 'with', 'are', 'have', 'be', 
			'at', 'or', 'as', 'was', 'so', 'if', 'out', 'not'
		);
        
		if ($_GET)
		{
			/**
			 * NOTES: 15/10/2010 - Emmanuel Kala <emmanuel@ushahidi.com>
			 *
			 * The search string undergoes a 3-phase sanitization process. This is not optimal
			 * but it works for now. The Kohana provided XSS cleaning mechanism does not expel
			 * content contained in between HTML tags this the "bruteforce" input sanitization.
			 *
			 * However, XSS is attempted using Javascript tags, Kohana's routing mechanism strips
			 * the "<script>" tags from the URL variables and passes inline text as part of the URL
			 * variable - This has to be fixed
			 */
              
			// Phase 1 - Fetch the search string and perform initial sanitization
			$keyword_raw = (isset($_GET['k']))? preg_replace('#/\w+/#', '', $_GET['k']) : "";

			// Phase 2 - Strip the search string of any HTML and PHP tags that may be present for additional safety              
			$keyword_raw = strip_tags($keyword_raw);

			// Phase 3 - Apply Kohana's XSS cleaning mechanism
			$keyword_raw = $this->input->xss_clean($keyword_raw);
		}
		else
		{
			$keyword_raw = "";
		}
		
		// Database instance
		$db = new Database();
		
		$keywords = explode(' ', $keyword_raw);
		if (is_array($keywords) AND !empty($keywords)) 
		{
			array_change_key_case($keywords, CASE_LOWER);
			$i = 0;
            
			foreach($keywords as $value)
			{
				if ( ! in_array($value,$stop_words) AND !empty($value))
				{
					// Escape the string for query safety
					$chunk = $db->escape_str($value);

					if ($i > 0)
					{
						$plus = ' + ';
						$or = ' OR ';
					}
                    
					// Give relevancy weighting
					// Title weight = 2
					// Description weight = 1
					$keyword_string = $keyword_string.$plus."(CASE WHEN incident_title LIKE '%$chunk%' THEN 2 ELSE 0 END) + "
										. "(CASE WHEN incident_description LIKE '%$chunk%' THEN 1 ELSE 0 END) ";
										
					$where_string = $where_string.$or."(incident_title LIKE '%$chunk%' OR incident_description LIKE '%$chunk%')";
					$i++;
				}
			}
            
			if ( ! empty($keyword_string) AND !empty($where_string))
			{
				// Limit the result set to only those reports that have been approved	
				$where_string .= ' AND incident_active = 1';
				$search_query = "SELECT *, (".$keyword_string.") AS relevance FROM "
								. $this->table_prefix."incident "
								. "WHERE ".$where_string." "
								. "ORDER BY relevance DESC LIMIT ";
			}
		}

		if ( ! empty($search_query))
		{
			// Pagination
			$pagination = new Pagination(array(
				'query_string' => 'page',
				'items_per_page' => (int) Kohana::config('settings.items_per_page'),
				'total_items' => ORM::factory('incident')->where($where_string)->count_all()
			));
            
			$query = $db->query($search_query . $pagination->sql_offset . ",". (int)Kohana::config('settings.items_per_page'));
            
			// Results Bar
			if ($pagination->total_items != 0)
			{
				$search_info .= "<div class=\"search_info\">"
							. Kohana::lang('ui_admin.showing_results')
							. ' '. ( $pagination->sql_offset + 1 )
							. ' '.Kohana::lang('ui_admin.to')
							. ' '.( (int) Kohana::config('settings.items_per_page') + $pagination->sql_offset )
							. ' '.Kohana::lang('ui_admin.of').' '. $pagination->total_items
							. ' '.Kohana::lang('ui_admin.searching_for').' <strong>'. $keyword_raw . "</strong>"
							. "</div>";
			}
			else
			{ 
				$search_info .= "<div class=\"search_info\">0 ".Kohana::lang('ui_admin.results')."</div>";

				$html .= "<div class=\"search_result\">";
				$html .= "<h3>".Kohana::lang('ui_admin.your_search_for')."<strong> ".$keyword_raw."</strong> ".Kohana::lang('ui_admin.match_no_documents')."</h3>";
				$html .= "</div>";

				$pagination = "";
			}
            
			foreach ($query as $search)
			{
				$incident_id = $search->id;
				$incident_title = $search->incident_title;
				$highlight_title = "";
				$incident_title_arr = explode(' ', $incident_title); 
                
				foreach($incident_title_arr as $value)
				{
					if (in_array(strtolower($value),$keywords) AND !in_array(strtolower($value),$stop_words))
					{
						$highlight_title .= "<span class=\"search_highlight\">" . $value . "</span> ";
					}
					else
					{
						$highlight_title .= $value . " ";
					}
				}
				
				$incident_description = $search->incident_description;

				// Remove any markup, otherwise trimming below will mess things up
				$incident_description = strip_tags($incident_description);

				// Trim to 180 characters without cutting words
				if ((strlen($incident_description) > 180) AND (strlen($incident_description) > 1))
				{
					$whitespaceposition = strpos($incident_description," ",175)-1;
					$incident_description = substr($incident_description, 0, $whitespaceposition);
				}
                
				$highlight_description = "";
				$incident_description_arr = explode(' ', $incident_description);

				foreach($incident_description_arr as $value)
				{
					if (in_array(strtolower($value),$keywords) && !in_array(strtolower($value),$stop_words))
					{
						$highlight_description .= "<span class=\"search_highlight\">" . $value . "</span> ";
					}
					else
					{
						$highlight_description .= $value . " ";
					}
				}
                
				$incident_date = date('D M j Y g:i:s a', strtotime($search->incident_date));

				$html .= "<div class=\"search_result\">";
				$html .= "<h3><a href=\"" . url::base() . "reports/view/" . $incident_id . "\">" . $highlight_title . "</a></h3>";
				$html .= $highlight_description . " ...";
				$html .= "<div class=\"search_date\">" . $incident_date . " | ".Kohana::lang('ui_admin.relevance').": <strong>+" . $search->relevance . "</strong></div>";
				$html .= "</div>";
			}
		}
		else
		{
			// Results Bar
			$search_info .= "<div class=\"search_info\">0 ".Kohana::lang('ui_admin.results')."</div>";

			$html .= "<div class=\"search_result\">";
			$html .= "<h3>".Kohana::lang('ui_admin.your_search_for')."<strong>".$keyword_raw."</strong> ".Kohana::lang('ui_admin.match_no_documents')."</h3>";
			$html .= "</div>";
		}
        
		$html .= $pagination;

		$this->template->content->search_info = $search_info;
		$this->template->content->search_results = $html;

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
    }
}
