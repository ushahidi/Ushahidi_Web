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
	 * Generates the HTML for embedding a video in a report
	 *
	 * @param string $raw URL of the video to be embedded
	 * @param string $auto Autoplays the video as soon as its loaded
	 */
	public function embed($raw, $auto)
	{
		// To hold the name of the video service
		$service_name = "";
		
		// Trim whitespaces from the raw data
		$raw = trim($raw);
		
		// Array of the supportted video services
		$services = array(
			"youtube" => "http://www.youtube.com/watch?v=", 
			"google" => "http://video.google.com/videoplay?docid=-",
			"revver" => "http://one.revver.com/watch/", 
			"metacafe" => "http://www.metacafe.com/watch/", 
			"lieveleak" => "http://www.liveleak.com/view?i=",
			"dotsub" => "http://dotsub.com/media/",
			"vimeo" => "http://vimeo.com/"
		);
		
		// Get the video URL
		$code = str_replace(array_values($services), "", $raw);
		
		// Determine the video service to use
		foreach ($services as $key => $value)
		{
			// Extract the domain name of the service and check if it exists in the provided video URL
			preg_match('#^((https|http)://)?([^/]+)#i', $value, $matches);
			if (count($matches) > 0 AND strpos($raw, $matches[2], 1))
			{
				$service_name = $key;
			}
		}
		
		// Check for valid hostnames
		if ( ! array_key_exists($service_name, $services))
		{
			echo '<a href="'.$raw.'" target="_blank">'.Kohana::lang('ui_main.view').' '.Kohana::lang('ui_main.video').'</a>';
			
			// No point in proceeding past this point therefore return
			return;
		}
		
		// Print the HTML embed code depending on the video service
		if ($service_name == "youtube")
		{
			// Check for autoplay
			$you_auto = ($auto == "play")? "&autoplay=1" : "";
			
			echo "<object width='320' height='265'>"
				. "	<param name='movie' value='http://www.youtube.com/v/$code$you_auto'></param>"
				. "	<param name='wmode' value='transparent'></param>"
				. "	<embed src='http://www.youtube.com/v/$code$you_auto' type='application/x-shockwave-flash' "
				. "		wmode='transparent' width='320' height='265'>"
				. "	</embed>"
				. "</object>";
		}
		elseif ($service_name == "google")
		{
			// Check for autoplay
			$google_auto = ($auto == "play")? "&autoPlay=true" : "";

			echo "<embed style='width:320px; height:265px;' id='VideoPlayback' type='application/x-shockwave-flash'"
				. "	src='http://video.google.com/googleplayer.swf?docId=-$code$google_auto&hl=en' flashvars=''>"
				. "</embed>";
		}
		elseif ($service_name == "revver")
		{
			// Sanitization
			$code = str_replace("/flv", "", $code);

			// Check for autoplay
			$rev_auto = ($auto == "play")? "&autoStart=true" : "";

			echo "<script src='http://flash.revver.com/player/1.0/player.js?mediaId:$code;affiliateId:0;height:320;width:265;'"
				. "	type='text/javascript'>"
				. "</script>";
		}
		elseif ($service_name == "metacafe")
		{
			// Sanitize input
			$code = strrev(trim(strrev($code), "/"));
			
			echo "<embed src='http://www.metacafe.com/fplayer/$code.swf'"
				. "	width='320' height='265' wmode='transparent' pluginspage='http://get.adobe.com/flashplayer/'"
				. "	type='application/x-shockwave-flash'> "
				. "</embed>";
		}
		elseif ($service_name == "liveleak")
		{
			echo "<object type='application/x-shockwave-flash' width='320' height='272'='transparent'"
				. "	data='http://www.liveleak.com/e/$code'>"
				. "	<param name='movie' value='http://www.liveleak.com/e/$code'>"
				. "	<param name='wmode' value='transparent'><param name='quality' value='high'>"
				. "</object>";
		}
		elseif ($service_name == "dotsub") 
		{
			echo "<iframe src='http://dotsub.com/media/$code' frameborder='0' width='320' height='500'></iframe>";
		}
		elseif ($service_name == "vimeo") 
		{
			echo "<iframe src=\"http://player.vimeo.com/video/$code\" width=\"100%\" height=\"300\" frameborder=\"0\">"
				. "</iframe>";
		}
		
		// Free memory - though this is done implicitly by the PHP interpreter
		unset($raw, $code, $service_name);
	}
}
?>