<div id="content">
	<div class="content-bg">
		<!-- start reports block -->
		<div class="big-block">
			<h1 class="heading">
				<?php $timeframe_title =  date('M d, Y', $oldest_timestamp).' through '.date('M d, Y', $latest_timestamp); ?>
				Showing Reports from <span class="time-period">
					<?php echo $timeframe_title; ?>
					</span> 
				<a href="#" class="btn-change-time ic-time">change date range</a>
			</h1>
			
			<div id="tooltip-box">
				<div class="tt-arrow"></div>
				<ul class="inline-links">
					<li><a title="<?php echo $timeframe_title; ?>" class="btn-date-range active" href="#">All Time</a></li>
					<li><a title="Today" class="btn-date-range" href="#">Today</a></li>
					<li><a title="This Week" class="btn-date-range" href="#">This Week</a></li>
					<li><a title="This Month" class="btn-date-range" href="#">This Month</a></li>
				</ul>
				
				<p class="labeled-divider"><span>Or choose your own date range:</span></p>
				<form>
					<table>
						<tr>
							<td><strong>
								<?php echo Kohana::lang('ui_admin.from')?>:</strong><input id="report_date_from" type="text" style="width:78px" />
							</td>
							<td>
								<strong><?php echo ucfirst(strtolower(Kohana::lang('ui_admin.to'))); ?>:</strong>
								<input id="report_date_to" type="text" style="width:78px" />
							</td>
							<td valign="bottom">
								<a href="#" id="applyDateFilter" class="filter-button" style="position:static;"><?php echo Kohana::lang('ui_main.go')?></a>
							</td>
						</tr>
					</table>              
				</form>
			</div>

			<div style="overflow:auto;">
				<!-- reports-box -->
				<div id="reports-box">
					<?php echo $report_listing_view; ?>
				</div>
				<!-- end #reports-box -->
				
				<div id="filters-box">
					<h2>Filter Reports By</h2>
					<div id="accordion">
						
						<h3><a href="#" class="small-link-button f-clear reset">clear</a><a class="f-title" href="#">Category</a></h3>
						<div class="f-category-box">
			          		<ul class="filter-list fl-categories" id="category-filter-list">
							</ul>
						</div>
						
						<h3><a class="f-title" href="#">Location</a></h3>
						
						<div class="f-location-box">
							<p><strong>Choose a location:</strong><img src="<?php echo url::base(); ?>themes/front-end-reports/images/location-filter-placeholder.png" /></p>
							<p><strong>Choose a radius:</strong><br /><img src="<?php echo url::base();?>themes/front-end-reports/images/radius-selection-placeholder.png" /></p>
							<p><a class="reset" href="#">Reset</a></p>
						</div>
						
						<h3><a href="#" class="small-link-button f-clear reset">clear</a><a class="f-title" href="#">Type</a></h3>
						<div class="f-type-box">
							<ul class="filter-list fl-type">
							<?php // Get the list of services ?>
								<li>
									<a href="#">
										<span class="item-icon ic-sms">&nbsp;</span>
										<span class="item-title">SMS</span>
										<span class="item-count">42342</span>
									</a>
								</li>
								<li>
									<a href="#" class="selected">
										<span class="item-icon ic-email">&nbsp;</span>
										<span class="item-title">Email</span>
										<span class="item-count">66</span>
									</a>
								</li>
								<li>
									<a href="#">
										<span class="item-icon ic-twitter">&nbsp;</span>
										<span class="item-title">Twitter</span>
										<span class="item-count">634</span>
									</a>
								</li>
								<li>
									<a href="#">
										<span class="item-icon ic-webform">&nbsp;</span>
										<span class="item-title">Web Form</span>
										<span class="item-count">367</span>
									</a>
								</li>
								<li>
									<a href="#" class="selected">
										<span class="item-icon ic-facebook">&nbsp;</span>
										<span class="item-title">Facebook</span>
										<span class="item-count">367</span>
									</a>
								</li>
								<li>
									<a href="#">
										<span class="item-icon ic-youtube">&nbsp;</span>
										<span class="item-title">Youtube</span>
										<span class="item-count">367</span>
									</a>
								</li>
								<li>
									<a href="#">
										<span class="item-icon ic-rss">&nbsp;</span>
										<span class="item-title">RSS</span>
										<span class="item-count">367</span>
									</a>
								</li>
							</ul>
						</div>
						
						<h3><a class="f-title" href="#">Verification</a></h3>
						
						<div class="f-verification-box">
							<ul class="filter-list fl-verification">
								<li>
									<a href="#">
										<span class="item-icon ic-verified">&nbsp;</span>
										<span class="item-title">Verified</span>
										<span class="item-count">42342</span>
									</a>
								</li>
								<li>
									<a href="#">
										<span class="item-icon ic-unverified">&nbsp;</span>
										<span class="item-title">Unverified</span>
										<span class="item-count">22452</span>
									</a>
								</li>
							</ul>
						</div>
						
						<h3><a href="#" class="small-link-button f-clear reset">clear</a><a class="f-title" href="#">Media</a></h3>
						<div class="f-media-box">
							<p>Filter reports that contain...</p>
							<ul class="filter-list fl-media">
								<li>
									<a href="#">
										<span class="item-icon ic-photos">&nbsp;</span>
										<span class="item-title">Photos</span>
										<span class="item-count">0</span>
									</a>
								</li>
								<li>
									<a href="#" class="selected">
										<span class="item-icon ic-videos">&nbsp;</span>
										<span class="item-title">Videos</span>
										<span class="item-count">252</span>
									</a>
								</li>
								<li>
									<a href="#">
										<span class="item-icon ic-news">&nbsp;</span>
										<span class="item-title">News Source Links</span>
										<span class="item-count">4</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<!-- end #accordion -->
					
					<div id="filter-controls">
						<p><a href="#" class="small-link-button reset">reset all filters</a> <a href="#" id="applyFilters" class="filter-button">Filter Reports</a></p>
					</div>          
				</div>
				<!-- end #filters-box -->
			</div>
      
			<div style="display:none">
				<?php
					// Filter::report_stats - The block that contains reports list statistics
					Event::run('ushahidi_filter.report_stats', $report_stats);
					echo $report_stats;
				?>
			</div>

		</div>
		<!-- end reports block -->
		
	</div>
	<!-- end content-bg -->
