<?php defined('SYSPATH') or die('No direct script access.');

/**
* Scheduler Controller (FAUX Cron)
* Generates 1x1 pixel image while executing scheduled tasks
*/
class Scheduler_Controller extends Controller
{
	public function __construct()
    {
        parent::__construct();
		$profiler = new Profiler;
	}
	
	function index()
	{
	    Header("Content-Type: image/gif");
		$this->_get_feeds();
		$this->_get_alerts();
		
		// Transparent GIF
		echo base64_decode("R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
	}
	
	
	/**
	 * parse feed and send feed items to database
	 */
	private function _get_feeds()
	{
		// Max number of feeds to keep
		$max_feeds = 100;
		
		// Today's Date
		$today = strtotime('now');
		
		// Get All Feeds From DB
		$feeds = ORM::factory('feed')->find_all();
		foreach ($feeds as $feed)
		{
			$last_update = $feed->feed_update;
			
			// Has it been more than 24 hours since the last update?
			if ( ((int)$today - (int)$last_update) > 86400	)	// 86400 = 24 hours
			{
				// Parse Feed URL using Feed Helper
				$feed_data = $this->_setup_simplepie( $feed->feed_url );

				foreach($feed_data->get_items(0,50) as $feed_data_item)
				{
					$title = $feed_data_item->get_title();
					$link = $feed_data_item->get_link();
					$description = $feed_data_item->get_description();
					$date = $feed_data_item->get_date();
					// Make Sure Title is Set (Atleast)
					if (isset($title) && !empty($title ))
					{
						// We need to check for duplicates!!!
						// Maybe combination of Title + Date? (Kinda Heavy on the Server :-( )
						$dupe_count = ORM::factory('feed_item')->where('item_title',$title)->where('item_date',date("Y-m-d H:i:s",strtotime($date)))->count_all();

						if ($dupe_count == 0) {
							$newitem = new Feed_Item_Model();
							$newitem->feed_id = $feed->id;
							$newitem->item_title = $title;
							if (isset($description) && !empty($description))
							{
								$newitem->item_description = $description;
							}
							if (isset($link) && !empty($link))
							{
								$newitem->item_link = $link;
							}
							if (isset($date) && !empty($date))
							{
								$newitem->item_date = date("Y-m-d H:i:s",strtotime($date));
							}
							// Set todays date
							else
							{
								$newitem->item_date = date("Y-m-d H:i:s",time());
							}
							$newitem->save();
						}
					}
				}
				
				// Get Feed Item Count
				$feed_count = ORM::factory('feed_item')->where('feed_id', $feed->id)->count_all();
				if ($feed_count > $max_feeds) {
					// Excess Feeds
					$feed_excess = $feed_count - $max_feeds;

					// Delete Excess Feeds
					foreach (ORM::factory('feed_item')
						->where('feed_id', $feed->id)
						->orderby('id', 'ASC')
						->limit($feed_excess)
						->find_all() as $del_feed)
					{
						$del_feed->delete($del_feed->id);
					}
				}

				// Set feed update date
				$feed->feed_update = strtotime('now');
				$feed->save();
			}
		}
	}
	
	
	/**
	 * generate alert messages (mobile & email)
	 */
	private function _get_alerts()
	{
		
	}
	
	
	/**
	 * setup simplepie
	 */
	private function _setup_simplepie( $feed_url ) {
			$data = new SimplePie();
			$data->set_feed_url( $feed_url );
			$data->enable_cache(false);
			$data->enable_order_by_date(true);
			$data->init();
			$data->handle_content_type();

			return $data;
	}
	
}