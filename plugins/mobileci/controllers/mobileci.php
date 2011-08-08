<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mobile Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Mobile Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Mobileci_Controller extends Template_Controller {
	
	public $auto_render = TRUE;
	public $mobile = TRUE;
	
	// Cacheable Controller
	public $is_cachable = TRUE;
	
	// Main template
    public $template = 'mobileci/layout';

	// Table Prefix
	protected $table_prefix;

    public function __construct()
    {
		parent::__construct();
		
		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');
		
		// Load Header & Footer
        $this->template->header  = new View('mobileci/header');
        $this->template->footer  = new View('mobileci/footer');

		$this->template->header->site_name = Kohana::config('settings.site_name');
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');
		
		// Google Analytics
		$google_analytics = Kohana::config('settings.google_analytics');
		$this->template->footer->google_analytics = $this->_google_analytics($google_analytics);
	}
	
	public function index()
	{
		
		$this->auth = new Auth();
		$this->session = Session::instance();
		$this->auth->auto_login();

		if ( ! $this->auth->logged_in('login'))
		{
			// If we aren't logged in, do some awesome login action
			
			$this->template->content  = new View('mobileci/login');
			
		}else{
			
			// If we are logged in, show the checkin form
			
			$this->template->content  = new View('mobileci/main');
			
			$this->template->content->loggedin_username = html::specialchars(Auth::instance()->get_user()->username);
			$this->template->content->loggedin_name = html::specialchars(Auth::instance()->get_user()->name);
			$this->template->content->loggedin_userid = Auth::instance()->get_user()->id;
		}
	}
	
	public function ci()
	{
		$_POST = Validation::factory($_POST)
            ->pre_filter('trim')
            ->add_rules('lat', 'required', 'between[-90,90]')
            ->add_rules('lon', 'required', 'between[-180,180]')
            ->add_rules('message', 'length[0,200]');
		
        if ($_POST->validate())
        {
        	$post = $_POST->safe_array();
        	
        	$lat = $post['lat'];
        	$lon = $post['lon'];
        	$message = $post['message'];
        	$user_id = Auth::instance()->get_user()->id;
        	
        	// FIRST, save the location
        	
			$location = new Location_Model();
			$location->location_name = $lat.','.$lon;
			$location->latitude = $lat;
			$location->longitude = $lon;
			$location->location_date = date("Y-m-d H:i:s",time());
			$location_id = $location->save();
			
			// SECOND, save the checkin
			
			$checkin = ORM::factory('checkin');
			$checkin->user_id = $user_id;
			$checkin->location_id = $location_id;
			$checkin->checkin_description = $message;
			$checkin->checkin_date = date("Y-m-d H:i:s",time());
			$checkin_id = $checkin->save();
			
			// THIRD, save the photo, if there is a photo
			/*
			if( is_array($photo) AND $photo != FALSE)
			{
				$filename = upload::save('photo');
				
				$new_filename = 'ci_'.$user_id.'_'.time().'_'.$this->getRandomString(4);
				$file_type = strrev(substr(strrev($filename),0,4));
				
				// IMAGE SIZES: 800X600, 400X300, 89X59
				
				// Large size
				Image::factory($filename)->resize(800,600,Image::AUTO)
					->save(Kohana::config('upload.directory', TRUE).$new_filename.$file_type);
	
				// Medium size
				Image::factory($filename)->resize(400,300,Image::HEIGHT)
					->save(Kohana::config('upload.directory', TRUE).$new_filename."_m".$file_type);
	
				// Thumbnail
				Image::factory($filename)->resize(89,59,Image::HEIGHT)
					->save(Kohana::config('upload.directory', TRUE).$new_filename."_t".$file_type);	
	
				// Remove the temporary file
				unlink($filename);
	
				// Save to DB
				$media_photo = new Media_Model();
				$media_photo->location_id = $location_id;
				$media_photo->checkin_id = $checkin_id;
				$media_photo->media_type = 1; // Images
				$media_photo->media_link = $new_filename.$file_type;
				$media_photo->media_medium = $new_filename."_m".$file_type;
				$media_photo->media_thumb = $new_filename."_t".$file_type;
				$media_photo->media_date = date("Y-m-d H:i:s",time());
				$media_photo->save();
				
			}
        	*/        	
        	
        }else{
        	var_dump($_POST);
        	die('invalid!');
        }
		
		url::redirect('mobileci');
	}
	
	public function createaccount()
	{
		
		if($_POST)
		{
			$_POST = Validation::factory($_POST)
					->pre_filter('trim')
					->add_rules('name', 'required')
					->add_rules('email', 'required', 'email')
					->add_rules('password', 'required');
			
			if ($_POST->validate())
			{
				$post = $_POST->safe_array();
				
				$name = $post['name'];
				$email = $post['email'];
				$password = $post['password'];
				
				$user = ORM::factory('user');
	            $user->name = $name;
	            $user->email = $email;
	            $user->username = $email;
	            $user->password = $password;
	            $user->add(ORM::factory('role', 'login'));
	            $user_id = $user->save();
				
			}
			
			url::redirect('mobileci');
		}
		
		$this->template->content  = new View('mobileci/createaccount');
		
	}
	
	/*
	* Google Analytics
	* @param text mixed  Input google analytics web property ID.
    * @return mixed  Return google analytics HTML code.
	*/
	private function _google_analytics($google_analytics = false)
	{
		$html = "";
		if (!empty($google_analytics)) {
			$html = "<script type=\"text/javascript\">
				var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
				document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
				</script>
				<script type=\"text/javascript\">
				var pageTracker = _gat._getTracker(\"" . $google_analytics . "\");
				pageTracker._trackPageview();
				</script>";
		}
		return $html;
	}	
}