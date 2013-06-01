<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Test Twitter Controller
 * Tests pulling tweets via twitteroaith
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Test_Twitter_Controller extends Admin_Controller {

	function __construct()
	{
		parent::__construct();	
	}

	public function index()
	{
		$this->template = "";
		$this->auto_render =FALSE;
		
		// grab the necessary keys consumer key, secret, token, token secret
		$consumer_key = Settings_Model::get_setting('twitter_api_key');
		$consumer_secret = Settings_Model::get_setting('twitter_api_key_secret');
		$oauth_token = Settings_Model::get_setting('twitter_token');
		$oauth_token_secret =Settings_Model::get_setting('twitter_token_secret');
		$_SESSION['access_token'] = array('oauth_token'=> $oauth_token,'oauth_token_secret' => $oauth_token_secret);
		$access_token = $_SESSION['access_token'];

		$connection = new Twitter_Oauth($consumer_key,$consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);
		$connection->decode_json = FALSE;
		$connection->get('account/verify_credentials');
		if ($connection->http_code == 200) 
		{
			echo json_encode(array("status"=>"success", "message"=>Kohana::lang('ui_main.success')));
		}
		else
		{
			echo json_encode(array("status"=>"error","message"=>Kohana::lang('ui_main.error')." - ".Kohana::lang('ui_admin.error_twitter')));
		}
				
	}
}

?>
