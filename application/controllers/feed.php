<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Feed Controller
 * ATOM/RSS2 Generator
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
*
*/

class Feed_Controller extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index($feedtype = 'rss2')
    {
        if ( ! Kohana::config('settings.allow_feed'))
        {
            throw new Kohana_404_Exception();
        }
        
        if ($feedtype != 'atom' AND $feedtype!= 'rss2')
        {
            throw new Kohana_404_Exception();
        }

        // How Many Items Should We Retrieve?
        $limit = ( isset($_GET['l']) AND !empty($_GET['l']) AND (int) $_GET['l'] <= 200)
            ? (int) $_GET['l'] : 20;

        // Start at which page?
        $page = ( isset($_GET['p']) AND ! empty($_GET['p']) AND (int) $_GET['p'] >= 1 )
            ? (int) $_GET['p'] 
            : 1;
            
        $page_position = ($page == 1) ? 0 : ( $page * $limit ) ; // Query position

        $site_url = url::base();

        // Cache the Feed with subdomain in the cache name if mhi is set

        $subdomain = '';
        if(substr_count($_SERVER["HTTP_HOST"],'.') > 1 AND Kohana::config('config.enable_mhi') == TRUE)
        {
            $subdomain = substr($_SERVER["HTTP_HOST"],0,strpos($_SERVER["HTTP_HOST"],'.'));
        }

        $cache = Cache::instance();
        $feed_items = $cache->get($subdomain.'_feed_'.$limit.'_'.$page);
        
        if ($feed_items == NULL)
        { // Cache is Empty so Re-Cache
            $incidents = ORM::factory('incident')
                            ->where('incident_active', '1')
                            ->orderby('incident_date', 'desc')
                            ->limit($limit, $page_position)->find_all();
            $items = array();

            foreach($incidents as $incident)
            {
                $categories = Array();
                foreach ($incident->category AS $category)
                {
                  $categories[] = (string)$category->category_title;
                }
              
              
                $item = array();
                $item['id'] = $incident->id;
                $item['title'] = $incident->incident_title;
                $item['link'] = $site_url.'reports/view/'.$incident->id;
                $item['description'] = $incident->incident_description;
                $item['date'] = $incident->incident_date;
                $item['categories'] = $categories;
                
                if($incident->location_id != 0
                    AND $incident->location->longitude
                    AND $incident->location->latitude)
                {
                        $item['point'] = array($incident->location->latitude,
                                                $incident->location->longitude);
                        $items[] = $item;
                }
            }

            $cache->set($subdomain.'_feed_'.$limit.'_'.$page, $items, array('feed'), 3600); // 1 Hour
            $feed_items = $items;
        }

        $feedpath = $feedtype == 'atom' ? 'feed/atom/' : 'feed/';

        //header("Content-Type: text/xml; charset=utf-8");
        $view = new View('feed_'.$feedtype);
        $view->feed_title = htmlspecialchars(Kohana::config('settings.site_name'));
        $view->site_url = $site_url;
        $view->georss = 1; // this adds georss namespace in the feed
        $view->feed_url = $site_url.$feedpath;
        $view->feed_date = gmdate("D, d M Y H:i:s T", time());
        $view->feed_description = Kohana::lang('ui_admin.incident_feed').' '.Kohana::config('settings.site_name');
        $view->items = $feed_items;
        $view->render(TRUE);
    }
}
