<div id="content">
	<div class="content-bg">
		<!-- start reports block -->
		<div class="big-block">
		  
		  <h1 class="heading">
        Showing Reports from <span class="time-period">Oct 4, 2010 through Jan 23, 2012</span> 
        <a href="#" class="btn-change-time ic-time">change date range</a>
       </h1>
      	
			<div id="tooltip-box">
         <div class="tt-arrow"></div>
          <ul class="inline-links">
             <li><a title="Oct 4, 2010 through Jan 23, 2012" class="btn-date-range active" href="#">All Time</a></li>
       			<li><a title="Today" class="btn-date-range" href="#">Today</a></li>
       			<li><a title="This Week" class="btn-date-range" href="#">This Week</a></li>
       			<li><a title="This Month" class="btn-date-range" href="#">This Month</a></li>
           </ul>
           <p class="labeled-divider"><span>Or choose your own date range:</span></p>
   			  <form>
             <table>
               <tr>
                 <td><strong>From:</strong><input id="from" type="text" style="width:78px" /></td>
                 <td><strong>To:</strong><input id="to" type="text" style="width:78px" /></td>
                 <td valign="bottom"><a href="#" class="filter-button" style="position:static;">Go</a></td>
               </tr>
             </table>              
           </form>
       </div>

      <div style="overflow:auto;">
        <div id="reports-box">
          <div class="rb_nav-controls r-5">
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>
                  <ul class="link-toggle report-list-toggle lt-icons-and-text">
                    <li class="active"><a href="#rb_list-view" class="list">List</a></li>
                    <li><a href="#rb_map-view" class="map">Map</a></li>
                  </ul>
                </td>
				<td>
					<?php echo $pagination; ?>
				</td>
                <td><?php echo $stats_breadcrumb; ?></td>
                <td class="last">
                  <ul class="link-toggle lt-icons-only">
                    <li><a href="#" class="prev">Previous</a></li>
                    <li><a href="#" class="next">Next</a></li>
                  </ul>
                </td>
              </tr>
            </table>
          </div>
          <div class="rb_list-and-map-box">
            <div id="rb_list-view">
              <?php
					foreach ($incidents as $incident)
					{
						$incident = ORM::factory('incident', $incident->incident_id);
      					$incident_id = $incident->id;
      					$incident_title = $incident->incident_title;
      					$incident_description = $incident->incident_description;
      					//$incident_category = $incident->incident_category;
      					// Trim to 150 characters without cutting words
      					// XXX: Perhaps delcare 150 as constant

      					$incident_description = text::limit_chars(strip_tags($incident_description), 140, "...", true);
      					$incident_date = date('H:i M d, Y', strtotime($incident->incident_date));
      					//$incident_time = date('H:i', strtotime($incident->incident_date));
      					$location_id = $incident->location_id;
      					$location_name = $incident->location->location_name;
      					$incident_verified = $incident->incident_verified;

      					if ($incident_verified)
      					{
      						$incident_verified = '<span class="r_verified">'.Kohana::lang('ui_main.verified').'</span>';
      						$incident_verified_class = "verified";
      					}
      					else
      					{
      						$incident_verified = '<span class="r_unverified">'.Kohana::lang('ui_main.unverified').'</span>';
      						$incident_verified_class = "unverified";
      					}

      					$comment_count = $incident->comment->count();

      					$incident_thumb = url::base()."media/img/report-thumb-default.jpg";
      					$media = $incident->media;
      					if ($media->count())
      					{
      						foreach ($media as $photo)
      						{
      							if ($photo->media_thumb)
      							{ // Get the first thumb
      								$prefix = url::base().Kohana::config('upload.relative_directory');
      								$incident_thumb = $prefix."/".$photo->media_thumb;
      								break;
      							}
      						}
      					}
      					?>
      					<div id="<?php echo $incident_id ?>" class="rb_report <?php echo $incident_verified_class; ?>">

      						<div class="r_media">
      							<p class="r_photo"> <a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>">
      								<img src="<?php echo $incident_thumb; ?>" height="59" width="89" /> </a>
      							</p>

      							<!-- Only show this if the report has a video -->
      							<p class="r_video" style="display:none;"><a href="#">Video</a></p>

      							<!-- Category Selector -->
      							<div class="r_categories">
      								<h4><?php echo Kohana::lang('ui_main.categories'); ?></h4>
      								<?php
      								foreach ($incident->category AS $category)
      								{

      									//don't show hidden categories
      									if($category->category_visible == 0)
      									{
      										continue;
      									}

      									if ($category->category_image_thumb)
      									{
      										?>
      										<a class="r_category" href="<?php echo url::site(); ?>reports/?c=<?php echo $category->id; ?>"><span class="r_cat-box"><img src="<?php echo url::base().Kohana::config('upload.relative_directory')."/".$category->category_image_thumb; ?>" height="16" width="16" /></span> <span class="r_cat-desc"><?php echo $localized_categories[(string)$category->category_title];?></span></a>
      										<?php
      									}
      									else
      									{
      										?>
      										<a class="r_category" href="<?php echo url::site(); ?>reports/?c=<?php echo $category->id; ?>"><span class="r_cat-box" style="background-color:#<?php echo $category->category_color;?>;"></span> <span class="r_cat-desc"><?php echo $localized_categories[(string)$category->category_title];?></span></a>
      										<?php
      									}
      								}
      								?>
      							</div>
      						</div>

      						<div class="r_details">
      							<h3><a class="r_title" href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>"><?php echo $incident_title; ?></a> <a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>#discussion" class="r_comments"><?php echo $comment_count; ?></a> <?php echo $incident_verified; ?></h3>
      							<p class="r_date r-3 bottom-cap"><?php echo $incident_date; ?></p>
      							<div class="r_description"> <?php echo $incident_description; ?>  
      							  <a class="btn-show btn-more" href="#<?php echo $incident_id ?>">More Info &raquo;</a> 
      							  <a class="btn-show btn-less" href="#<?php echo $incident_id ?>">&laquo; Less Info</a> </div>
      							<p class="r_location"><a href="<?php echo url::site(); ?>reports/?l=<?php echo $location_id; ?>"><?php echo $location_name; ?></a></p>
      						</div>
      					</div>
      				<?php } ?>
             
            </div>
            <div id="rb_map-view" style="display:none;">
              <img src="/ushahidi/themes/front-end-reports/images/map-placeholder.jpg" />
            </div>
          </div>
          <div class="rb_nav-controls r-5">
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>
                  <ul class="link-toggle report-list-toggle lt-icons-and-text">
                    <li class="active"><a href="#rb_list-view" class="list">List</a></li>
                    <li><a href="#rb_map-view" class="map">Map</a></li>
                  </ul>
                </td>
                <td><?php echo $pagination; ?></td>
                <td><?php echo $stats_breadcrumb; ?></td>
                <td class="last">
                  <ul class="link-toggle lt-icons-only">
                    <li><a href="#" class="prev">Previous</a></li>
                    <li><a href="#" class="next">Next</a></li>
                  </ul>
                </td>
              </tr>
            </table>
          </div>
        </div> <!-- end #reports-box -->
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
          	  <p><strong>Choose a location:</strong><img src="/ushahidi/themes/front-end-reports/images/location-filter-placeholder.png" /></p>
          	  <p><strong>Choose a radius:</strong><br /><img src="/ushahidi/themes/front-end-reports/images/radius-selection-placeholder.png" /></p>
          	  <p><a class="reset" href="#">Reset</a></p>
          	</div>
          	<h3><a href="#" class="small-link-button f-clear reset">clear</a><a class="f-title" href="#">Type</a></h3>
          	<div class="f-type-box">
          		<ul class="filter-list fl-type">
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
          </div> <!-- end #accordion -->
          <div id="filter-controls">
            
            <p><a href="#" class="small-link-button reset">reset all filters</a> <a href="#" class="filter-button">Filter Reports</a></p>

          </div>          
        </div> <!-- end #filters-box -->
      </div>
      
      <div style="display:none">
		  
			<?php
			// Filter::report_stats - The block that contains reports list statistics
			Event::run('ushahidi_filter.report_stats', $report_stats);
			echo $report_stats;
			?>
			<h1><?php echo Kohana::lang('ui_main.reports').": ";?> <?php echo ($category_title) ? " in $category_title" : ""?> <?php echo $pagination_stats; ?></h1>
			<div style="clear:both;"></div>
			<div class="r_cat_tooltip"> <a href="#" class="r-3">2a. Structures a risque | Structures at risk</a> </div>
			<div class="reports-box">
				
			</div>
			<?php echo $pagination; ?>
			
			</div>
			
		</div>
		<!-- end reports block -->
	</div>
