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
		  
		  // "Choose Date Range"" Datepicker
		  var dates = $( "#from, #to" ).datepicker({
      			defaultDate: "+1w",
      			changeMonth: true,
      			numberOfMonths: 1,
      			onSelect: function( selectedDate ) {
      				var option = this.id == "from" ? "minDate" : "maxDate",
      					instance = $( this ).data( "datepicker" ),
      					date = $.datepicker.parseDate(
      						instance.settings.dateFormat ||
      						$.datepicker._defaults.dateFormat,
      						selectedDate, instance.settings );
      				dates.not( this ).datepicker( "option", option, date );
      			}
      		});
		  
		  // date range datepicker box functionality
			// show the box when clicking the "change time" link
		  $(".btn-change-time").click(function(){
				$("#tooltip-box").css({
          'left': ($(this).offset().left - 80),
          'top': ($(this).offset().right)
        }).show();
        return false;
			});
			
		  	
			// change time period text in page header to reflect what was clicked
			// then hide the date range picker box
			$(".btn-date-range").click(function(){
				// change the text
				$(".time-period").text($(this).attr("title"));
			  // update the "active" state
				$(".btn-date-range").removeClass("active");
				$(this).addClass("active");
				// hide the box
				$("#tooltip-box").hide();
				return false;
			});
		  $("#tooltip-box a.filter-button").click(function(){
		    // change the text
		    $(".time-period").text($("#from").val()+" to "+$("#to").val());
		    // hide the box
		    $("#tooltip-box").hide();
		    return false;
		  });
		
			  
		  // list/map view toggle
			$("#reports-box .report-list-toggle a").click(function(){
			  // hide both divs
			  $("#rb_list-view, #rb_map-view").hide();
			  // show the appropriate div
			  $($(this).attr("href")).show();
			  // remove the class "selected" from all parent li's
			  $("#reports-box .report-list-toggle a").parent().removeClass("active");
			  // add class "selected" to both instances of the clicked link toggle
			  $("."+$(this).attr("class")).parent().addClass("active");
			  return false;
			});
			
		  
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
		
			// show/hide categories and location for a report
			$("a.btn-show").click(function(){
			  var $reportBox = $(this).attr("href");
			  // hide self
			  $(this).hide();
			  if ($(this).hasClass("btn-more"))
			  {
			    //show categories and location
  			  $($reportBox + " .r_categories, " + $reportBox + " .r_location").slideDown();
  			  // show the "show less" link
  			  $($reportBox + " a.btn-less").show();
			  }
			  else if ($(this).hasClass("btn-less"))
			  {
			    //hide categories and location
  			  $($reportBox + " .r_categories, " + $reportBox + " .r_location").slideUp();
  			  // show the "show more" link
  			  $($reportBox + " a.btn-more").attr("style","");
			  };
			  return false;		    
			});
		
			// initialize accordion for Report Filters
			$( "#accordion" ).accordion({autoHeight: false});
			
			
			//---For DEMO PURPOSES ONLY-----//
			
			// onclick, remove all highlighting on filter list items and hide the item clicked
			$("a.f-clear").click(function(){
				$(".filter-list li a").removeClass("selected");
				$(this).addClass("hide");
			});
			
			// toggle highlighting on the filter lists
			$(".filter-list li a").toggle(
			  function(){
			    $(this).addClass("selected");
			  },
			  function(){
			    $(this).removeClass("selected"); 
			  }
			);
			
			//---------END DEMO CODE---------//
		
		});