<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Viddler Library
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

class Viddler_Core {
	
	protected $username;
	protected $password;
	protected $api_key;
	protected $callback_url;
	
	protected $table_prefix;
	
	protected $viddler = NULL;
	protected $authed_user = NULL;

	public function __construct()
	{
		
		// Set variables
		$this->username = Kohana::config('viddler.username');
		$this->password = Kohana::config('viddler.password');
		$this->api_key = Kohana::config('viddler.api_key');
		$this->callback_url = Kohana::config('viddler.callback_url');
		
		$this->table_prefix = Kohana::config('database.default.table_prefix');
		
	}
	
	public function authenticate()
	{
		// Authenticate with Viddler
		if($this->viddler == NULL OR $this->authed_user == NULL)
		{
			$this->viddler = new ViddlerV2($this->api_key);
			$this->authed_user = $this->viddler->viddler_users_auth(array('user'=>$this->username, 'password'=>$this->password));
		}
	}
	
	public function delete($viddler_id)
	{
		// First remove from our DB
		$db = Database::instance();
		$query = 'DELETE FROM '.$this->table_prefix.'viddler '
				. 'WHERE viddler_id = '.(int)$viddler_id.';';
		
		$db->query($query);
		
		// Second, remove from Viddler
		$this->authenticate();
		$params = array(
			'sessionid'=>$this->authed_user['auth']['sessionid'],
			'video_id'=>$viddler_id
		);
		$response = $this->viddler->viddler_videos_delete($params);
		
	}
	
}
