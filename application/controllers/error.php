<?php defined('SYSPATH') or die('No direct script access.');
/**
* Custom 404 Error Page Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Error Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Error_Controller extends Exception
{
	/**
	 * Render Custom 404 Error Page
	 */
	public function error_404()
	{
		Header("HTTP/1.0 404 Not Found");
		
		$this->layout = new View('error');
		$this->layout->title = Kohana::lang('ui_admin.page_not_found');
		$this->layout->content = Kohana::lang('ui_admin.page_not_found_message');
		$this->layout->render(true);
	}
	
	/**
	 * Post Bug Back to Ushahidi
	 */	
	public function bug()
	{
		// setup and initialize form field names
		$form = array
	    (
			'subject' => '',
			'yourname' => '',
			'email' => '',
			'description' => '',
			'error' => '',
			'environ' => '',
			'captcha' => ''
	    );

        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
		$form_error = FALSE;

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

	        // Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('subject', 'required', 'length[1,160]');
			$post->add_rules('yourname', 'required', 'length[1,160]');
			$post->add_rules('error', 'required');
			$post->add_rules('email', 'required','email', 'length[4,100]');
			$post->add_rules('captcha', 'required', 'Captcha::valid');
			
			$version = "";
			$version_db = "";
			$settings = ORM::factory('settings', 1)->find();
			if ($settings->loaded)
			{
				$version = $settings->ushahidi_version;
				$version_db = $settings->db_version;
			}

			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
				// Yes! everything is valid
				$message = "";
				$message .= "*From*.......: ".$post->yourname."\n";
				$message .= "*Title*......: ".$post->subject."\n\n";
				$message .= "*Version*....: ".$version.", *DB*: ".$version_db."\n\n";
				$message .= "*Message*:\n-----------\n".$description."\n\n";
				$message .= "*Enviroment*:\n-----------\n".$environ."\n\n\n\n";
				$message .= "[[[BUG]]]";
				
				if ( ! email::send("david@kobia.net", 
					$post->email, $post->subject, $message, FALSE))
				{
					Kohana::log('error', "email to ".$post->email." could not be sent");
					echo json_encode(array("status"=>"error", "message"=>"Message could not be sent"));
				}
				
				
				// Success!
				echo json_encode(array("status"=>"error", "message"=>array("Message could not be sent")));
	        }
            // No! We have validation errors
            else
	        {
	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('bug'));
				echo json_encode(array("status"=>"error", "message"=>$errors));
	        }
	    }
	}
}
