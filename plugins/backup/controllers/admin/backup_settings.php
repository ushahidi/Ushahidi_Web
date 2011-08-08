<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Backup Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Backup Settings Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Backup_Settings_Controller extends Admin_Controller
{
	public function index()
	{
		$this->template->this_page = 'addons';
		
		// Standard Settings View
		$this->template->content = new View("admin/plugins_settings");
		$this->template->content->title = "Backup Settings";
		
		// Settings Form View
		$this->template->content->settings_form = new View("backup/admin/backup_settings");
		
		// Get the sites URL
		$this->template->content->settings_form->url = '';
		if(isset($_SERVER["HTTP_HOST"]))
		{
			$site_domain = Kohana::config('config.site_domain');
			$slashornoslash = '';
			if($site_domain{0} != '/') $slashornoslash = '/';
			$val = 'http://'.$_SERVER["HTTP_HOST"].$slashornoslash.$site_domain;
			$this->template->content->settings_form->url = base64_encode($val);
		}
		
		// setup and initialize form field names
        $form = array
        (
            'email' => '',
            'password' => '',
            'key' => ''
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

            $post->add_rules('email', 'required', 'email');
            $post->add_rules('password', 'required', 'alpha_dash');
            $post->add_rules('key', 'required', 'alpha_numeric', 'length[50]');

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Yes! everything is valid
				$backupdata = new Backup_Model(1);
				$backupdata->email = $post->email;
				$backupdata->password = hash('sha512',mysql_real_escape_string($post->password));
				$backupdata->key = $post->key;
				$backupdata->save();
				
				
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
            // Set defaults
            
            $backupdata = new Backup_Model(1);

            $form = array
            (
	            'email' => $backupdata->email,
	            'password' => '',
	            'key' => $backupdata->key
	        );
        }
        
        // Is the key already set? This determines what we show on the settings page
		
		$this->template->content->settings_form->show_setup = true;
		$result = ORM::factory('backup')->find(1);
		if(strlen($result->key) > 0)
		{
			$this->template->content->settings_form->show_setup = false;
		}
		
		// Pass the $form on to the settings_form variable in the view
		$this->template->content->settings_form->form = $form;
		
		// Other variables
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
	}
}