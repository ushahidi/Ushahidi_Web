<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Viddler - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class viddlerevents {
	
	protected $username;
	protected $password;
	protected $api_key;
	protected $callback_url;
	
	protected $table_prefix;

	public function __construct()
	{
		Event::add('system.pre_controller', array($this, 'add'));
		
		// Set variables
		$this->username = Kohana::config('viddler.username');
		$this->password = Kohana::config('viddler.password');
		$this->api_key = Kohana::config('viddler.api_key');
		$this->callback_url = Kohana::config('viddler.callback_url');
		
		$this->table_prefix = Kohana::config('database.default.table_prefix');
		
	}

	public function add()
	{
		Event::add('ushahidi_action.report_form_admin_after_video_link', array($this, 'show_admin_upload_form_field'));
		Event::add('ushahidi_action.report_form_after_video_link', array($this, 'show_upload_form_field'));
		Event::add('ushahidi_action.report_edit',  array($this, 'upload_video'));
		Event::add('ushahidi_action.report_add',  array($this, 'upload_video'));
		Event::add('ushahidi_action.report_display_media', array($this, 'display_videos'));
	}
	
	public function show_admin_upload_form_field()
	{
		$view = View::factory('admin_upload_form_field');
		
		$incident_id = Event::$data;
		if($incident_id != FALSE)
		{
			$view->videos = $this->get_videos($incident_id);
		}else{
			$view->videos = array();
		}
		
		// Set maximum filesize
		$view->maximum_filesize = Kohana::config('viddler.maximum_filesize');
		
		$view->render(TRUE);
	}
	
	public function show_upload_form_field()
	{
		$view = View::factory('upload_form_field');
		
		// Set maximum filesize
		$view->maximum_filesize = Kohana::config('viddler.maximum_filesize');
		
		$view->render(TRUE);
	}
	
	public function get_videos($incident_id=FALSE)
	{
		// First check if any videos need to be updated with embed codes
		$this->check_for_embed();
		
		$db = Database::instance();
		$query = 'SELECT viddler_id, url, embed, embed_small, incident_id '
				. 'FROM '.$this->table_prefix.'viddler';
		if($incident_id != FALSE){
			$query .= ' WHERE incident_id = '.(int)$incident_id.';';
		}
		
		return $db->query($query);
	}
	
	public function display_videos()
	{
		$view = View::factory('display_videos');
		
		$incident_id = Event::$data;
		$view->videos = $this->get_videos($incident_id);
		
		$show = FALSE;
		if(count($view->videos) > 0)
		{
			$show = TRUE;
		}
		
		$view->render($show);
	}
	
	public function check_for_embed()
	{
		$db = Database::instance();
		$query = 'SELECT viddler_id '
				. 'FROM '.$this->table_prefix.'viddler '
				. 'WHERE embed IS NULL;';
		$results = $db->query($query);
		
		if(count($results) > 0)
		{
			// Authenticate with Viddler
			$viddler = new ViddlerV2($this->api_key);
			$user = $viddler->viddler_users_auth(array('user'=>$this->username, 'password'=>$this->password));
			
			// See which files have had successful encoding
			foreach($results as $result)
			{
				$details = $viddler->viddler_videos_getDetails(array('sessionid' => $user['auth']['sessionid'], 'video_id' => $result->viddler_id));
				
				if($details['video']['status'] == 'ready')
				{
					// The video is ready, get the embed code for the video
					$embed = $viddler->viddler_videos_getEmbedCode(
						array('sessionid' => $user['auth']['sessionid'],
							'video_id' => $result->viddler_id,
							'player_type' => 'simple',
							'embed_code_type' => '3'));
					
					$embed_small = $viddler->viddler_videos_getEmbedCode(
						array('sessionid' => $user['auth']['sessionid'],
							'video_id' => $result->viddler_id,
							'player_type' => 'simple',
							'embed_code_type' => '3',
							'width' => '200'));
					
					// Now save our embed codes.
					$query = 'UPDATE `'.$this->table_prefix.'viddler` SET 
							embed = \''.mysql_real_escape_string($embed['video']['embed_code']).'\', 
							embed_small = \''.mysql_real_escape_string($embed_small['video']['embed_code']).'\' 
							WHERE viddler_id = '.(int)$result->viddler_id.'';
					$db->query($query);
				}
			}
		}
		
	}
	
	public function upload_video($incident_id=FALSE)
	{	
		// First, save our upload

		$filename = upload::save('incident_video_file');
		
		if($filename == false) return FALSE;
		
		// Authenticate with Viddler
		$viddler = new ViddlerV2($this->api_key);
		$user = $viddler->viddler_users_auth(array('user'=>$this->username, 'password'=>$this->password));
		
		if($incident_id == FALSE)
		{
			$incident_id = Event::$data->id;
		}
		
		$params = array(
			'sessionid'=>$user['auth']['sessionid'],
			'title'=>Kohana::config('settings.site_name').'_'.$incident_id.'_'.time(),
			'tags'=>date('MY').','.Kohana::config('settings.subdomain'),
			'description'=>'none',
			'file'=>'@'.$filename
		);
		
		// Prepare and upload the video
		$prepare = $viddler->viddler_videos_prepareUpload(array('sessionid' => $user['auth']['sessionid']));
		$results = $viddler->viddler_videos_upload($params, $prepare['upload']['endpoint']);
		
		// Save video details to the database
		$this->save_video($incident_id,$results);
		
		return $results;
	}
	
	public function save_video($incident_id,$video_details)
	{
		if(isset($video_details['error']))
		{
			return FALSE;
		}
		
		$viddler_id = $video_details['video']['id'];
		$url = $video_details['video']['url'];
		
		$query = 'INSERT INTO `'.$this->table_prefix.'viddler`
				(`viddler_id`,`incident_id`,`url`) VALUES 
				(\''.mysql_real_escape_string($viddler_id).'\', 
				\''.(int)$incident_id.'\', 
				\''.mysql_real_escape_string($url).'\');';
		mysql_query($query);
	}
}
new viddlerevents;