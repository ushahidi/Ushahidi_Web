<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This class handles posting report to ushahidi via the API.
 *
 * @version 24 - Emmanuel Kala 2010-10-25
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
class Report_Api_Object extends Api_Object_Core {

    private $error_string = ''; // To hold the string of error messages
    
    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }

    /**
     * Services the request for reporting an incident via the API
     */
    public function perform_task()
    {
  		// If user doesn't have member perms and allow_reports is disabled, Throw auth error
  		if ( ! Kohana::config('settings.allow_reports') AND ! $this->api_service->_login(FALSE, TRUE) )
			{
				$this->set_error_message($this->response(2));
				return;
			}
			
        $ret_value = $this->_submit();
        
        $this->response_data =  $this->response($ret_value, $this->error_string);
    }
    
    /**
     * The actual reporting -
     *
     * @return int
     */
    private function _submit() 
    {
        // Setup and initialize form field names
        $form = array(
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
            'person_email' => ''
        );
        
        $this->messages = $form;
        
        // Check for HTTP POST, setup validation
        if ($_POST) 
        {
            // Instantiate Validation, use $post, so we don't overwrite 
            // $_POST fields with our own things
            $post = array_merge($_POST, $_FILES);
            $post['incident_category'] = explode(',', $post['incident_category']);

            // 
            // EK <emmanuel@ushahidi.com> - 17/05/2012
            // Commenting out this event ('ushahidi_action.report_submit_api') because
            // of the following:
            // The 'ushahidi_action.report_submit' and 'ushahidi_action.report_add'
            // events should suffice for all plugins that wish to run extra
            // operations once a report has been submitted and saved - avoid
            // superfluous events
            // 

            // In case there's a plugin that would like to know about 
            // this new incident, I mean report
            // Event::run('ushahidi_action.report_submit_api', $post);

            if (reports::validate($post))
            {
                // STEP 1: SAVE LOCATION
                $location = new Location_Model();
                reports::save_location($post, $location);

                // STEP 2: SAVE INCIDENT
                $incident = new Incident_Model();
                reports::save_report($post, $incident, $location->id);

                // STEP 2b: SAVE INCIDENT GEOMETRIES
                reports::save_report_geometry($post, $incident);

                // STEP 3: SAVE CATEGORIES
                reports::save_category($post, $incident);

                // STEP 4: SAVE MEDIA
                reports::save_media($post, $incident);

                // STEP 5: SAVE CUSTOM FORM FIELDS
                reports::save_custom_fields($post, $incident);

                // STEP 6: SAVE PERSONAL INFORMATION
                reports::save_personal_info($post, $incident);

                // Run events
                Event::run('ushahidi_action.report_submit', $post);
                Event::run('ushahidi_action.report_add', $incident);
                
                // Action::report_edit_api - Edited a Report
                Event::run('ushahidi_action.report_edit_api', $incident);

                // Success
                return 0;

            } 
            else 
            {               
                // Populate the error fields, if any
                $this->messages = arr::overwrite($this->messages, 
                        $post->errors('report'));

                foreach ($this->messages as $error_item => $error_description) 
                {
                    if( ! is_array($error_description)) 
                    {
                        $this->error_string .= $error_description;
                        
                        if ($error_description != end($this->messages)) 
                        {
                            $this->error_string .= " - ";
                        }
                    }
                }

                //FAILED!!!
                return 1; //validation error
            }
        } 
        else 
        {
            return 3; // Not sent by post method.
        }
    }

}
