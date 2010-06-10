<?php
/**
 * MHI Homepage JS file
 * 
 * Non-clustered map rendering (Please refer to main_cluster_js for Server Side Clusters)
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Main_JS View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>

$(function(){
	$(".intro-slideshow").cycle({ delay:3000, speed:1000, timeout:2000, autostop:4 });
	$(".intro-slideshow").cycle('pause');
	
	$('a.cycle-Resume').click(function(){
		//alert("clicked");
		$(".intro-slideshow").cycle('destroy');
		$(".intro-slideshow").cycle({ delay:-2000, speed:1000, timeout:2000, autostop:4 })
		return false;
	});
	
});