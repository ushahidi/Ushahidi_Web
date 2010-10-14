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
	
	private $subdomain;
	
	private $gzip = "";
	
	public function __construct()
	{	
		$this->cache = new Cache;
		
		// Gzip compression
		$this->gzip = Kohana::config('settings.gz');
		
		// Subdomain (MHI)?
		$subdomain = Kohana::config('settings.subdomain');
		if ( ! empty($subdomain))
		{
			$this->subdomain = $subdomain."_";
		}
		
		// If this is a POST, disable cache
		if (empty($_POST))
		{
			Event::add_before( 'system.routing', 
				array('Router', 'setup'), array($this, 'load_cache') );
		}
	}
	
	public function load_cache()
	{
		if ($cache = $this->cache->get($this->subdomain.'page_'.$this->gzip.'_'.$_SERVER['REQUEST_URI']))
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
		// If controller is cachable - cache
		// If this is an error page - DO NOT cache
		if ( ! empty(Kohana::$instance->is_cachable)
		 	AND Kohana::$instance->is_cachable == true
		 	AND Kohana::$has_error == false )
		{
			$this->cache->set($this->subdomain.'page_'.$this->gzip.'_'.$_SERVER['REQUEST_URI'], Event::$data);
		}
	}
}

if (Kohana::config('cache.cache_pages'))
{
	$hook = new hook_page_cache;
	unset($hook);
}