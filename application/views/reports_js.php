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
	var deSelectedFilters = [];
	
	// Lat/lon and zoom for the map
	var latitude = <?php echo $latitude; ?>;
	var longitude = <?php echo $longitude; ?>;
	var defaultZoom = <?php echo $default_zoom; ?>;
	
	// Tracks whether the map has already been loaded
	var mapLoaded = 0;
	
	// Map object
	var map = null;
	
	// Map projections
	var proj_4326 = new OpenLayers.Projection('EPSG:4326');
	var proj_900913 = new OpenLayers.Projection('EPSG:900913');
	
	// Map options
	var options = {
		units: "dd",
		numZoomLevels: 18,
		controls:[],
		projection: proj_900913,
		'displayProjection': proj_4326
	};
	
	
	if (urlParameters.length == 0)
	{
		urlParameters = {};
	}
	
	$(document).ready(function() {
		  
		// "Choose Date Range"" Datepicker
		var dates = $( "#report_date_from, #report_date_to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "report_date_from" ? "minDate" : "maxDate",
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
		
		/**
		 * When the date filter button is clicked
		 */
		$("#tooltip-box a.filter-button").click(function(){
			// Change the text
			$(".time-period").text($("#report_date_from").val()+" to "+$("#report_date_to").val());
			
			// Hide the box
			$("#tooltip-box").hide();
			
			report_date_from = $("#report_date_from").val();
			report_date_to = $("#report_date_to").val();
			
			if (report_date_from != '' && report_date_to != '')
			{
				// Add the parameters
				urlParameters["from"] = report_date_from;
				urlParameters["to"] = report_date_to;
				
				// Fetch the reports
				fetchReports();
			}
			
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
		
		/**
		 * Category tooltip functionality
		 */
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
	
	function createMap()
	{
		// Creates the map
		// Load the map
		map = new OpenLayers.Map('rb_map-view', options);
		map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
		
		<?php echo map::layers_js(FALSE); ?>
		map.addLayers(<?php echo map::layers_array(FALSE); ?>);
		map.addControl(new OpenLayers.Control.Navigation());
		map.addControl(new OpenLayers.Control.PanZoomBar());
		map.addControl(new OpenLayers.Control.MousePosition(
				{ div: 	document.getElementById('mapMousePosition'), numdigits: 5 
			}));    
		map.addControl(new OpenLayers.Control.Scale('mapScale'));
		map.addControl(new OpenLayers.Control.ScaleLine());
		map.addControl(new OpenLayers.Control.LayerSwitcher());
		
		// Create a lat/lon object
		myPoint = new OpenLayers.LonLat(longitude, latitude);
		myPoint.transform(proj_4326, map.getProjectionObject());
		
		// Display the map centered on a latitude and longitude
		map.setCenter(myPoint, defaultZoom);
		
		mapLoaded = 1;
	}
	
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
				if ($(this).hasClass("selected"))
				{
					// Add the id of the deselected filter
					deSelectedFilters.push($(this).attr("id"));
				}
				
				$(this).removeClass("selected");
			}
		);
	}
	
	/**
	 * List/map view toggle
	 */
	function addReportViewOptionsEvents()
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
			
			// Check if the map view is active
			if ($("#rb_map-view").css("display") == "block")
			{
				// Check if the map has already been created
				if (mapLoaded == 0)
				{
					createMap();
				}
				
				// Set the current page
				urlParameters["page"] = $(".pager li a.active").html();
				
				// Load the map
				setTimeout(function(){ showIncidentMap() }, 400);
			}
			return false;
		});
	}
	
	/**
	 * Attaches paging events to the paginator
	 */	
	function attachPagingEvents()
	{
		// Add event handler that allows switching between list view and map view
		addReportViewOptionsEvents();
		
		// Remove page links for the metadata pager
		$("ul.pager a").attr("href", "#");
		
		$("ul.pager a").click(function() {
			// Add the clicked page to the url parameters
			urlParameters["page"] = $(this).html();
			
			// Fetch the reports
			fetchReports();
			
		});
	}
	
	/**
	 * Gets the reports using the specified parameters
	 */
	function fetchReports()
	{
		// Reset the map loading tracker
		mapLoaded = 0;
		
		// Remove the deselected report filters
		removeDeselectedReportFilters();
	
		var loadingURL = "<?php echo url::base().'media/img/loading_g.gif'; ?>"
		var statusHtml = "<div style=\"width: 100%; margin-top: 100px;\" align=\"center\">" + 
					"<div><img src=\""+loadingURL+"\" border=\"0\"></div>" + 
					"<p style=\"padding: 10px 2px;\"><h3><?php echo Kohana::lang('ui_main.loading_reports'); ?>...</h3></p>"
					"</div>";
	
		$("#reports-box").html(statusHtml);
		
		// Check if there are any parameters
		if ($.isEmptyObject(urlParameters))
		{
			urlParameters = {show: "all"}
		} 
		
		// Get the content for the new page
		$.get('<?php echo url::site().'reports/fetch_reports'?>',
			urlParameters,
			function(data) {
				if (data != null && data != "" && data.length > 0) {
				
					// Animation delay
					setTimeout(function(){
						$("#reports-box").html(data);
				
						attachPagingEvents();
					}, 400);
				}
			}
		);
	}
	
	/** 
	 * Removes the deselected report filters from the list
	 * of filters for fetching the reports
	 */
	function removeDeselectedReportFilters()
	{
		// Removes a parameter item from urlParameters
		removeParameterItem = function(key, val) {
			if (! $.isEmptyObject(urlParameters))
			{
				// Get the object type
				objectType = Object.prototype.toString.call(urlParameters[key]);
				
				if (objectType == "[object Array]")
				{
					currentItems  = urlParameters[key];
					newItems = [];
					for (var j=0; j < currentItems.length; j++)
					{
						if (currentItems[j] != val)
						{
							newItems.push(currentItems[j]);
						}
					}
					
					if (newItems.length > 0)
					{
						urlParameters[key] = newItems;
					}
					else
					{
						delete urlParameters[key];
					}
				}
				else if (objectType == "[object String]")
				{
					delete urlParameters[key];
				}
			}
		}
		
		if (deSelectedFilters.length > 0)
		{
			for (var i=0; i< deSelectedFilters.length; i++)
			{
				currentItem = deSelectedFilters[i];
				if (currentItem != null && currentItem != '')
				{
					// Check for category filter
					if (currentItem.indexOf('filter_link_cat_') != -1){
						catId = currentItem.substring('filter_link_cat_'.length);
						removeParameterItem("c", catId);
					}
					else if (currentItem.indexOf('filter_link_mode_') != -1)
					{
						modeId = currentItem.substring('filter_link_mode_'.length);
						removeParameterItem("mode", modeId);
					}
					else if (currentItem.indexOf('filter_link_media_') != -1)
					{
						mediaType = currentItem.substring('filter_link_media_'.length);
						removeParameterItem("m", mediaType);
					}
				}
			}
		}
	}
	
	/**
	 * Adds an event handler for the "Filter reports" button
	 */
	function attachFilterReportsAction()
	{
		$("#applyFilters").click(function(){
			
			// 
			// Get all the selected categories
			// 
			var category_ids = [];
			$.each($(".fl-categories li a.selected"), function(i, item){
				itemId = item.id.substring("filter_link_cat_".length);
				// Check if category 0, "All categories" has been selected
				category_ids.push(itemId);
			});
			
			if (category_ids.length > 0)
			{
				urlParameters["c"] = category_ids;
			}
			
			// 
			// Get the incident modes
			// 
			var incidentModes = [];
			$.each($(".fl-incident-mode li a.selected"), function(i, item){
				modeId = item.id.substring("filter_link_mode_".length);
				incidentModes.push(modeId);
			});
			
			if (incidentModes.length > 0)
			{
				urlParameters["mode"] = incidentModes;
			}
			
			// 
			// Get the media type
			// 
			var mediaTypes = [];
			$.each($(".fl-media li a.selected"), function(i, item){
				mediaId = item.id.substring("filter_link_media_".length);
				mediaTypes.push(mediaId);
			});
			
			if (mediaTypes.length > 0)
			{
				urlParameters["m"] = mediaTypes;
			}
			
			
			// Fetch the reports
			fetchReports(urlParameters);
			
		});
	}
	
	/**
	 * Handles display of the incidents current incidents on the map
	 * This method is only called when the map view is selected
	 */
	function showIncidentMap()
	{
		// URL to be used for fetching the incidents
		fetchURL = '<?php echo url::site().'json/index' ;?>';
		
		// Generate the url parameter string
		parameterStr = "";
		$.each(urlParameters, function(key, value){
			if (parameterStr == "")
			{
				parameterStr += key + "=" + value.toString();
			}
			else
			{
				parameterStr += "&" + key + "=" + value.toString();
			}
		});
		
		// Add the parameters to the fetch URL
		fetchURL += '?' + parameterStr;
		
		// Fetch the incidents
		
		// Set the layer name
		var layerName = '<?php echo Kohana::lang('ui_main.reports')?>';
				
		// Get all current layers with the same name and remove them from the map
		currentLayers = map.getLayersByName(layerName);
		for (var i = 0; i < currentLayers.length; i++)
		{
			map.removeLayer(currentLayers[i]);
		}
				
		// Styling for the incidents
		reportStyle = new OpenLayers.Style({
			pointRadius: "8",
			fillColor: "#30E900",
			fillOpacity: "0.7",
			strokeColor: "#197700",
			strokeWidth: 3,
			graphicZIndex: 1
		});
				
		// Apply transform to each feature before adding it to the layer
		preFeatureInsert = function(feature)
		{
			var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
			OpenLayers.Projection.transform(point, proj_4326, proj_900913);
		};
				
		// Create vector layer
		vLayer = new OpenLayers.Layer.Vector(layerName, {
			projection: map.displayProjection,
			preFeatureInsert: preFeatureInsert,
			extractAttributes: true,
			styleMap: new OpenLayers.StyleMap({'default' : reportStyle}),
			strategies: [new OpenLayers.Strategy.Fixed()],
			protocol: new OpenLayers.Protocol.HTTP({
				url: fetchURL,
				format: new OpenLayers.Format.GeoJSON()
			})
		});
				
		// Add the vector layer to the map
		map.addLayer(vLayer);
	}