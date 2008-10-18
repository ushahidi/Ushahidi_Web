<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to list/ view and edit reports
 */

class Reports_Controller extends Main_Controller {

    function __construct()
    {
        parent::__construct();	
    }

    /**
     * Displays all reports.
     */
    public function index() {}
    
    /**
	 * Submits a new report.
	 */
	public function submit()
	{
		$this->template->header->this_page = 'reports';
		$this->template->content = new View('reports_submit');
		
		// setup and initialize form field names
		$form = array
	    (
			'incident_title'      => '',
	        'incident_description'    => '',
	        'incident_date'  => '',
	        'incident_hour'      => '',
			'incident_minute'      => '',
			'incident_ampm' => '',
			'latitude' => '',
			'longitude' => '',
			'location_name' => '',
			'country_id' => '',
			'incident_category' => array(),
			'incident_news' => array(),
			'incident_video' => array(),
			'incident_photo' => array(),
			'person_first' => '',
			'person_last' => '',
			'person_email' => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		
		// Retrieve Country Cities
		$default_country = Kohana::config('settings.default_country');
		$this->template->content->cities = $this->_get_cities($default_country);
		
		$this->template->content->form = $form;
		$this->template->content->categories = $this->_get_categories();
		
		// Javascript Header
		$this->template->header->map_enabled = TRUE;
        $this->template->header->datepicker_enabled = TRUE;
		$this->template->header->js = new View('reports_submit_js');
		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->header->js->latitude = Kohana::config('settings.default_lat');
		$this->template->header->js->longitude = Kohana::config('settings.default_lon');
	}
	
	 /**
     * Displays a report.
     * @param boolean $id If id is supplied, a report with that id will be
     * retrieved.
     */
	public function view( $id = false )
	{
		$this->template->header->this_page = 'reports';
		$this->template->content = new View('reports_view');
		
        if ( !$id )
        {
            url::redirect('main');
        }
        else
        {
            $incident = ORM::factory('incident', $id);
			
            if ( $incident->id == 0 )	// Not Found
            {
                url::redirect('main');
            }

			// Comment Post?
			// setup and initialize form field names
			$form = array
		    (
		        'comment_author'      => '',
				'comment_description'      => '',
		        'comment_email'    => '',
		        'comment_ip'  => '',
				'captcha'  => ''
		    );
			$captcha = Captcha::factory(); 
			$errors = $form;
			$form_error = FALSE;
			
			// check, has the form been submitted, if so, setup validation
		    if ($_POST)
		    {
	            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
				$post = Validation::factory($_POST);

		         //  Add some filters
		        $post->pre_filter('trim', TRUE);
		
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('comment_author','required', 'length[3,100]');
				$post->add_rules('comment_description','required');
				$post->add_rules('comment_email','required','email', 'length[4,100]');
				$post->add_rules('captcha', 'required', 'Captcha::valid');
				
				// Test to see if things passed the rule checks
		        if ($post->validate())
		        {
	                // Yes! everything is valid
					$comment = new Comment_Model();
					$comment->incident_id = $id;
					$comment->comment_author = $post->comment_author;
					$comment->comment_description = $post->comment_description;
					$comment->comment_email = $post->comment_email;
					$comment->comment_ip = $_SERVER['REMOTE_ADDR'];
					$comment->comment_date = date("Y-m-d H:i:s",time());
					$comment->comment_active = 1;		// Activate comment for now
					$comment->save();
					
					// Redirect
					url::redirect('reports/view/' . $id);
				}

	            // No! We have validation errors, we need to show the form again, with the errors
		        else   
				{
		            // repopulate the form fields
		            $form = arr::overwrite($form, $post->as_array());

		            // populate the error fields, if any
		            $errors = arr::overwrite($errors, $post->errors('comments'));
					$form_error = TRUE;
		        }
			}
			
            $this->template->content->incident_id = $incident->id;
			$this->template->content->incident_title = $incident->incident_title;
            $this->template->content->incident_description = $incident->incident_description;
			$this->template->content->incident_rating = $incident->incident_rating;
            $this->template->content->incident_location = $incident->location->location_name;
            $this->template->content->incident_latitude = $incident->location->latitude;
            $this->template->content->incident_longitude = $incident->location->longitude;
			
            $this->template->content->incident_date = date('M j Y', strtotime($incident->incident_date));
            $this->template->content->incident_time = date('H:i', strtotime($incident->incident_date));
			
            // Retrieve Categories
            $incident_category = array();
            foreach($incident->incident_category as $category) 
            { 
                $incident_category[$category->category_id] = 
                    array($category->category->category_title, $category->category->category_color);
            }
			
            // Retrieve Media
            $incident_news = array();
            $incident_video = array();
            $incident_photo = array();
            
            //XXX: Replace magic numbers
            foreach($incident->media as $media) 
            {
                if ($media->media_type == 4)
                {
                    $incident_news[] = $media->media_link;
                }
                elseif ($media->media_type == 2)
                {
                    $incident_video[] = $media->media_link;
                }
                elseif ($media->media_type == 1)
                {
                    $incident_photo[] = $media->media_link;
                }
            }
			
            if ( $incident->incident_verified == 1 )
            {
                $this->template->content->incident_verified = "<p><strong class=\"green\">YES</strong></p>";
            }
            else
            {
                $this->template->content->incident_verified = "<p><strong class=\"red\">NO</strong></p>";
            }

			// Retrieve Comments (Additional Information)
			$this->template->content->incident_comments = $this->_get_comments($id);
        }
		
		// Javascript Header
		$this->template->header->map_enabled = TRUE;
		$this->template->header->js = new View('reports_view_js');
		$this->template->header->js->incident_id = $incident->id;
		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->header->js->latitude = $incident->location->latitude;
		$this->template->header->js->longitude = $incident->location->longitude;
		$this->template->header->js->incident_photos = $incident_photo;
		
		// Forms
		$this->template->content->form = $form;
		$this->template->content->captcha = $captcha;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
	}
	
		
	/**
     * Report Rating.
     * @param boolean $id If id is supplied, a rating will be applied to selected report
     */
	public function rating( $id = false )
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		if ( !$id )
        {
			echo json_encode(array("status"=>"error", "message"=>"ERROR!"));
		}
		else
		{
			if ( !empty($_POST['action']) && !empty($_POST['type']) ) {
				$action = $_POST['action'];
				$type = $_POST['type'];
				
				// Is this an ADD(+1) or SUBTRACT(-1)?
				if ($action == 'add') {
					$action = 1;
				}
				elseif ($action == 'subtract') {
					$action = -1;
				}
				else {
					$action = 0;
				}
				
				if (!empty($action) && ($type == 'original' || $type == 'comment'))
				{
					// Has this IP Address rated this post before?
					if ($type == 'original') {
						$previous = ORM::factory('rating')->where('incident_id',$id)->where('rating_ip',$_SERVER['REMOTE_ADDR'])->find();
					}
					elseif ($type == 'comment') {
						$previous = ORM::factory('rating')->where('comment_id',$id)->where('rating_ip',$_SERVER['REMOTE_ADDR'])->find();
					}
					
					$rating = new Rating_Model($previous->id);	// If previous exits... update previous vote
					// Are we rating the original post or the comments?
					if ($type == 'original') {
						$rating->incident_id = $id;
					}
					elseif ($type == 'comment') {
						$rating->comment_id = $id;
					}

					$rating->rating = $action;
					$rating->rating_ip = $_SERVER['REMOTE_ADDR'];
					$rating->rating_date = date("Y-m-d H:i:s",time());
					$rating->save();
					
					// Get total rating and send back to json
					$total_rating = $this->_get_rating($id, $type);
					
					echo json_encode(array("status"=>"saved", "message"=>"SAVED!", "rating"=>$total_rating));
				}
				else
				{
					echo json_encode(array("status"=>"error", "message"=>"ERROR!"));
				}
			}
			else
			{
				echo json_encode(array("status"=>"error", "message"=>"ERROR!"));
			}
		}
	}
	
	
    /*
	* Retrieves Previously Cached Geonames Cities
	*/
	private function _get_cities()
	{
		$cities = ORM::factory('city')->orderby('city', 'asc')->find_all();
		$city_select = array('' => 'Select A City');
		foreach ($cities as $city) {
			$city_select[$city->city_lon .  "," . $city->city_lat] = $city->city;
		}
		return $city_select;
	}
    