</div>

<!-- Begin Reports Listing Javascript -->
<script type="text/javascript">
	$(document).ready(function(){
		
		// START: Populate category filter list
		function populateCategoryFilter(){
			
			var cat_class = '';
			var cat_selected = '';
		
			$('#category-filter-list').html('<li><a href="#"><span class="item-swatch" style="background-color:#CC0000">&nbsp;</span><span class="item-title">All Categories</span><span class="item-count" id="all_report_count">0</span></a></li>');
			
			// Grab all the categories
			categories_json_url = '<?php echo url::site(); ?>api/?task=categories';
			$.getJSON(categories_json_url, function(data) {
				$.each(data.payload.categories, function(i,item){
					
					if(item.category.parent_id != 0){
						cat_class = 'report-listing-category-child';
					}else{
						cat_class = '';
					}
					
					// TODO: set var cat_selected to "selected" if it has been selected
					
					$('#category-filter-list').append('<li class="'+cat_class+'"><a href="#" class="'+cat_selected+'" id="'+item.category.id+'_cat_filter_link"><span class="item-swatch" style="background-color:#'+item.category.color+'">&nbsp;</span><span class="item-title">'+item.category.title+'</span><span class="item-count" id="'+item.category.id+'_cat_report_count">0</span></a></li>');
					
				},populateCategoryFilterCounts());
			});
		}
		populateCategoryFilter();
		// END: Populate category filter list
		
		// START: Populate report counts for category filter
		function populateCategoryFilterCounts(){
			// Grab report counts for the categories
			catcount_json_url = '<?php echo url::site(); ?>api/?task=incidents&by=catcount';
			$.getJSON(catcount_json_url, function(data) {
				$.each(data.payload.category_counts, function(i,item){
					$('#'+item.category_id+'_cat_report_count').ready(function(){
						$('#'+item.category_id+'_cat_report_count').html(item.reports);
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


