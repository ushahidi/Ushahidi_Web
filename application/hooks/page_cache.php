<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Page_Cache Hook - Caches entire pages to cache directory
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Page_Cache Hook  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class hook_page_cache
{
	private $cache;
	
	public function __construct()
	{
		$this->cache = new Cache;
		
		Event::add_before( 'system.routing', 
			array('Router', 'setup'), array($this, 'load_cache') );
	}
	
	public function load_cache()
	{
		if ($cache = $this->cache->get('page_'.Router::$complete_uri))
		{
			Kohana::render($cache);
			exit;
		}
		else
		{
			Event::add('system.display', array($this, 'save_cache'));
		}
	}
	
	public function save_cache()
	{
		$this->cache->set('page_'.Router::$complete_uri, Event::$data);
	}
}

if (Kohana::config('cache.cache_pages'))
{
	$hook = new hook_page_cache;
	unset($hook);
}