    //XXX: Move form html code to viewer	
	private function _get_categories()
	{
		// Count categories to determine column length
		$categories_total = ORM::factory('category')->where('category_visible', '1')->count_all();
        $this->template->content->categories_total = $categories_total;

		$categories = array();
		foreach (ORM::factory('category')->where('category_visible', '1')->find_all() as $category)
		{
			// Create a list of all categories
			$categories[$category->id] = array($category->category_title, $category->category_color);
		}

        //format categories for 2 column display
        $this_col = 1; // First column
        $max_col = round($categories_total/2); // Maximum number of columns
        $html= "";
        foreach ($categories as $category => $category_extra)
        {
            $category_title = $category_extra[0];
            $category_color = $category_extra[1];
            if ($this_col == 1) 
                $html.="<ul>";
        
            if (!empty($form['incident_category']) 
                && in_array($category, $form['incident_category'])) {
                $category_checked = TRUE;
            }
            else
            {
                $category_checked = FALSE;
            }
                                                                            
            $html.="\n<li><label>";
            $html.=form::checkbox('incident_category[]', $category, $category_checked, ' class="check-box"');
            $html.="$category_title";
            $html.="</label></li>";
       
            if ($this_col == $max_col) 
                $html.="\n</ul>\n";
      
            if ($this_col < $max_col)
            {
                $this_col++;
            } 
            else 
            {
                $this_col = 1;
            }
        }
        return $html;
	}
	
	
	/*
	* Retrieves Comments
	*/
	private function _get_comments($id)
	{
		if ($id)
		{
			$html = "";
			foreach(ORM::factory('comment')->where('incident_id',$id)->where('comment_active','1')->orderby('comment_date', 'asc')->find_all() as $comment)
			{
				$html .= "<div class=\"discussion-box\">";
				$html .= "<p><strong>" . $comment->comment_author . "</strong>&nbsp;(" . date('M j Y', strtotime($comment->comment_date)) . ")</p>";
				$html .= "<p>" . $comment->comment_description . "</p>";
				$html .= "<div class=\"report_rating\">";
				$html .= "	<div>";
				$html .= "	Credibility:&nbsp;";
				$html .= "	<a href=\"javascript:rating('" . $comment->id . "','add','comment','cloader_" . $comment->id . "')\"><img id=\"cup_" . $comment->id . "\" src=\"" . url::base() . 'media/img/' . "up.png\" alt=\"UP\" title=\"UP\" border=\"0\" /></a>&nbsp;";
				$html .= "	<a href=\"javascript:rating('" . $comment->id . "','subtract','comment','cloader_" . $comment->id . "')\"><img id=\"cdown_" . $comment->id . "\" src=\"" . url::base() . 'media/img/' . "down.png\" alt=\"DOWN\" title=\"DOWN\" border=\"0\" /></a>&nbsp;";
				$html .= "	</div>";
				$html .= "	<div class=\"rating_value\" id=\"crating_" . $comment->id . "\">" . $comment->comment_rating . "</div>";
				$html .= "	<div id=\"cloader_" . $comment->id . "\" class=\"rating_loading\" ></div>";
				$html .= "</div>";
				$html .= "</div>";
			}
			
			return $html;
		}
	}
	
	
	/*
	* Retrieves Total Rating For Specific Post
	* Also Updates The Incident & Comment Tables (Ratings Column)
	*/
	private function _get_rating($id = false, $type = NULL)
	{
		if (!empty($id) && ($type == 'original' || $type == 'comment'))
		{
			if ($type == 'original') {
				$which_count = 'incident_id';
			} elseif ($type == 'comment') {
				$which_count = 'comment_id';
			}
			else {
				return 0;
			}
			
			$total_rating = 0;
			// Get All Ratings and Sum them up
			foreach(ORM::factory('rating')->where($which_count,$id)->find_all() as $rating)
			{
				$total_rating += $rating->rating;
			}
			
			// Update Counts
			if ($type == 'original') {
				$incident = ORM::factory('incident', $id);
				if ($incident->loaded==true)
				{
					$incident->incident_rating = $total_rating;
					$incident->save();
				}
			} elseif ($type == 'comment') {
				$comment = ORM::factory('comment', $id);
				if ($comment->loaded==true)
				{
					$comment->comment_rating = $total_rating;
					$comment->save();
				}
			}
			
			return $total_rating;
			
		} else {
			return 0;
		}

	}


} // End Main