</div>


<!-- Begin Reports Listing Javascript -->
<script type="text/javascript">
	$(document).ready(function(){
		
		// START: Populate category filter list
		function populateCategoryFilter()
		{
			var cat_class = '';
			var cat_selected = '';
			
			var categoryHtml = "<li>" + 
						"<a href=\"#\"><span class=\"item-swatch\" style=\"background-color:#CC0000\">&nbsp;</span>" + 
						"<span class=\"item-title\">All Categories</span><span class=\"item-count\" id=\"all_report_count\">0</span>" +
						"</a></li>";
		
			$('#category-filter-list').html(categoryHtml);
			
			// Grab all the categories
			categories_json_url = '<?php echo url::site(); ?>api/?task=categories';
			
			$.getJSON(categories_json_url, function(data) {
				$.each(data.payload.categories, function(i,item){
					
					// 	Get the category class
					cat_class = (item.category.parent_id != 0)? 'report-listing-category-child' : '';
					
					// TODO: set var cat_selected to "selected" if it has been selected
					
					categoryHtml = "<li class=\""+cat_class+"\">" +
							"<a href=\"#\" class=\""+cat_selected+"\" id=\"filter_link_cat_"+item.category.id+"\">" +
							"<span class=\"item-swatch\" style=\"background-color:#"+item.category.color+"\">&nbsp;</span>" +
							"<span class=\"item-title\">"+item.category.title+"</span>" +
							"<span class=\"item-count\" id=\"report_count_cat_"+item.category.id+"\">0</span>" +
							"</a></li>";
					
					$('#category-filter-list').append(categoryHtml);
					
					addToggleReportsFilterEvents();
					
				},populateCategoryFilterCounts());
			});
		}
		
		populateCategoryFilter();
		// END: Populate category filter list
		
		// START: Populate report counts for category filter
		function populateCategoryFilterCounts()
		{
			// Grab report counts for the categories
			catcount_json_url = '<?php echo url::site(); ?>api/?task=incidents&by=catcount';
			$.getJSON(catcount_json_url, function(data) {
				$.each(data.payload.category_counts, function(i,item){
					$('#report_cat_count_'+item.category_id).ready(function(){
						$('#report_count_cat_'+item.category_id).html(item.reports);
					});
				});
				
				// Display total
				$('#all_report_count').html(data.payload.total_reports);
				
			});
		}
		
		//populateCategoryFilterCounts();
		// END: Populate report counts for category filter
		
	});
</script>
<!-- End Reports Listing Javascript -->


