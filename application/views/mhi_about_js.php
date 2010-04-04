<?php
/**
 * MHI About JS file
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
	var msie6 = $.browser == 'msie' && $.browser.version < 7;

	if (!msie6) {
		var top = $('.side-bar-module').offset().top - parseFloat($('.side-bar-module').css('margin-top').replace(/auto/, 0));
		
		$(window).scroll(function (event) {
			// what the y position of the scroll is
			var y = $(this).scrollTop();
			
			// whether that's below the form
			if (y >= top) {
				// if so, ad the fixed class
				$('.side-bar-module').addClass('fixed');
			} else {
				// otherwise remove it
				$('.side-bar-module').removeClass('fixed');
			}
		});
	} 
});
