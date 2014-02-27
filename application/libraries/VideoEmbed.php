<?php
/**
 * Video embedding libary
 * Provides a feature for embedding videos (YouTube, Google Video, Revver, Metacafe, LiveLeak, 
 * Dostub and Vimeo) in a report
 * 
 * @package	   VideoEmbed
 * @author	   Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
 */
class VideoEmbed 
{
	/**
	 * @var current video url
	 */
	private $url = FALSE;
	
	/**
	 * @var name of current service
	 */
	private $service_name = FALSE;
	
	/**
	 * @var config array for current service
	 */
	private $service = array();
	
	/**
	 * Get the services supported by VideoEmbed
	 * 
	 * @return array
	 */
	private function services()
	{
		$services = array(
			"youtube" => array(
				'baseurl' => "http://www.youtube.com/watch?v=",
				'searchstring' => 'youtube.com',
				'oembed' => 'http://www.youtube.com/oembed',
				'keep-params' => 'v'
			),
			// May now be defunct
			"google" => array(
				'baseurl' => "http://video.google.com/videoplay?docid=-",
				'searchstring' => 'google.com',
				'keep-params' => 'docid'
			),
			"metacafe" => array(
				'baseurl' => "http://www.metacafe.com/watch/", 
				'searchstring' => 'metacafe.com',
			),
			"dotsub" => array(
				'baseurl' => "http://dotsub.com/view/",
				'searchstring' => 'dotsub.com',
				'oembed' => 'http://dotsub.com/services/oembed',
			),
			"vimeo" => array(
				'baseurl' => "http://vimeo.com/",
				'searchstring' => 'vimeo.com',
				'oembed' => 'http://vimeo.com/api/oembed.json',
			),
		);
		
		Event::run('ushahidi_filter.video_embed_services', $services);
		
		return $services;
	}
	
	/**
	 * Set current video url and preprocess
	 * 
	 * @param string $url video url
	 **/
	public function set_url($url)
	{
		$this->service_name = $this->detect_service($url);
		if ($this->service_name !== FALSE)
		{
			$services = $this->services();
			$this->service = $services[$this->service_name];
		}
		
		$this->url = $this->clean_url($url);
	}
	
	/**
	 * Convert raw url to standard structure
	 * Particularly needed for youtube where v= param must be first
	 * 
	 * @param string $raw raw url
	 * @return string standarized url
	 */
	private function clean_url($raw)
	{
		if (isset($this->service['keep-params']))
		{
			$components = parse_url($raw);
			parse_str($components['query'], $query);
			if (! isset($query[$this->service['keep-params']]) ) break;
			$raw = $this->service['baseurl']. $query[$this->service['keep-params']];
		}

		return $raw;
	}
	
	/**
	 * Detect video services based on URL
	 * 
	 * @param string $raw video url
	 * @return string $service_name service name
	 */
	private function detect_service($raw)
	{
		// To hold the name of the video service
		$service_name = "";
		
		// Trim whitespaces from the raw data
		$raw = trim($raw);
		
		// Array of the supportted video services
		$services = $this->services();
		
		// Determine the video service to use
		$service_name = FALSE;
		foreach ($services as $key => $value)
		{
			// Match raw url against service search string
			if (strpos($raw, $value['searchstring']))
			{
				$service_name = $key;
				break;
			}
		}
		
		$data = array($services, $service_name);
		Event::run('ushahidi_filter.video_embed_detect_services', $data);
		list($services, $service_name) = $data;
		
		return $service_name;
	}
	
	/**
	 * Generates the HTML for embedding a video
	 *
	 * @param string $raw URL of the video to be embedded
	 * @param boolean $auto Autoplays the video as soon as its loaded
	 * @param boolean $echo Should we echo the embed code or just return it
	 * @return
	 */
	public function embed($raw, $auto = FALSE, $echo = TRUE)
	{
		$this->set_url($raw);
		$output = FALSE;
		
		// Get video code from url.
		if (isset($this->service['baseurl']))
		{
			$code = str_replace($this->service['baseurl'], "", $this->url);
		}
		
		switch($this->service_name)
		{
			case "youtube":
				// Check for autoplay
				$you_auto = ($auto) ? "&autoplay=1" : "";
				
				$output = '<iframe id="ytplayer" type="text/html" width="320" height="265" '
					. 'src="//www.youtube.com/embed/'.html::escape($code).'?origin='.urlencode(url::base()).html::escape($you_auto).'" '
					. 'frameborder="0"></iframe>';
			break;
			
			case "google":
				// Check for autoplay
				$google_auto = ($auto) ? "&autoPlay=true" : "";
				
				$output = "<embed style='width:320px; height:265px;' id='VideoPlayback' type='application/x-shockwave-flash'"
					. "	src='//video.google.com/googleplayer.swf?docId=-".html::escape($code.$google_auto)."&hl=en' flashvars=''>"
					. "</embed>";
			break;
			
			case "metacafe":
				// Sanitize input
				$code = strrev(trim(strrev($code), "/"));
				
				$output = "<embed src='http://www.metacafe.com/fplayer/".html::escape($code).".swf'"
					. "	width='320' height='265' wmode='transparent' pluginspage='http://get.adobe.com/flashplayer/'"
					. "	type='application/x-shockwave-flash'> "
					. "</embed>";
			break;
			
			case "dotsub":
				$output = "<iframe src='http://dotsub.com/media/".html::escape($code)."' frameborder='0' width='320' height='500'></iframe>";
			
			break;
			
			case "vimeo":
				$vimeo_auto = ($auto) ? "?autoplay=1" : "";
				
				$output = '<iframe src="//player.vimeo.com/video/'.html::escape($code.$vimeo_auto).'" width="320" height="265" frameborder="0">'
					. '</iframe>';
			break;
		}

		$data = array($this->service_name, $output);
		Event::run('ushahidi_filter.video_embed_embed', $data);
		list($this->service_name, $output) = $data;
		
		if (!$output)
		{
			$output = '<a href="'.$this->url.'" target="_blank">'.Kohana::lang('ui_main.view_video').'</a>';
		}

		if ($echo) echo $output;

		return $output;
	}
	
	/**
	 * Generates the thumbnail a video
	 *
	 * @param string $raw URL of the video
	 * @return string url of video thumbnail
	 */
	public function thumbnail($raw)
	{
		$this->set_url($raw);
		$output = FALSE;

		if (isset($this->service['oembed']))
		{

			$url = $this->service['oembed']."?url=".urlencode($this->url);

			$request = new HttpClient($url);
			$result = $request->execute();

			if ($result !== FALSE)
			{
				$oembed = json_decode($result);

				if (!empty($oembed) AND ! empty($oembed->thumbnail_url))
				{
					$output = $oembed->thumbnail_url;
				}
			}
		}

		$data = array($this->service_name, $output);
		Event::run('ushahidi_filter.video_embed_thumbnail', $data);
		list($this->service_name, $output) = $data;

		return $output;
	}
}
?>
