<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Clickatell Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Clickatell Settings Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Clickatell_Settings_Controller extends Admin_Controller
{
	public function index()
	{
		$this->template->this_page = 'addons';
		
		// Standard Settings View
		$this->template->content = new View("admin/plugins_settings");
		$this->template->content->title = "Clickatell Settings";
		
		// Settings Form View
		$this->template->content->settings_form = new View("clickatell/admin/clickatell_settings");
		
		// JS Header Stuff
        $this->template->js = new View('clickatell/admin/clickatell_settings_js');
		
		// setup and initialize form field names
        $form = array
        (
            'clickatell_api' => '',
            'clickatell_username' => '',
            'clickatell_password' => ''
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

            $post->add_rules('clickatell_api','required', 'length[4,20]');
            $post->add_rules('clickatell_username', 'required', 'length[3,50]');
            $post->add_rules('clickatell_password', 'required', 'length[5,50]');

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Yes! everything is valid
                $clickatell = new Clickatell_Model(1);
                $clickatell->clickatell_api = $post->clickatell_api;
                $clickatell->clickatell_username = $post->clickatell_username;
                $clickatell->clickatell_password = $post->clickatell_password;
                $clickatell->save();

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
            // Retrieve Current Settings
            $clickatell = ORM::factory('clickatell', 1);

            $form = array
            (
                'clickatell_api' => $clickatell->clickatell_api,
                'clickatell_username' => $clickatell->clickatell_username,
                'clickatell_password' => $clickatell->clickatell_password
            );
        }
		
		// Pass the $form on to the settings_form variable in the view
		$this->template->content->settings_form->form = $form;
		
		
		// Do we have a frontlineSMS Key? If not create and save one on the fly
        $clickatell = ORM::factory('clickatell', 1);
		
		if ($clickatell->loaded AND $clickatell->clickatell_key)
		{
			$clickatell_key = $clickatell->clickatell_key;
		}
		else
		{
			$clickatell_key = strtoupper(text::random('alnum',8));
            $clickatell->clickatell_key = $clickatell_key;
            $clickatell->save();
		}

		$this->template->content->settings_form->clickatell_key = $clickatell_key;
		$this->template->content->settings_form->clickatell_link = url::site()."clickatell/index/".$clickatell_key;
		
		// Other variables
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
	}
	
	/**
     * Retrieves Clickatell Balance using Clickatell Library
     */
    function smsbalance()
    {
        $this->template = "";
        $this->auto_render = FALSE;

        $clickatell = ORM::factory("clickatell")->find(1);
        if ($clickatell->loaded)
		{
            $clickatell_api = $clickatell->clickatell_api;
            $clickatell_username = $clickatell->clickatell_username;
            $clickatell_password = $clickatell->clickatell_password;

            $testsms = new Clickatell_API();
            $testsms->api_id = $clickatell_api;
            $testsms->user = $clickatell_username;
            $testsms->password = $clickatell_password;
            $testsms->use_ssl = false;
            $testsms->sms();
            // echo $mysms->session;
            echo $testsms->getbalance();
        }
    }
}