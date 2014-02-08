<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Twitter Scheduler Controller
 *
 * This utilizes twitterouath by abrahama -> https://github.com/abraham/twitteroauth

 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Scheduler
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
*/

class S_Twitter_Controller extends Controller {

	// Cache instance
	protected $cache;

	public function __construct()
	{
		parent::__construct();

		// Load cache
		$this->cache = new Cache;

		//Load session
		$this->session = new Session;
	}

	public function index()
	{

		// Grab all the twitter credentials - tokens and keys
		$consumer_key = Settings_Model::get_setting('twitter_api_key');
		$consumer_secret = Settings_Model::get_setting('twitter_api_key_secret');
		$oauth_token = Settings_Model::get_setting('twitter_token');
		$oauth_token_secret = Settings_Model::get_setting('twitter_token_secret');

		$_SESSION['access_token'] = array(
		  'oauth_token'=> $oauth_token,
			'oauth_token_secret' => $oauth_token_secret
			);

		/* Get user access tokens out of the session. */
		$access_token = $_SESSION['access_token'];

		/* Create a TwitterOauth object with consumer/user tokens. */
		$connection = new Twitter_Oauth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		$connection->decode_json = FALSE;

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
		
		// Perform Hashtag Search
		$twitter_hashtags = Settings_Model::get_setting('twitter_hashtags');
		$hashtags = explode(',',$twitter_hashtags);
		foreach($hashtags as $hashtag){
			if (!empty($hashtag))
			{
				$page = 1;
				$have_results = TRUE; //just starting us off as true, although there may be no results
				while($have_results == TRUE AND $page <= 2)
				{ //This loop is for pagination of twitter results
					$hashtag = rawurlencode(trim($hashtag));
					$twitter_url = $connection->get('search/tweets',array('count' => 100, 'q' => $hashtag));
					$have_results = $this->add_hash_tweets($twitter_url);
					$page++;
				}
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

		$tweet_results = json_decode($data);
		foreach($tweet_results->statuses as $tweet)
		{
			$reporter = ORM::factory('reporter')
				->where('service_id', $service->id)
				->where('service_account', $tweet->user->screen_name)
				->find();

			if (!$reporter->loaded)
			{
				// get default reporter level (Untrusted)
				$level = ORM::factory('level')
					->where('level_weight', 0)
					->find();

				$reporter->service_id	   = $service->id;
				$reporter->level_id			= $level->id;
				$reporter->service_account	= $tweet->user->screen_name;
				$reporter->reporter_first	= null;
				$reporter->reporter_last	= null;
				$reporter->reporter_email	= null;
				$reporter->reporter_phone	= null;
				$reporter->reporter_ip	  = null;
				$reporter->reporter_date	= date('Y-m-d');
				$reporter->save();
			}

			if ($reporter->level_id > 1 &&
				count(ORM::factory("message")
					->where("service_messageid = '".$tweet->{'id_str'}."'")
					->find_all()) == 0)
			{

				// Grab geo data if it exists from the tweet
				$tweet_lat = null;
				$tweet_lon = null;
				if ($tweet->{'coordinates'} != null)
				{
					$tweet_lat = $tweet->{'coordinates'}->coordinates[0];
					$tweet_lon = $tweet->{'coordinates'}->coordinates[1];
				}

				// Save Tweet as Message
				$message = new Message_Model();
				$message->parent_id = 0;
				$message->incident_id = 0;
				$message->user_id = 0;
				$message->reporter_id = $reporter->id;
				$message->message_from = $tweet->user->screen_name;
				$message->message_to = null;
				$message->message = $tweet->{'text'};
				$message->message_type = 1; // Inbox
				$tweet_date = date("Y-m-d H:i:s",strtotime($tweet->{'created_at'}));
				$message->message_date = $tweet_date;
				$message->service_messageid = $tweet->{'id_str'};
				$message->latitude = $tweet_lat;
				$message->longitude = $tweet_lon;
				$message->save();

				// Action::message_twitter_add - Twitter Message Received!
				Event::run('ushahidi_action.message_twitter_add', $message);

				// Auto-Create A Report if Reporter is Trusted
				$reporter_weight = $reporter->level->level_weight;
				$reporter_location = $reporter->location;
				if ($reporter_weight > 0 AND $reporter_location)
				{
					$incident_title = text::limit_chars($message->message, 50, "...", false);

					// Create Incident
					$incident = new Incident_Model();
					$incident->location_id = $reporter_location->id;
					$incident->incident_title = $incident_title;
					$incident->incident_description = $message->message;
					$incident->incident_date = $tweet_date;
					$incident->incident_dateadd = date("Y-m-d H:i:s",time());
					$incident->incident_active = 1;
					$incident->incident_mode = 4;
					if ($reporter_weight == 2)
					{
						$incident->incident_verified = 1;
					}
					$incident->save();

					// Update Message with Incident ID
					$message->incident_id = $incident->id;
					$message->save();

					// Save Incident Category
					$trusted_categories = ORM::factory("category")
						->where("category_trusted", 1)
						->find();
					if ($trusted_categories->loaded)
					{
						$incident_category = new Incident_Category_Model();
						$incident_category->incident_id = $incident->id;
						$incident_category->category_id = $trusted_categories->id;
						$incident_category->save();
					}
				}
			}
		}

		$this->_unlock();
		return true;
	}

	private function _lock()
	{
		// *************************************
		// Create A 5 Minute RETRIEVE LOCK
		// This lock is released at the end of execution
		// Or expires automatically
		$twitter_lock = $this->cache->get(Kohana::config('settings.subdomain')."_twitter_lock");
		if ( ! $twitter_lock)
		{
			// Lock doesn't exist
			$timestamp = time();
			$this->cache->set(Kohana::config('settings.subdomain')."_twitter_lock", $timestamp, array("twitter"), 300);
			return false;
		}
		else
		{
			// Lock Exists - End
			return true;
		}
	}

	private function _unlock()
	{
		$this->cache->delete(Kohana::config('settings.subdomain')."_twitter_lock");
	}
}
