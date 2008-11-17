<?php defined('SYSPATH') or die('No direct script access.');

/**
* Feed Controller
* 
*/
class Feed_Controller extends Controller
{
	function __construct()
    {
        parent::__construct();
    }

	public function index($feed = "rss2") 
	{
		// Display News Feed?
		$allow_feed = Kohana::config('settings.allow_feed');
		
		if ($allow_feed == 1) {
			$site_url = "http://" . $_SERVER['SERVER_NAME'] . "/";
		
			// Feed Type?
			if ($feed = "rss2") {
				$feed_view = "rss2";
				$generate_feed = $this->_get_rss2();
			}
			// Add Atom and other formats here in future
			else
			{
				$feed_view = "rss2";
				$generate_feed = $this->_get_rss2();
			}
		
			$view = new View('feed_' . $feed_view, array(
				'feed_title' => htmlspecialchars(Kohana::config('settings.site_name')),
				'feed_url' => $site_url,
				'feed_date' => date("D, d M Y H:i:s", time()),
				'page_description' => htmlspecialchars(Kohana::config('settings.site_name')),
				'feeds' => $generate_feed
				));
			header("Content-Type: text/xml");
			$view->render(TRUE);
		}
	}
	
	private function _get_rss2()
	{
		$site_url = "http://" . $_SERVER['SERVER_NAME'] . "/";
				
		$feed_data = "";
		foreach(ORM::factory('incident')
            ->where('incident_active', '1')
            ->orderby('incident_date', 'desc')
			->limit(20)
            ->find_all() as $feed)
		{
			$feed_data .= "<item>";
			$feed_data .= "	<title>" . htmlspecialchars($feed->incident_title) . "</title>\n";
			$feed_data .= "	<link>" . $site_url . 'reports/view/' . $feed->id . "</link>\n";
			$feed_data .= "	<description>" . htmlspecialchars(text::limit_chars($feed->incident_description, 120, "...", true)) . "</description>\n";
			$feed_data .= "	<pubDate>" . date("D, d M Y H:i:s", strtotime($feed->incident_date)) . "</pubDate>\n";
			$feed_data .= "	<guid>" . $site_url . 'reports/view/' . $feed->id . "</guid>\n";
			$feed_data .= "</item>\n";
		}
		return $feed_data;
	}
}