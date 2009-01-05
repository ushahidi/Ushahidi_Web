<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Swatch Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Swatch Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
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
