<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Generate Data Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Generate Date Settings Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Generatedata_Settings_Controller extends Admin_Controller
{
	public function index()
	{
		$this->template->this_page = 'addons';
		
		// Standard Settings View
		$this->template->content = new View("admin/plugins_settings");
		$this->template->content->title = "Generate Data Settings";
		
		// Settings Form View
		$this->template->content->settings_form = new View("generatedata/admin/generatedata_settings");
		
		// setup and initialize form field names
        $form = array
        (
            'thismanyreports' => ''
        );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;

        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field, followed by a list of checks, carried out in order

            $post->add_rules('thismanyreports','required', 'numeric');

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Yes! everything is valid
                // CREATE REPORTS HERE
                
                // Just double check it's a positive integer value
                $num_make = abs((int)$post->thismanyreports);
                
                $categories = Category_Model::categories();
                
                $cat_arr = array();
                foreach($categories as $cat)
                {
                	$cat_arr[] = $cat['category_id'];
                }
                
                $i = 0;
                while($i < $num_make)
                {
                	$cat = $cat_arr[array_rand($cat_arr)];
                	$lat = rand(-90,90);
                	$lon = rand(-180,180);
                	$latlon = $lat.','.$lon;
                	$rand_title = Generatedata_Settings_Controller::rand_string(mt_rand(5,40));
                	$rand_description = Generatedata_Settings_Controller::rand_string(mt_rand(15,1000));
                	
                	
          
                	// Create Location
                	$location = new Location_Model();
	                $location->location_name = $latlon;
	                $location->latitude = $lat;
	                $location->longitude = $lon;
	                $location->location_date = date("Y-m-d H:i:s",time());
	                $location->save();
	                
	                // Create Report
	                $incident = new Incident_Model();
		            $incident->location_id = $location->id;
		            $incident->form_id = 1;
		            $incident->user_id = $_SESSION['auth_user']->id;
		            $incident->incident_title = $rand_title;
		            $incident->incident_description = $rand_description;
		            // Spread out over 6 months
                	$incident->incident_date = date( "Y-m-d H:i:s", (time()+mt_rand(-15778463,15778463)) );
                	$incident->incident_dateadd = date("Y-m-d H:i:s", time());
                	$incident->incident_active = 1;
                	// Randomly decide if a report is verified
		            $incident->incident_verified = mt_rand(0,1);
		            //Save
		            $incident->save();
                	
                	// Set Category (only setting one category per report)
                	$incident_category = new Incident_Category_Model();
                    $incident_category->incident_id = $incident->id;
                    $incident_category->category_id = $cat;
                    $incident_category->save();

                	$i++;
                }
				
                // Everything is A-Okay!
                $form_saved = TRUE;

                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

            }

            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('settings'));
                $form_error = TRUE;
            }
        }
        else
        {
            // Set default to 1

            $form = array
            (
                'thismanyreports' => '1'
            );
        }
		
		// Pass the $form on to the settings_form variable in the view
		$this->template->content->settings_form->form = $form;
		
		// Other variables
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
	}
	
	public function rand_string($lenth) {
		// makes a random alpha numeric string of a given lenth
		$aZ09 = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9));
		$out ='';
		for($c=0;$c < $lenth;$c++) {
			if(mt_rand(0,6) == 2) {
				$out .= ' ';
			}
			$out .= $aZ09[mt_rand(0,count($aZ09)-1)];
		}
		return $out;
	} 
}