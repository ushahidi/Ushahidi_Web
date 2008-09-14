<?php defined('SYSPATH') or die('No direct script access.');

/**
* Swatch Controller
*/
class Swatch_Controller extends Controller
{
	function index()
	{
	    if(!isset($_GET['c'])) {
	        $main_color    = "FFFFFF";
	    }
		else
		{
			$main_color     = $_GET['c'];
		}
	
	
	    if(!isset($_GET['w'])) {
	        $width = "15";
	    }
		else
		{
			$width    = $_GET['w'];
		}
	
	    if(!isset($_GET['h'])) {
	        $height = "15";
	    }
		else
		{
			$height    = $_GET['h'];
		}
	
	
	    if(!isset($_GET['b'])) {
	        $brdr_color = "000000";
	    }
		else
		{
			$brdr_color    = $_GET['b'];
		}

		$mc_red    =    hexdec(substr($main_color, 0, 2));
		$mc_green    =    hexdec(substr($main_color, 2, 2));
		$mc_blue    =    hexdec(substr($main_color, 4, 2));

		$bc_red    =    hexdec(substr($brdr_color, 0, 2));
		$bc_green    =    hexdec(substr($brdr_color, 2, 2));
		$bc_blue    =    hexdec(substr($brdr_color, 4, 2));

		$image    = imagecreate( $width, $height );
		$main_color    = imagecolorallocate( $image, $mc_red, $mc_green, $mc_blue );
		$brdr_color    = imagecolorallocate( $image, $bc_red, $bc_green, $bc_blue );

		imagefill( $image, 0, 0, $brdr_color);
		imagefilledrectangle( $image, 1, 1, ($width-2), ($height-2), $main_color);
		
		imagepng($image);
		imagedestroy($image);
	
	}
}