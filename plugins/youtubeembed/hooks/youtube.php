<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Youtube Links Hook - Load All Events
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

class youtube {
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{	
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Only add the events if we are on that controller
		if (Router::$controller == 'reports' AND Router::$method == 'view')
		{
			Event::add('ushahidi_filter.report_description', array($this, '_embed_youtube'));
		}
	}
	
	public function _embed_youtube()
	{
		// Access the report description
		$report_description = Event::$data;
		
		$report_description = $this->_auto_embed($report_description);
		
		// Return new description
		Event::$data = $report_description;
	}
	
	
	/**
	 * Convert the youtube text anchors into links.
	 *
	 * @param   string   text to autoembed
	 * @return  string
	 */
	private function _auto_embed($text)
	{
		// Finds all http/https/ftp/ftps links that are not part of an existing html anchor
		if (preg_match_all('~\b(?<!href="|">)(?:ht|f)tps?://\S+(?:/|\b)~i', $text, $matches))
		{
			foreach ($matches[0] as $match)
			{
				// Find All YouTube links
				if(preg_match('/youtube\.com\/(v\/|watch\?v=)([\w\-]+)/', $match, $matches2))
				{
					$embed_code = $this->_embed_code($matches2[2]);
					$text = str_replace($match, $embed_code, $text);
				}
			}
		}
		
		return $text;
	}
	
	private function _embed_code($id = NULL)
	{
		if ($id)
		{
			return '<div style="margin:15px 0 15px 0"><object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/'.$id.'&hl=en&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$id.'&hl=en&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object></div>';
		}
		else
		{
			return "";
		}
	}
}

new youtube;