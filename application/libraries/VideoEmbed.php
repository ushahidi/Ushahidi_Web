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
	/*
	 * Get the services supported by VideoEmbed
	 * 
	 */
	public function services()
	{
		$services = array(
			"youtube" => array(
				'baseurl' => "http://www.youtube.com/watch?v=",
				'searchstring' => 'youtube.com'
			),
			// May now be defunct
			"google" => array(
				'baseurl' => "http://video.google.com/videoplay?docid=-",
				'searchstring' => 'google.com'
			),
			"metacafe" => array(
				'baseurl' => "http://www.metacafe.com/watch/", 
				'searchstring' => 'metacafe.com'
			),
			"dotsub" => array(
				'baseurl' => "http://dotsub.com/view/",
				'searchstring' => 'dotsub.com'
			),
			"vimeo" => array(
				'baseurl' => "http://vimeo.com/",
				'searchstring' => 'vimeo.com'
			),
		);
		
		Event::run('ushahidi_filter.video_embed_services', $services);
		
		return $services;
	}
	
	public function detect_service($raw)
	{
		// To hold the name of the video service
		$service_name = "";
		
		// Trim whitespaces from the raw data
		$raw = trim($raw);
		
		// Array of the supportted video services
		$services = $this->services();
		
		// Determine the video service to use
		$service_name = false;
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
	 */
	public function embed($raw, $auto = FALSE, $echo = TRUE)
	{
		$service_name = $this->detect_service($raw);
		$services = $this->services();
		$output = FALSE;
		
		// Get video code from url.
		$code = str_replace($services[$service_name]['baseurl'], "", $raw);
		
		switch($service_name)
		{
			case "youtube":
				// Check for autoplay
				$you_auto = ($auto) ? "&autoplay=1" : "";
				
				$output = '<iframe id="ytplayer" type="text/html" width="320" height="265" '
					. 'src="http://www.youtube.com/embed/'.htmlentities($code, ENT_QUOTES, "UTF-8").'?origin='.urlencode(url::base()).htmlentities($you_auto, ENT_QUOTES, "UTF-8").'" '
					. 'frameborder="0"></iframe>';
			break;
			
			case "google":
				// Check for autoplay
				$google_auto = ($auto) ? "&autoPlay=true" : "";
				
				$output = "<embed style='width:320px; height:265px;' id='VideoPlayback' type='application/x-shockwave-flash'"
					. "	src='http://video.google.com/googleplayer.swf?docId=-".htmlentities($code.$google_auto, ENT_QUOTES, "UTF-8")."&hl=en' flashvars=''>"
					. "</embed>";
			break;
			
			case "metacafe":
				// Sanitize input
				$code = strrev(trim(strrev($code), "/"));
				
				$output = "<embed src='http://www.metacafe.com/fplayer/".htmlentities($code, ENT_QUOTES, "UTF-8").".swf'"
					. "	width='320' height='265' wmode='transparent' pluginspage='http://get.adobe.com/flashplayer/'"
					. "	type='application/x-shockwave-flash'> "
					. "</embed>";
			break;
			
			case "dotsub":
				$output = "<iframe src='http://dotsub.com/media/".htmlentities($code, ENT_QUOTES, "UTF-8")."' frameborder='0' width='320' height='500'></iframe>";
			
			break;
			
			case "vimeo":
				$vimeo_auto = ($auto) ? "?autoplay=1" : "";
				
				$output = '<iframe src="http://player.vimeo.com/video/'.htmlentities($code.$vimeo_auto, ENT_QUOTES, "UTF-8").'" width="320" height="265" frameborder="0">'
					. '</iframe>';
			break;
		}

		$data = array($service_name, $output);
		Event::run('ushahidi_filter.video_embed_embed', $data);
		list($service_name, $output) = $data;
		
		if (!$output)
		{
			$output = '<a href="'.$raw.'" target="_blank">'.Kohana::lang('ui_main.view_view').'</a>';
		}

		if ($echo) echo $output;

		return $output;
	}
	
	/**
	 * Generates the thumbnail a video
	 *
	 * @param string $raw URL of the video
	 */
	public function thumbnail($raw)
	{
		$service_name = $this->detect_service($raw);
		$services = $this->services();
		$output = FALSE;
		
		// Get video code from url.
		$code = str_replace($services[$service_name]['baseurl'], "", $raw);
		
		switch($service_name)
		{
			case "youtube":
				$oembed = @json_decode(file_get_contents("http://www.youtube.com/oembed?url=".urlencode($raw)));
				if (!empty($oembed) AND ! empty($oembed->thumbnail_url))
				{
					$output = $oembed->thumbnail_url;
				}
			break;
			
			case "dotsub":
				$oembed = @json_decode(file_get_contents("http://dotsub.com/services/oembed?url=".urlencode($raw)));
				if (!empty($oembed) AND ! empty($oembed->thumbnail_url))
				{
					$output = $oembed->thumbnail_url;
				}
			break;
			
			case "vimeo":
				$oembed = @json_decode(file_get_contents("http://vimeo.com/api/oembed.json?url=".urlencode($raw)));
				if (!empty($oembed) AND ! empty($oembed->thumbnail_url))
				{
					$output = $oembed->thumbnail_url;
				}
			break;
			
			
			case "google":
			case "metacafe":
			default:
				$output = FALSE;
			break;
		}

		$data = array($service_name, $output);
		Event::run('ushahidi_filter.video_embed_thumbnail', $data);
		list($service_name, $output) = $data;

		return $output;
	}
}
?>
