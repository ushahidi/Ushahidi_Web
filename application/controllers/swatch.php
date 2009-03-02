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
		// Image Color
	    if(!isset($_GET['c'])) {
	        $main_color    = "990000";
	    }
		else
		{
			$main_color     = $_GET['c'];
		}
	
		
		// Image Width
	    if(!isset($_GET['w']) || !is_numeric($_GET['w'])) {
	        $width = "16";
	    }
		else
		{
			$width    = $_GET['w'];
		}
	
		// Image Height
	    if(!isset($_GET['h']) || !is_numeric($_GET['h'])) {
	        $height = "16";
	    }
		else
		{
			$height    = $_GET['h'];
		}
	
		// Image Border Color
	    if(!isset($_GET['b'])) {
	        $brdr_color = "990000";
	    }
		else
		{
			$brdr_color    = $_GET['b'];
		}
		
		// Image Type (Circle or Rectangle?)
		if(!isset($_GET['t']) || ($_GET['t'] != "rec" && $_GET['t'] != "cir") ) {
	        $image_type = "rec";
	    }
		else
		{
			$image_type    = $_GET['t'];
		}

		$mc_red    =    hexdec(substr($main_color, 0, 2));
		$mc_green    =    hexdec(substr($main_color, 2, 2));
		$mc_blue    =    hexdec(substr($main_color, 4, 2));

		$bc_red    =    hexdec(substr($brdr_color, 0, 2));
		$bc_green    =    hexdec(substr($brdr_color, 2, 2));
		$bc_blue    =    hexdec(substr($brdr_color, 4, 2));
		
		
		if ($image_type == 'rec')
		{
			$image    	   = imagecreate( $width, $height );
			$main_color    = imagecolorallocate( $image, $mc_red, $mc_green, $mc_blue );
			$brdr_color    = imagecolorallocate( $image, $bc_red, $bc_green, $bc_blue );
			
			imagefill( $image, 0, 0, $brdr_color);
			imagefilledrectangle( $image, 1, 1, ($width-2), ($height-2), $main_color);
		}
		else
		{ // Use imagecolorallocatealpha to set transparency level
			
			$a = $width;
			$b = $a*4;
			$c = $b/2;
			$d = $b;
			$e = $d-(2*8);
			
			$image    	   = imagecreate( $a, $a );
			$image2    	   = imagecreate( ($b), ($b) );
			$main_color    = imagecolorallocatealpha( $image2, $mc_red, $mc_green, $mc_blue, 20 );
			$brdr_color    = imagecolorallocatealpha( $image2, $bc_red, $bc_green, $bc_blue, 0 );
			$bkg_color	   = imagecolorallocatealpha($image2, 0, 0, 0, 127);
			
			imagefill( $image2, 0, 0, $bkg_color);
			imagefilledellipse( $image2, $c, $c, $d, $d, $brdr_color);
			imagefilledellipse( $image2, $c, $c, $e, $e, $main_color);
			
			imagecopyresampled($image,$image2,0,0,0,0,$a,$a,$b,$b);
		}
		
		
		header("Content-type: image/png");
		imagepng($image);
		imagedestroy($image);
	}
}
