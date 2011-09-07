<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Contact Us Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Contact_Controller extends Main_Controller 
{
	function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $this->template->header->this_page = 'contact';
        $this->template->content = new View('contact');
		
		// Setup and initialize form field names
        $form = array (
            'contact_name' => '',
            'contact_email' => '',
            'contact_phone' => '',
            'contact_subject' => '',			
            'contact_message' => '',
            'captcha' => ''
        );

        // Copy the form as errors, so the errors will be stored with keys
        // corresponding to the form field names
		$captcha = Captcha::factory();
        $errors = $form;
        $form_error = FALSE;
        $form_sent = FALSE;
		
		// Check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);
	
	        // Add some rules, the input field, followed by a list of checks, carried out in order
            $post->add_rules('contact_name', 'required', 'length[3,100]');
			$post->add_rules('contact_email', 'required','email', 'length[4,100]');
            $post->add_rules('contact_subject', 'required', 'length[3,100]');
            $post->add_rules('contact_message', 'required');
            $post->add_rules('captcha', 'required', 'Captcha::valid');
			
			// Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Yes! everything is valid - Send email
                $site_email = Kohana::config('settings.site_email');
                $message = Kohana::lang('ui_admin.sender').": " . $post->contact_name . "\n";
                $message .= Kohana::lang('ui_admin.email').": " . $post->contact_email . "\n";
                $message .= Kohana::lang('ui_admin.phone').": " . $post->contact_phone . "\n\n";
                $message .= Kohana::lang('ui_admin.message').": \n" . $post->contact_message . "\n\n\n";
                $message .= "~~~~~~~~~~~~~~~~~~~~~~\n";
                $message .= Kohana::lang('ui_admin.sent_from_website'). url::base();
                
                // Send Admin Message
                email::send( $site_email, $post->contact_email, $post->contact_subject, $message, FALSE );
				
                $form_sent = TRUE;
            }
            // No! We have validation errors, we need to show the form again, with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('contact'));
                $form_error = TRUE;
            }
        }
		
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_sent = $form_sent;
        $this->template->content->captcha = $captcha;
		
        // Rebuild Header Block
        $this->template->header->header_block = $this->themes->header_block();		
    }	
}
