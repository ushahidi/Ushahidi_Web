
<div style="display:none;">
<?php echo Kohana::lang('ui_main.title'); ?>
<?php echo Kohana::lang('ui_main.location'); ?>
<?php echo Kohana::lang('ui_main.date'); ?>
<?php echo Kohana::lang('ui_main.category'); ?>

<a class="more" href="<?php echo url::site() . 'reports/' ?>"><?php echo Kohana::lang('ui_main.view_more'); ?></a>

</div>
  <!-- TODO: Make these filters dynamic, also need to add dynamic category filtering. -->
  <p style="display:none;"><strong>Filter By Month:</strong> 
    <span id="filters" class="option-set" data-filter-group="date">
      <a href="#filter" data-filter="*" class="selected">All</a> | 
      <!--<a href="#filter" data-filter=".Jan">Jan-</a> | 
      <a href="#filter" data-filter=".Feb">Feb</a> | 
      <a href="#filter" data-filter=".Mar">Mar</a> | 
      <a href="#filter" data-filter=".Apr">Apr</a> | 
      <a href="#filter" data-filter=".May">May</a> | 
      <a href="#filter" data-filter=".Jun">Jun</a> |--> 
      <a href="#filter" data-filter=".Jul" class="">Jul</a> | 
      <a href="#filter" data-filter=".Aug" class="">Aug</a> | 
      <a href="#filter" data-filter=".Sep" class="">Sep</a> <!-- | 
      <a href="#filter" data-filter=".Oct">Oct</a> | 
      <a href="#filter" data-filter=".Nov">Nov</a> | 
      <a href="#filter" data-filter=".Dec">Dec</a> -->
    </span>
</p>
  <div class="polaroids">
		<?php
		if ($total_items == 0)
		{
			?>
			<p><?php echo Kohana::lang('ui_main.no_reports'); ?></p>
			<?php
		}
		foreach ($incidents as $incident)
		{
			$incident_id = $incident->id;
			$incident_title = text::limit_chars($incident->incident_title, 20, '...', False);
			$incident_date = $incident->incident_date;
			$incident_date = date('l j F Y', strtotime($incident->incident_date));
			$incident_month = date('M', strtotime($incident->incident_date));
			$incident_location = $incident->location->location_name;
			$incident_lat = $incident->location->latitude;
			$incident_lon = $incident->location->longitude;
			
			$incident_category = $incident->incident_category;
		?>
      
      <?php
        $isotope_js_filters = $incident_month;
        
				foreach($incident_category as $category) 
				{

					// don't show hidden categoies
					if($category->category->category_visible == 0)
					{
						continue;
					}
					else
					{
					  $isotope_js_filters = $isotope_js_filters." ".$category->category->category_title;
				  }
				}
			?>
		  <div class="polaroid <?php echo $isotope_js_filters ?>">
		  <p class="picture"> <a rel="the-roids" class="modal-that-junk" href="<?php echo url::site() . 'reports/view/' . $incident_id; ?>"><img style="width:100px; height:100px;" src="https://maps.google.com/maps/api/staticmap?center=<?php echo $incident_lat.",".$incident_lon; ?>&zoom=7&markers=size:mid|color:green|<?php echo $incident_lat.",".$incident_lon; ?>|<?php echo $incident_lat.",".$incident_lon; ?>&size=100x100&sensor=true&&style=visibility:off&style=feature:water|element:geometry|visibility:on|invert_lightness:true|hue:0x0088ff|saturation:-39&style=feature:administrative.country|visibility:on|hue:0xffa200|saturation:64&style=feature:administrative|element:labels|visibility:on|saturation:-15|hue:0x000000|lightness:58&style=feature:administrative.province|element:geometry|visibility:on|saturation:76|hue:0xddff00&style=feature:poi|element:geometry|visibility:on|hue:0x00bbff&style=feature:road|visibility:simplified|lightness:-4&style=feature:landscape.natural|visibility:on|hue:0xffc300|invert_lightness:true|lightness:87" />
      <span><?php echo $incident_title ?></span></a></p>
			
			<div class="extra-stuff" id="picture-<?php echo $incident_id ?>" style="display:none">
			<p>Location: <?php echo $incident_location ?></p>
			<p>Date: <?php echo $incident_date; ?></p>
			  <p>Categories: 
    			<?php
    				foreach($incident_category as $category) 
    				{

    					// don't show hidden categoies
    					if($category->category->category_visible == 0)
    					{
    						continue;
    					}

    				  if ($category->category->category_image_thumb)
    					{
    					?>
    					<!-- removing category links for now -->
    					<!-- category href: <?php echo url::site()."reports/?c=".$category->category->id; ?> -->
    					<a href="#"><span class="r_cat-box" style="background:transparent url(<?php echo url::base().Kohana::config('upload.relative_directory')."/".$category->category->category_image_thumb; ?>) 0 0 no-repeat;">&nbsp;</span> <?php echo $category->category->category_title; ?></a>

    					<?php 
    					}
    					else
    					{
    					?>
    					  <!-- removing category links for now -->
    					  <!-- category href: <?php echo url::site()."reports/?c=".$category->category->id; ?> -->
    					  <a href="#"><span class="r_cat-box" style="background-color:#<?php echo $category->category->category_color; ?>">&nbsp;</span> <?php echo $category->category->category_title; ?></a>
    				  <?php
    				  }
    				}
    			?>
    			</p>
    			
    			</div>
		</div>
		<?php
		}
		?>
		
    <div style="clear:both;"></div>

