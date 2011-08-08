<!-- main body -->
<div id="main" class="clearingfix">
	<div id="mainmiddle" class="floatbox withright">

		<!-- right column -->
		<div id="right" class="clearingfix">

			<?php
			// Action::main_sidebar - Add Items to the Entry Page Sidebar
			Event::run('ushahidi_action.main_sidebar');
			?>
			
			
			<div id="report_main_details">
				
				
				<!-- additional content -->
				<?php
				if (Kohana::config('settings.allow_reports'))
				{
					?>
					<div class="additional-content">
						<h5><?php echo Kohana::lang('ui_main.how_to_report'); ?></h5>
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
				
				
			</div>
	
		</div>
		<!-- / right column -->
		
		<!-- content column -->
		<div id="content" class="clearingfix">
			<div class="floatbox">
			
				<script type="text/javascript">
					function toggleLayerSlide(link, layer){
						if ($("#"+link).text() == "<?php echo Kohana::lang('ui_main.show'); ?>")
						{
							$('#'+layer).fadeIn(500);
							$("#"+link).text("<?php echo Kohana::lang('ui_main.hide'); ?>");
						}
						else
						{
							$('#'+layer).fadeOut(500);
							$("#"+link).text("<?php echo Kohana::lang('ui_main.show'); ?>");
						}
					}	
				</script>
			
			
				<!-- category filters -->
				<div class="cat-filters clearingfix shadow">
					<strong><?php echo Kohana::lang('ui_main.category_filter');?> <span>[<a href="javascript:toggleLayerSlide('category_switch_link', 'category_switch')" id="category_switch_link"><?php echo Kohana::lang('ui_main.show'); ?></a>]</span></strong>
				</div>

			
				<ul id="category_switch" class="category-filters shadow">
					<li><a class="active" id="cat_0" href="#"><span class="swatch" style="background-color:<?php echo "#".$default_map_all;?>"></span><span class="category-title"><?php echo Kohana::lang('ui_main.all_categories');?></span></a></li>
					<?php
						foreach ($categories as $category => $category_info)
						{
							$category_title = $category_info[0];
							$category_color = $category_info[1];
							$category_image = '';
							$color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
							if($category_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$category_info[2])) {
								$category_image = html::image(array(
									'src'=>Kohana::config('upload.relative_directory').'/'.$category_info[2],
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
	                                                            if($child_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$child_info[2])) {
	                                                                    $child_image = html::image(array(
	                                                                            'src'=>Kohana::config('upload.relative_directory').'/'.$child_info[2],
	                                                                            'style'=>'float:left;padding-right:5px;'
	                                                                            ));
	                                                                    $color_css = '';
	                                                            }
	                                                            echo '<li style="padding-left:20px;"><a href="#" id="cat_'. $child .'"><span '.$color_css.'>'.$child_image.'</span><span class="category-title">'.$child_title.'</span></a></li>';
	                                                    }
	                                                    echo '</ul>';
	                                                }
							echo '</div></li>';
						}
					?>
				</ul>
				<!-- / category filters -->
				
				<?php
				if ($layers)
				{
					?>
					<!-- Layers (KML/KMZ) -->
					<div class="cat-filters clearingfix" style="margin-top:20px;">
						<strong><?php echo Kohana::lang('ui_main.layers_filter');?> <span>[<a href="javascript:toggleLayer('kml_switch_link', 'kml_switch')" id="kml_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
					</div>
					<ul id="kml_switch" class="category-filters">
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
							onclick="switchLayer(\''.$layer.'\',\''.$layer_link.'\',\''.$layer_color.'\'); return false;"><div class="swatch" style="background-color:#'.$layer_color.'"></div>
							<div>'.$layer_name.'</div></a></li>';
						}
						?>
					</ul>
					<!-- /Layers -->
					<?php
				}
				?>
				
				
				<?php
				if ($shares)
				{
					?>
					<!-- Layers (Other Ushahidi Layers) -->
					<div class="cat-filters clearingfix" style="margin-top:20px;">
						<strong><?php echo Kohana::lang('ui_main.other_ushahidi_instances');?> <span>[<a href="javascript:toggleLayer('sharing_switch_link', 'sharing_switch')" id="sharing_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
					</div>
					<ul id="sharing_switch" class="category-filters">
						<?php
						foreach ($shares as $share => $share_info)
						{
							$sharing_name = $share_info[0];
							$sharing_color = $share_info[1];
							echo '<li><a href="#" id="share_'. $share .'"><div class="swatch" style="background-color:#'.$sharing_color.'"></div>
							<div>'.$sharing_name.'</div></a></li>';
						}
						?>
					</ul>
					<!-- /Layers -->
					<?php
				}
				?>

			
				
			
				
				<script type="text/javascript">
				// Overwrite click event on report points
				onFeatureSelect = function(event){
					
					selectedFeature = event;
					// Since KML is user-generated, do naive protection against
					// Javascript.
					
					zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
					lon = zoom_point.lon;
					lat = zoom_point.lat;
					
					var content = "<div class=\"infowindow\"><div class=\"infowindow_list\">"+event.feature.attributes.name+"<div style=\"clear:both;\"></div></div>";
					content = content+"\n<div class=\"infowindow_meta\"><a href='javascript:zoomToSelectedFeature("+lon+","+lat+", 1)'>Zoom&nbsp;In</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:zoomToSelectedFeature("+lon+","+lat+", -1)'>Zoom&nbsp;Out</a></div>";
					content = content+"</div>";
					
					holder = $(event.feature.attributes.name);
					holder.html(content);
					href = holder.find('a:first').attr('href');
					href = href.split('/');
					reportid = href[href.length-1];
					
					if (reportid == parseInt(reportid)) {
						showOneReport(reportid);
					}else{
						querystring = reportid.substr(1);
						showListReports(querystring);
					}
					
				

					
					if (content.search("<script") != -1)
					{
					    content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/</g, "&lt;");
					}
				};
				
				onFeatureUnselect = function(event){
					return true;
				}
				
				function showOneReport(reportid,querystring){
				
					$('#report_main_details').fadeOut(500, function() {
						$('#report_main_details').text('');
						
						jsonurl = '<?php echo url::site()."api/?task=incidents&by=incidentid&id="; ?>'+reportid;
						
						$.getJSON(jsonurl, function(data) {
							$.each(data.payload.incidents, function(i,item){
								$('#report_main_details').append("<div><h2>"+item.incident.incidenttitle+"</h2></div>");
								
								if(item.incident.incidentverified == 1){
									$('#report_main_details').append('<span class="r_verified" style="background-color:green;color:#FFFFFF"><?php echo Kohana::lang('ui_main.verified'); ?></p>');
								}else{
									$('#report_main_details').append('<span class="r_unverified" style="background-color:red;color:#FFFFFF"><?php echo Kohana::lang('ui_main.unverified'); ?></p>');
								}
								
								$('#report_main_details').append("<div>"+item.incident.incidentdate+"</div>");
								
								$('#report_main_details').append("<div>"+item.incident.locationname+"</div>");
								
								$('#report_main_details').append("<div>"+item.incident.incidentdescription+"</div>");
								
								$('#report_main_details').append("<div><a href=\"<?php echo url::site(); ?>reports/view/"+item.incident.incidentid+"\"><?php echo Kohana::lang('ui_main.more_information');?></a></div>");
								
								if(querystring != null){
									$('#report_main_details').append("<div><a href=\"#\" onClick=\"javascript:showListReports('"+querystring+"')\"><?php echo Kohana::lang('ui_admin.back'); ?></a></div>");
								}
								
							});
						});
					});
					$('#report_main_details').fadeIn(500);
				}
				
				function showListReports(querystring){
					
					$('#report_main_details').fadeOut(500, function() {
						$('#report_main_details').text('');
					
						jsonurl = '<?php echo url::site()."api/?task=incidents&by=bounds&limit=1000&"; ?>'+querystring;
						
						$.getJSON(jsonurl, function(data) {
							$.each(data.payload.incidents, function(i,item){
								$('#report_main_details').append("<div><a href=\"#\" onClick=\"javascript:showOneReport('"+item.incident.incidentid+"','"+querystring+"')\">"+item.incident.incidenttitle+"</a></div>");
							});
						});
					});
					
					$('#report_main_details').fadeIn(500);
					
				}
				
				</script>
				<?php								
				// Map and Timeline Blocks
				
				echo $div_map;
				?>
				
				<div style="display:none;">
				<?php
				echo $div_timeline;
				?>
				</div>
				
								
			</div>
		</div>
		<!-- / content column -->

	</div>
</div>
<!-- / main body -->
