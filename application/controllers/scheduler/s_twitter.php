<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Twitter Scheduler Controller
 *
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
	}

	public function index()
	{
		// Grabbing tweets requires cURL so we will check for that here.
		if (!function_exists('curl_exec'))
		{
			throw new Kohana_Exception('twitter.cURL_not_installed');
			return false;
		}

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
				{ //This loop is for pagination of rss results
					$hashtag = rawurlencode(trim(str_replace('#','',$hashtag)));
					$twitter_url = 'http://search.twitter.com/search.json?q=%23'.$hashtag.'&rpp=100&page='.$page; //.$last_tweet_id;
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
	}

	/**
	* Adds hash tweets in JSON format to the database and saves the sender as a new
	* Reporter if they don't already exist
	* @param string $data - Twitter JSON results
	*/
	private function add_hash_tweets($data)
	{
		if ($this->_lock())
		{
			return false;
		}

		$services = new Service_Model();
		$service = $services->where('service_name', 'Twitter')->find();

		if ( ! $service)
		{
			$this->_unlock();
			return false;
		}

		$tweets = json_decode($data, false);
		if ( ! $tweets)
		{
			$this->_unlock();
			return false;
		}

		if (isset($tweets->{'error'}))
		{
			$this->_unlock();
			return false;
		}

		$tweet_results = $tweets->{'results'};

		foreach($tweet_results as $tweet)
		{
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

				$reporter->service_id	   = $service->id;
				$reporter->level_id			= $level->id;
				$reporter->service_account	= $tweet->{'from_user'};
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
				if ($tweet->{'geo'} != null)
				{
					$tweet_lat = $tweet->{'geo'}->coordinates[0];
					$tweet_lon = $tweet->{'geo'}->coordinates[1];
				}

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
