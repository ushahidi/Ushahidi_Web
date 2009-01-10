<?php
class VideoEmbed{
	
   function embed($raw, $auto)
   {
	  $host = "";
      //clean the raw data
      $raw = trim($raw);
      
      //check auto play
      
      //get the code
      $hosts = array("http://www.youtube.com/watch?v=", "http://video.google.com/videoplay?docid=-",
      "http://one.revver.com/watch/", "http://www.metacafe.com/watch/", "http://www.liveleak.com/view?i=","http://dotsub.com/media/");
      $code = str_replace($hosts, "", $raw);
      
   
      //find host
      $host1 = strpos($raw, "youtube.com", 1);
      $host2 = strpos($raw, "video.google.com", 1);
      $host3 = strpos($raw, "one.revver.com", 1);
      $host4 = strpos($raw, "www.metacafe.com", 1);
      $host5 = strpos($raw, "www.liveleak.com", 1);
	  $host6 = strpos($raw, "dotsub.com", 1);
      
      if($host1 != null) {$host .= "youtube";}
      if($host2 != null) {$host .= "google";}
      if($host3 != null) {$host .= "revver";}
      if($host4 != null) {$host .= "metacafe";}
      if($host5 != null) {$host .= "liveleak";}
	  if($host6 != null) {$host .= "dotsub";}
      
      //error
      if($host != "youtube" && $host != "google" && $host != "revver" && $host != "metacafe" && $host != "liveleak" && $host != "dotsub")
      {
         $error = true;
         echo "Error Embedding<br/>";
      }
      
      if($host == "youtube")
      {
         //autoplay
         $you_auto = "";
         if($auto == "play") { $you_auto = "&autoplay=1"; }
      
         echo "
         <object width='320' height='265'><param name='movie' value='http://www.youtube.com/v/$code$you_auto'></param>
         <param name='wmode' value='transparent'></param>
         <embed src='http://www.youtube.com/v/$code$you_auto' type='application/x-shockwave-flash' wmode='transparent' width='320' height='265'>
         </embed></object><br/>";
      }
      
      if($host == "google")
      {
         //autoplay
         $google_auto = "";
         if($auto == "play") { $google_auto = "&autoPlay=true"; }
      
         echo "
         <embed style='width:320px; height:265px;' id='VideoPlayback' type='application/x-shockwave-flash'
         src='http://video.google.com/googleplayer.swf?docId=-$code$google_auto&hl=en' flashvars=''></embed><br/><br/>";
      }
      
      if($host == "revver")
      {
         //clean the code
         $code = str_replace("/flv", "", $code);
         
         //autoplay
         $rev_auto = "";
         if($auto == "play") { $rev_auto = "&autoStart=true"; }
         
         echo "<script src='http://flash.revver.com/player/1.0/player.js?mediaId:$code;affiliateId:0;height:320;width:265;' type='text/javascript'>
         </script><br/>";
      }
      
      if($host == "metacafe")
      {
         //clean the code
         $code = strrev($code);
         $code = trim($code, "/");
         $code = strrev($code);
      
         echo "<embed src='http://www.metacafe.com/fplayer/$code.swf'
         width='320' height='265' wmode='transparent' pluginspage='http://www.macromedia.com/go/getflashplayer'
         type='application/x-shockwave-flash'> </embed><br/><br/>";
      }
      
      if($host == "liveleak")
      {
         //clean the code
         $code = str_replace("&p=1", "", $code);
      
      
         echo "<object type='application/x-shockwave-flash' width='320' height='272'='transparent'
         data='http://www.liveleak.com/player.swf?autostart=$live_auto&token=$code'>
         <param name='movie' value='http://www.liveleak.com/player.swf?autostart=$live_auto&token=$code'>
         <param name='wmode' value='transparent'><param name='quality' value='high'></object><br/><br/>";
      }

	  if( $host == "dotsub") 
	  {
		 echo "<iframe src='http://dotsub.com/media/$code' frameborder='0' width='320' height='500'></iframe>";
	  }
      
      //clean up varibles
      $raw = null;
      $code = null;
      $host = null;      
   }

}
?>