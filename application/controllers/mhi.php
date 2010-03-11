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
 * @module     Contact Us Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class MHI_Controller extends Template_Controller
{

	// MHI template
    public $template = 'layout';

	function __construct()
    {
        parent::__construct();

        // Load Header & Footer
        $this->template->header  = new View('mhi_header');
        $this->template->footer  = new View('mhi_footer');

        $this->template->header->site_name = Kohana::config('settings.site_name');

        // If we aren't at the top level MHI site or MHI isn't enabled, don't allow access to any of this jazz
        if(Kohana::config('config.enable_mhi') == FALSE || Kohana::config('settings.subdomain') != '') {
        	throw new Kohana_User_Exception('MHI Access Error', "MHI disabled for this site.");
        }
    }

    public function index()
    {
    	$this->template->header->this_page = 'mhi';
        $this->template->content = new View('mhi');
    }

    public function signup()
    {
    	$this->template->header->this_page = 'mhi';
        $this->template->content = new View('mhi_signup');
        $this->template->content->site_name = Kohana::config('settings.site_name');
        $this->template->content->domain_name = $_SERVER['HTTP_HOST'];
    }

    public function create()
    {
    	$this->template->header->this_page = 'mhi';
        $this->template->content = new View('mhi_create');

        // Process Form
        if($_POST){
			$post = Validation::factory($_POST);

			//Trim whitespaces
			$post->pre_filter('trim');

			$post->add_rules('signup_first_name','required','alpha_dash');
			$post->add_rules('signup_last_name','required','alpha_dash');
			$post->add_rules('signup_email', 'required','email');
			$post->add_rules('signup_password','required');
			$post->add_rules('signup_subdomain','required','alpha_dash');
			$post->add_rules('signup_instance_name','required');
			$post->add_rules('signup_instance_tagline','required');

			if($post->validate()) {

				$mhi_user = new Mhi_User_Model();
				$db_genesis = new db_genesis;
				$mhi_site_database = new Mhi_Site_Database_Model();
				$mhi_site = new Mhi_Site_Model();

				// Create new user

				$user_id = $mhi_user->save_user(array(
					'firstname'=>$post->signup_first_name,
					'lastname'=>$post->signup_last_name,
					'email'=>$post->signup_email,
					'password'=>$post->signup_password
				));

				// Set up DB and Site

				$base_db = $db_genesis->current_db();

				$new_db_name = $base_db.'_'.$post->signup_subdomain;

				// Do some not so graceful validation
				if($mhi_site_database->db_assigned($new_db_name) || $db_genesis->db_exists($new_db_name)) throw new Kohana_User_Exception('MHI Site Setup Error', "Database already exists and/or is already assigned in the MHI DB.");
				if($mhi_site->domain_exists($post->signup_subdomain)) throw new Kohana_User_Exception('MHI Site Setup Error', "Domain already assigned in MHI DB.");

				// Create site
				$site_id = $mhi_site->save_site(array(
					'user_id'=>$user_id,
					'site_domain'=>$post->signup_subdomain,
					'site_privacy'=>1,    // TODO: 1 is the hardcoded default for now. Needs to be changed?
					'site_active'=>1      // TODO: 1 is the default. This needs to be a config item since this essentially "auto-approves" sites
				));

				// Set up database and save details to MHI DB
				$db_genesis->create_db($new_db_name);
				$mhi_site_database->assign_db($new_db_name,$site_id);
				$db_genesis->populate_db($new_db_name,
					array(
						'username'=>$post->signup_email,
						'name'=>$post->signup_first_name.' '.$post->signup_last_name,
						'password'=>$post->signup_password,
						'email'=>$post->signup_email),
					array(
						'site_name'=>$post->signup_instance_name,
						'site_tagline'=>$post->signup_instance_tagline));

			}else{
				throw new Kohana_User_Exception('Validation Error', "Form not validating. Dev tip: Come back later and clean up validation!");
			}

		}else{
			// If the form was never posted, we need to complain about it.
			throw new Kohana_User_Exception('Incomplete Form', "Form not posted.");
		}
    }
}