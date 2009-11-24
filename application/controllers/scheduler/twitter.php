<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Twitter Scheduler Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Twitter Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
*/

class Twitter_Controller extends Controller
{
	public function __construct()
    {
        parent::__construct();
	}

	public function index()
	{
		// Grabbing tweets requires cURL so we will check for that here.
		if (!function_exists('curl_exec'))
		{
			throw new Kohana_Exception('twitter.cURL_not_installed');
			return false;
		}

		// Retrieve Current Settings
		$settings = ORM::factory('settings', 1);

		// Retrieve Last Stored Twitter ID
		$last_tweet_id = "";
		$tweets = ORM::factory('message')
			->with('reporter')
			->where('service_id', '3')
			->orderby('service_messageid','desc')
			->find();
		if ($tweets->loaded == true)
		{
			$last_tweet_id = "&since_id=" . $tweets->service_messageid;
		}

		//Perform Hashtag Search
		$hashtags = explode(',',$settings->twitter_hashtags);
		foreach($hashtags as $hashtag){
			if (!empty($hashtag))
			{
				$page = 1;
				$have_results = TRUE; //just starting us off as true, although there may be no results
				while($have_results == TRUE && $page <= 2){ //This loop is for pagination of rss results
					$hashtag = trim(str_replace('#','',$hashtag));
					$twitter_url = 'http://search.twitter.com/search.json?q=%23'.$hashtag.'&rpp=100&page='.$page;
					$curl_handle = curl_init();
					curl_setopt($curl_handle,CURLOPT_URL,$twitter_url);
					curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,4); //Since Twitter is down a lot, set timeout to 4 secs
					curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); //Set curl to store data in variable instead of print
					$buffer = curl_exec($curl_handle);
					curl_close($curl_handle);
					$have_results = $this->add_hash_tweets($buffer); //if FALSE, we will drop out of the loop
					$page++;
				}
			}
		}

		//Perform Direct Reports Search
		$username = $settings->twitter_username;
		$password = $settings->twitter_password;
		if (!empty($username) && !empty($password))
		{
			$twitter_url = 'http://twitter.com/statuses/replies.json'; //XXX '?.$last_tweet_id;
			$curl_handle = curl_init();
			curl_setopt($curl_handle,CURLOPT_URL,$twitter_url);
			curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,4);
			curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl_handle,CURLOPT_USERPWD,"$username:$password"); //Authenticate!
			$buffer = curl_exec($curl_handle);
			curl_close($curl_handle);
			$this->add_reply_tweets($buffer);
		}
	}


	/**
	* Adds reply tweets in JSON format to the database and saves the sender as a new
	* Reporter if they don't already exist
    * @param string $data - Twitter JSON results
    */
	private function add_reply_tweets($data)
	{
		$services = new Service_Model();
    	$service = $services->where('service_name', 'Twitter')->find();
	   	if (!$service) {
 		    return;
	    }
		$tweets = json_decode($data, false);
		if (!$tweets) {
			return;
		}
		if (isset($tweets->{'error'})) {
			return;
		}

		foreach($tweets as $tweet) {
			$tweet_user = $tweet->{'user'};
			
    		$reporter = ORM::factory('reporter')
				->where('service_id', $service->id)
				->where('service_userid', $tweet_user->{'id'})
				->find();

			if (!$reporter->loaded)
			{
				// Add new reporter
	    		$names = explode(' ', $tweet_user->{'name'}, 2);
	    		$last_name = '';
	    		if (count($names) == 2) {
	    			$last_name = $names[1];
	    		}

	    		// get default reporter level (Untrusted)
				$level = ORM::factory('level')
					->where('level_weight', 0)
					->find();

	    		$reporter = new Reporter_Model();
	    		$reporter->service_id       = $service->id;
				$reporter->level_id	        = $level->id;
	    		$reporter->service_userid   = $tweet_user->{'id'};
	    		$reporter->service_account  = $tweet_user->{'screen_name'};
	    		$reporter->reporter_first   = $names[0];
	    		$reporter->reporter_last    = $last_name;
	    		$reporter->reporter_email   = null;
	    		$reporter->reporter_phone   = null;
	    		$reporter->reporter_ip      = null;
	    		$reporter->reporter_date    = date('Y-m-d');
	    		$reporter->save();
				$reporter_id = $reporter->id;
			}
			
			if ($reporter->level_id > 1 && 
			    count(ORM::factory('message')->where('service_messageid', $tweet->{'id'})
			                           ->find_all()) == 0) {
				// Save Tweet as Message
	    		$message = new Message_Model();
	    		$message->parent_id = 0;
	    		$message->incident_id = 0;
	    		$message->user_id = 0;
	    		$message->reporter_id = $reporter->id;
	    		$message->message_from = $tweet_user->{'screen_name'};
	    		$message->message_to = null;
	    		$message->message = $tweet->{'text'};
	    		$message->message_type = 1; // Inbox
	    		$tweet_date = date("Y-m-d H:i:s",strtotime($tweet->{'created_at'}));
	    		$message->message_date = $tweet_date;
	    		$message->service_messageid = $tweet->{'id'};
	    		$message->save();
    		}
    	}
	}
	
	
	/**
	* Adds hash tweets in JSON format to the database and saves the sender as a new
	* Reporter if they don't already exist
    * @param string $data - Twitter JSON results
    */
	private function add_hash_tweets($data)
	{
		$services = new Service_Model();
    	$service = $services->where('service_name', 'Twitter')->find();
	   	if (!$service) {
 		    return;
	    }
		$tweets = json_decode($data, false);
		if (!$tweets) {
			return;
		}
		if (isset($tweets->{'error'})) {
			return;
		}
		
		$tweet_results = $tweets->{'results'};

		foreach($tweet_results as $tweet) {
    		$reporter = ORM::factory('reporter')
				->where('service_id', $service->id)
				->where('service_account', $tweet->{'from_user'})
				->find();

			if (!$reporter->loaded)
			{
	    		// get default reporter level (Untrusted)
	    		$level = ORM::factory('level')
					->where('level_weight', 0)
					->find();

	    		$reporter->service_id       = $service->id;
				$reporter->level_id	        = $level->id;
	    		$reporter->service_userid   = null;
	    		$reporter->service_account  = $tweet->{'from_user'};
	    		$reporter->reporter_first   = null;
	    		$reporter->reporter_last    = null;
	    		$reporter->reporter_email   = null;
	    		$reporter->reporter_phone   = null;
	    		$reporter->reporter_ip      = null;
	    		$reporter->reporter_date    = date('Y-m-d');
	    		$reporter->save();
			}
			
			if ($reporter->level_id > 1 && 
			    count(ORM::factory('message')->where('service_messageid', $tweet->{'id'})
			                           ->find_all()) == 0) {
				// Save Tweet as Message
	    		$message = new Message_Model();
	    		$message->parent_id = 0;
	    		$message->incident_id = 0;
	    		$message->user_id = 0;
	    		$message->reporter_id = $reporter->id;
	    		$message->message_from = $tweet->{'from_user'};
	    		$message->message_to = null;
	    		$message->message = $tweet->{'text'};
	    		$message->message_type = 1; // Inbox
	    		$tweet_date = date("Y-m-d H:i:s",strtotime($tweet->{'created_at'}));
	    		$message->message_date = $tweet_date;
	    		$message->service_messageid = $tweet->{'id'};
	    		$message->save();
    		}
    	}
	}
}
