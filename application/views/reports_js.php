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
 * @module     Reports Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
	// Tracks the current URL parameters
	var urlParameters = <?php echo $url_params; ?>;
	if (urlParameters.length == 0)
	{
		urlParameters = {};
	}
	
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
		  
		/**
		 * Date range datepicker box functionality
		 * Show the box when clicking the "change time" link
		 */
		$(".btn-change-time").click(function(){
			$("#tooltip-box").css({
				'left': ($(this).offset().left - 80),
				'top': ($(this).offset().right)
			}).show();
			
	        return false;
		});
			
		  	
		/**
		 * Change time period text in page header to reflect what was clicked
		 * then hide the date range picker box
		 */
		$(".btn-date-range").click(function(){
			// Change the text
			$(".time-period").text($(this).attr("title"));
			
			// Update the "active" state
			$(".btn-date-range").removeClass("active");
			
			$(this).addClass("active");
			
			// Hide the box
			$("#tooltip-box").hide();
			
			return false;
		});
		
		$("#tooltip-box a.filter-button").click(function(){
			// Change the text
			$(".time-period").text($("#from").val()+" to "+$("#to").val());
			
			// Hide the box
			$("#tooltip-box").hide();
			
			return false;
		});
		
		/**
		 * Hover functionality for each report
		 */
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
				// Place the category text inside the category tooltip
				$tt.find('a').html($(this).find('.r_cat-desc').html());
				
				// Display the category tooltip
				$tt.css({
					'left': ($(this).offset().left - 6),
					'top': ($(this).offset().top - 27)
				}).show();
			}, 
			
			function () {
				$tt.hide();
			}
		);

		/**
		 * Show/hide categories and location for a report
		 */
		$("a.btn-show").click(function(){
			var $reportBox = $(this).attr("href");
		
			// Hide self
			$(this).hide();
			if ($(this).hasClass("btn-more"))
			{
				// Show categories and location
				$($reportBox + " .r_categories, " + $reportBox + " .r_location").slideDown();
			
				// Show the "show less" link
				$($reportBox + " a.btn-less").show();
			}
			else if ($(this).hasClass("btn-less"))
			{
				// Hide categories and location
				$($reportBox + " .r_categories, " + $reportBox + " .r_location").slideUp();
			
				// Show the "show more" link
				$($reportBox + " a.btn-more").attr("style","");
			};
		
			return false;		    
		});

		// Initialize accordion for Report Filters
		$( "#accordion" ).accordion({autoHeight: false});
		
		// 	Events for toggling the report filters
		addToggleReportsFilterEvents();
		
		// Attach paging events to the paginator
		attachPagingEvents();
		
		// Attach the "Filter Reports" action
		attachFilterReportsAction();
	});
	
	function addToggleReportsFilterEvents()
	{
		/**
		 * onclick, remove all highlighting on filter list items and hide the item clicked
		 */
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
	}
	
	/**
	 * List/map view toggle
	 */
	function addToggleViewOptionEvents()
	{
		$("#reports-box .report-list-toggle a").click(function(){
			// Hide both divs
			$("#rb_list-view, #rb_map-view").hide();
			
			// Show the appropriate div
			$($(this).attr("href")).show();
			
			// Remove the class "selected" from all parent li's
			$("#reports-box .report-list-toggle a").parent().removeClass("active");
			
			// Add class "selected" to both instances of the clicked link toggle
			$("."+$(this).attr("class")).parent().addClass("active");
			
			return false;
		});
	}
	
	/**
	 * Attaches paging events to the paginator
	 */	
	function attachPagingEvents()
	{
		// Add event handler that allows switching between list view and map view
		addToggleViewOptionEvents();
		
		// Remove page links for the metadata pager
		$("ul.pager a").attr("href", "#");
		
		$("ul.pager a").click(function() {
			
			
			// Add the clicked page to the url parameters
			urlParameters["page"] = $(this).html();
			
			// Fetch the reports
			fetchReports(urlParameters);
			
		});
	}
	
	/**
	 * Gets the reports using the specified parameters
	 */
	function fetchReports(parameters)
	{
		var loadingURL = "<?php echo url::base().'media/img/loading_g.gif'; ?>"
		var statusHtml = "<div style=\"width: 100%; margin-top: 100px;\" align=\"center\">" + 
					"<div><img src=\""+loadingURL+"\" border=\"0\"></div>" + 
					"<p style=\"padding: 10px 2px;\"><h3><?php echo Kohana::lang('ui_main.loading_reports'); ?>...</h3></p>"
					"</div>";
		
		$("#reports-box").html(statusHtml);
		// $("#reports-box").fadeOut('slow');
		
		// Get the content for the new page
		$.get('<?php echo url::site().'reports/fetch_reports'?>',
			urlParameters,
			function(data) {
				if (data != null && data != "" && data.length > 0) {
					
					// Animation delay
					setTimeout(function(){
						// $("#reports-box").fadeIn('slow');
						$("#reports-box").html(data);
					
						attachPagingEvents();
					}, 400);
				}
			}
		);
	}
	
	/**
	 * Adds an event handler for the "Filter reports" button
	 */
	function attachFilterReportsAction()
	{
		$("#applyFilters").click(function(){
			// Get all the selected categories
			var category_ids = [];
			$.each($(".fl-categories li a.selected"), function(i, item){
				itemId = item.id.substring("filter_link_cat_".length);
				category_ids.push(itemId);
			});
			
			urlParameters = {"c[]" : category_ids};
			
			// Fetch the reports
			fetchReports(urlParameters);
			
		});
	}