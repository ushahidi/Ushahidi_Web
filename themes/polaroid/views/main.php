<!-- main body -->
<div id="main" class="clearingfix">
	<div id="mainmiddle">

	<?php if($site_message != '') { ?>
		<div class="green-box">
			<h3><?php echo $site_message; ?></h3>
		</div>
	<?php } ?>

		<!-- right column -->
		<div id="report-map-filter-box" class="clearingfix">
	    <a class="btn toggle" id="filter-menu-toggle" class="" href="#the-filters"><?php echo Kohana::lang('ui_main.filter_reports_by'); ?><span class="btn-icon ic-right">&raquo;</span></a>
	    
	    <!-- filters box -->
	    <div id="the-filters" class="map-menu-box">
	      
        <!-- report category filters -->
        <div id="report-category-filter">
    			<h3><?php echo Kohana::lang('ui_main.category');?></h3>
			
    			<ul id="category_switch" class="category-filters">
    				<li><a class="active" id="cat_0" href="#"><span class="swatch" style="background-color:<?php echo "#".$default_map_all;?>"></span><span class="category-title"><?php echo Kohana::lang('ui_main.all_categories');?></span></a></li>
    				<?php
    					foreach ($categories as $category => $category_info)
    					{
    						$category_title = $category_info[0];
    						$category_color = $category_info[1];
    						$category_image = '';
    						$color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
    						if($category_info[2] != NULL) {
    							$category_image = html::image(array(
    								'src'=>$category_info[2],
    								'style'=>'float:left;padding-right:5px;'
    								));
    							$color_css = '';
    						}
    						echo '<li><a href="#" id="cat_'. $category .'"><span '.$color_css.'>'.$category_image.'</span><span class="category-title">'.$category_title.'</span></a>';
    						// Get Children
    						echo '<div class="hide" id="child_'. $category .'">';
                                                    if( sizeof($category_info[3]) != 0)
                                                    {
                                                        echo '<ul>';
                                                        foreach ($category_info[3] as $child => $child_info)
                                                        {
                                                                $child_title = $child_info[0];
                                                                $child_color = $child_info[1];
                                                                $child_image = '';
                                                                $color_css = 'class="swatch" style="background-color:#'.$child_color.'"';
                                                                if($child_info[2] != NULL) {
                                                                        $child_image = html::image(array(
                                                                                'src'=>$child_info[2],
                                                                                'style'=>'float:left;padding-right:5px;'
                                                                                ));
                                                                        $color_css = '';
                                                                }
                                                                echo '<li style="padding-left:10px;"><a href="#" id="cat_'. $child .'"><span '.$color_css.'>'.$child_image.'</span><span class="category-title">'.$child_title.'</span></a></li>';
                                                        }
                                                        echo '</ul>';
                                                    }
    						echo '</div></li>';
    					}
    				?>
    			</ul>

			  </div>
			  <!-- / report category filters -->
			  
  			<!-- report type filters -->
  			<div id="report-type-filter" class="filters">
  				<h3><?php echo Kohana::lang('ui_main.type'); ?></h3>
  					<ul>
  						<li><a id="media_0" class="active" href="#"><span><?php echo Kohana::lang('ui_main.reports'); ?></span></a></li>
  						<li><a id="media_4" href="#"><span><?php echo Kohana::lang('ui_main.news'); ?></span></a></li>
  						<li><a id="media_1" href="#"><span><?php echo Kohana::lang('ui_main.pictures'); ?></span></a></li>
  						<li><a id="media_2" href="#"><span><?php echo Kohana::lang('ui_main.video'); ?></span></a></li>
  						<li><a id="media_0" href="#"><span><?php echo Kohana::lang('ui_main.all'); ?></span></a></li>
  					</ul>
  					<div class="floatbox">
      					<?php
      					// Action::main_filters - Add items to the main_filters
      					Event::run('ushahidi_action.map_main_filters');
      					?>
      				</div>
      				<!-- / report type filters -->
  			</div>
      			
			</div>
			<!-- / filters box -->
			
			<?php
			if ($layers)
			{
				?>
				<div id="layers-box">
				  <a class="btn toggle" id="layers-menu-toggle" class="" href="#kml_switch"><?php echo Kohana::lang('ui_main.layers');?> <span class="btn-icon ic-right">&raquo;</span></a>
  				<!-- Layers (KML/KMZ) -->
  				<ul id="kml_switch" class="category-filters map-menu-box">
  					<?php
  					foreach ($layers as $layer => $layer_info)
  					{
  						$layer_name = $layer_info[0];
  						$layer_color = $layer_info[1];
  						$layer_url = $layer_info[2];
  						$layer_file = $layer_info[3];
  						$layer_link = (!$layer_url) ?
  							url::base().Kohana::config('upload.relative_directory').'/'.$layer_file :
  							$layer_url;
  						echo '<li><a href="#" id="layer_'. $layer .'"
  						onclick="switchLayer(\''.$layer.'\',\''.$layer_link.'\',\''.$layer_color.'\'); return false;"><span class="swatch" style="background-color:#'.$layer_color.'"></span>
  						<span class="category-title">'.$layer_name.'</span></a></li>';
  					}
  					?>
  				</ul>
				</div>
				<!-- /Layers -->
				<?php
			}
			?>
			
			<!-- additional content -->
			<?php
			if (Kohana::config('settings.allow_reports'))
			{
				?>
				<a class="btn toggle" id="how-to-report-menu-toggle" class="" href="#how-to-report-box"><?php echo Kohana::lang('ui_main.how_to_report'); ?> <span class="btn-icon ic-question">&raquo;</span></a>
				<div id="how-to-report-box" class="map-menu-box">
					<ol>
						<?php if (!empty($phone_array)) 
						{ ?><li><?php echo Kohana::lang('ui_main.report_option_1')." "; ?> <?php foreach ($phone_array as $phone) {
							echo "<strong>". $phone ."</strong>";
							if ($phone != end($phone_array)) {
								echo " or ";
							}
						} ?></li><?php } ?>
						<?php if (!empty($report_email)) 
						{ ?><li><?php echo Kohana::lang('ui_main.report_option_2')." "; ?> <a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a></li><?php } ?>
						<?php if (!empty($twitter_hashtag_array)) 
									{ ?><li><?php echo Kohana::lang('ui_main.report_option_3')." "; ?> <?php foreach ($twitter_hashtag_array as $twitter_hashtag) {
						echo "<strong>". $twitter_hashtag ."</strong>";
						if ($twitter_hashtag != end($twitter_hashtag_array)) {
							echo " or ";
						}
						} ?></li><?php
						} ?><li><a href="<?php echo url::site() . 'reports/submit/'; ?>"><?php echo Kohana::lang('ui_main.report_option_4'); ?></a></li>
					</ol>

				</div>
			<?php } ?>
			<!-- / additional content -->
			
			<?php
			// Action::main_sidebar - Add Items to the Entry Page Sidebar
			Event::run('ushahidi_action.main_sidebar');
			?>
	
		</div>
		<!-- / right column -->
	
		<!-- content column -->
		<div id="content" class="clearingfix">
		  

				<?php								
				// Map and Timeline Blocks
				echo $div_map;
				echo $div_timeline;
				?>
			</div>
		</div>
		<!-- / content column -->

	</div>
</div>
<!-- / main body -->

<!-- content -->
<div class="content-container">

	<!-- content blocks -->
			<?php blocks::render(); ?>
	<!-- /content blocks -->

</div>
<!-- content -->
