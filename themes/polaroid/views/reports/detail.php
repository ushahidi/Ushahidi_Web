<div id="main" class="report_detail">

	<div class="left-col">
	
  	 

		
	
		<p class="report-when-where">
			<span class="r_date"><?php echo $incident_time.' '.$incident_date; ?> </span>
			<span class="r_location"><?php echo $incident_location; ?></span>
			<?php Event::run('ushahidi_action.report_meta_after_time', $incident_id); ?>
		</p>
	
		<div class="report-category-list">
		<p>
			<?php
				foreach ($incident_category as $category)
				{
					// don't show hidden categoies
					if ($category->category->category_visible == 0)
					{
						continue;
					}
					if ($category->category->category_image_thumb)
					{
						$style = "background:transparent url(".url::convert_uploaded_to_abs($category->category->category_image_thumb).") 0 0 no-repeat";
					}
					else
					{
						$style = "background-color:#".$category->category->category_color;
					}
					
					?>
					<a href="<?php echo url::site()."reports/?c=".$category->category->id; ?>" title="<?php echo Category_Lang_Model::category_description($category->category_id);; ?>">
						<span class="r_cat-box" style="<?php echo $style ?>">&nbsp;</span>
						<?php echo Category_Lang_Model::category_title($category->category_id); ?>
					</a>
					<?php 
				}
			?>
			</p>
			<?php
			// Action::report_meta - Add Items to the Report Meta (Location/Date/Time etc.)
			Event::run('ushahidi_action.report_meta', $incident_id);
			?>
		</div>
	  
	  <div style="display:none;">
	   <?php
    	  if ($incident_verified)
    		{
    			echo '<p>'.Kohana::lang('ui_main.verified').'</p>';
    		}
    		else
    		{
    			echo '<p>'.Kohana::lang('ui_main.unverified').'</p>';
    		}
  	  ?>
  	  </div>
	
	</div>
	
	<div class="middle-col">
	    <h1 class="report-title"><?php
  			echo $incident_title;

  			// If Admin is Logged In - Allow For Edit Link
  			if ($logged_in)
  			{
  				echo " [&nbsp;<a href=\"".url::site()."admin/reports/edit/".$incident_id."\">".Kohana::lang('ui_main.edit')."</a>&nbsp;]";
  			}
  		?></h1>
  		
	  	<!-- start report description -->
  		<div class="report-description-text">
  			<h5 style="display:none"><?php echo Kohana::lang('ui_main.reports_description');?></h5>
  			<?php echo $incident_description; ?>
  			<br/>


  			<!-- start news source link -->
  			<div class="credibility">
  			<h5><?php echo Kohana::lang('ui_main.reports_news');?></h5>
  			<?php if( count($incident_news) > 0 ) { ?>
  					<?php
  						foreach( $incident_news as $incident_new) 
  						{
  							?>
  							<a href="<?php echo $incident_new; ?>"><?php
  							echo $incident_new;?></a>
  							<br/>
  							<?php	
  						}
  					} ?>
  			<!-- end news source link -->
  			</div>

  			<!-- start additional fields -->
  			<div class="credibility">
  			<h5>Additional Information</a>
  			</h5>
  			<?php
  				echo $custom_forms;

  			?>
  			<br/>
  			<!-- end additional fields -->
  			</div>

  			<?php if ($features_count)
  			{
  				?>
  				<br /><br /><h5><?php echo Kohana::lang('ui_main.reports_features');?></h5>
  				<?php
  				foreach ($features as $feature)
  				{
  					echo ($feature->geometry_label) ?
  					 	"<div class=\"feature_label\"><a href=\"javascript:getFeature($feature->id)\">$feature->geometry_label</a></div>" : "";
  					echo ($feature->geometry_comment) ?
  						"<div class=\"feature_comment\">$feature->geometry_comment</div>" : "";
  				}
  			}?>

  			<div class="credibility">
  				<table class="rating-table" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td><?php echo Kohana::lang('ui_main.credibility');?>:</td>
              <td><a href="javascript:rating('<?php echo $incident_id; ?>','add','original','oloader_<?php echo $incident_id; ?>')"><img id="oup_<?php echo $incident_id; ?>" src="<?php echo url::file_loc('img'); ?>media/img/up.png" alt="UP" title="UP" border="0" /></a></td>
              <td><a href="javascript:rating('<?php echo $incident_id; ?>','subtract','original')"><img id="odown_<?php echo $incident_id; ?>" src="<?php echo url::file_loc('img'); ?>media/img/down.png" alt="DOWN" title="DOWN" border="0" /></a></td>
              <td><a href="" class="rating_value" id="orating_<?php echo $incident_id; ?>"><?php echo $incident_rating; ?></a></td>
              <td><a href="" id="oloader_<?php echo $incident_id; ?>" class="rating_loading" ></a></td>
            </tr>
          </table>
  			</div>
  		</div>

  		<?php
        // Action::report_extra - Allows you to target an individual report right after the description
        Event::run('ushahidi_action.report_extra', $incident_id);

  			// Filter::comments_block - The block that contains posted comments
  			Event::run('ushahidi_filter.comment_block', $comments);
  			echo $comments;
  		?>

  		<?php
  			// Filter::comments_form_block - The block that contains the comments form
  			Event::run('ushahidi_filter.comment_form_block', $comments_form);
  			echo $comments_form;
  		?>
    
	 
	</div>
	
	<div class="right-col">
    
     <div class="polaroid">
		  <p class="picture"> <img style="width:100px; height:100px;" src="https://maps.google.com/maps/api/staticmap?center=<?php echo $incident_latitude.",".$incident_longitude; ?>&zoom=7&markers=size:mid|color:green|<?php echo $incident_latitude.",".$incident_longitude; ?>|<?php echo $incident_latitude.",".$incident_longitude; ?>&size=100x100&sensor=true&&style=visibility:off&style=feature:water|element:geometry|visibility:on|invert_lightness:true|hue:0x0088ff|saturation:-39&style=feature:administrative.country|visibility:on|hue:0xffa200|saturation:64&style=feature:administrative|element:labels|visibility:on|saturation:-15|hue:0x000000|lightness:58&style=feature:administrative.province|element:geometry|visibility:on|saturation:76|hue:0xddff00&style=feature:poi|element:geometry|visibility:on|hue:0x00bbff&style=feature:road|visibility:simplified|lightness:-4&style=feature:landscape.natural|visibility:on|hue:0xffc300|invert_lightness:true|lightness:87" /></p>
      </div>
    
     <?php 
		// if there are images, show them
		if( count($incident_photos) > 0 )
		{ 
			echo '<div id="report-images">';
			foreach ($incident_photos as $photo)
			{
				echo '<a class="photothumb" rel="lightbox-group1" href="'.$photo['large'].'"><img src="'.$photo['thumb'].'"/></a> ';
			};
			echo '</div>';  
		}
	    
	    // if there are videos, show those too
	    if( count($incident_videos) > 0 ) 
	    { 
	      echo '<div id="report-video"><ol>';

          // embed the video codes
          foreach( $incident_videos as $incident_video) 
          {
            echo '<li>';
            $videos_embed->embed($incident_video,'');
            echo '</li>';
          };
  			echo '</ol></div>';
        
	    } 
	    ?>
     
		<div class="report-media-box-content">
			
			<div id="report-map" class="report-map">
				<div class="map-holder" id="map"></div>
        <ul class="map-toggles">
          <li><a href="#" class="smaller-map">Smaller map</a></li>
          <li style="display:block;"><a href="#" class="wider-map">Wider map</a></li>
          <li><a href="#" class="taller-map">Taller map</a></li>
          <li><a href="#" class="shorter-map">Shorter Map</a></li>
        </ul>
        <div style="clear:both"></div>
			</div>			
		</div>
		<div class="report-additional-reports">
			<h4><?php echo Kohana::lang('ui_main.additional_reports');?></h4>
			<?php foreach($incident_neighbors as $neighbor) { ?>
			  <div class="rb_report">
  			  <h5><a href="<?php echo url::site(); ?>reports/view/<?php echo $neighbor->id; ?>"><?php echo $neighbor->incident_title; ?></a></h5>
  			  <p class="r_date r-3 bottom-cap"><?php echo date('H:i M d, Y', strtotime($neighbor->incident_date)); ?></p>
  			  <p class="r_location"><?php echo $neighbor->location_name.", ".round($neighbor->distance, 2); ?> Kms</p>
  			</div>
      <?php } ?>
		</div>

	</div>
	
	<div style="clear:both;"></div>
	
	
	
	
</div>
