<?php
/**
 * Reports listing js file.
 *
 * Handles javascript stuff related to reports list function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
		$(document).ready(function() {
		  
		  // hover functionality for each report
		  $(".rb_report").hover(
		    function () {
          $(this).addClass("hover");
        }, 
        function () {
          $(this).removeClass("hover");
        }
		  );
		  
		  // category tooltip functionality
		  var $tt = $('.r_cat_tooltip');
		  $("a.r_category").hover(
		    function () {
		      // place the category text inside the category tooltip
		      $tt.find('a').html($(this).find('.r_cat-desc').html());
          // display the category tooltip
          $tt.css({
            'left': ($(this).offset().left - 6),
            'top': ($(this).offset().top - 27)
          }).show();
        }, 
        function () {
          $tt.hide();
        }
		  );
		});