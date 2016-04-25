<!-- main body -->
</script>
		       	
<div id="main" class="clearingfix">
	<div id="mainmiddle" class="floatbox">

		<!-- content column -->
		<div id="content" class="clearingfix">
			<div class="floatbox">

				<!-- filters -->
				<a class="btn toggle responsive menu" id="mobile-category" href="#the-filters-embeded"><?php echo Kohana::lang('ui_main.category');?></a>
				<div id="the-filters-embeded">
                                <div class="filters clearingfix" id="embed-filter-map">
					<div id="report-category-filter"> 
						<h3 style="text-transform: capitalize" id="text-title"><?php echo Kohana::lang('ui_main.category');?></h3>

						<ul id="category_switch" class="category-filters">
						<?php
						$color_css = 'class="category-icon swatch" style="background-color:#'.$default_map_all.'"';
						$all_cat_image = '';
						if ($default_map_all_icon != NULL)
						{
							$all_cat_image = html::image(array(
								'src'=>$default_map_all_icon
							));
							$color_css = 'class="category-icon"';
						}
						?>
						<li>
							<a class="active" id="cat_0" href="#">
								<span <?php echo $color_css; ?>><?php echo $all_cat_image; ?></span>
								<span class="category-title"><?php echo Kohana::lang('ui_main.all_categories');?></span>
							</a>
						</li>
						<?php
							foreach ($categories as $category => $category_info)
							{
								$category_title = html::escape($category_info[0]);
								$category_color = $category_info[1];
								$category_image = ($category_info[2] != NULL)
								    ? url::convert_uploaded_to_abs($category_info[2])
								    : NULL;
								$category_description = html::escape(Category_Lang_Model::category_description($category));
								$color_css = 'class="category-icon swatch" style="background-color:#'.$category_color.'"';
								if ($category_info[2] != NULL)
								{
									$category_image = html::image(array(
										'src'=>$category_image,
										));
									$color_css = 'class="category-icon"';
								}
								echo '<li>'
								    . '<a href="#" id="cat_'. $category .'" title="'.$category_description.'">'
								    . '<span '.$color_css.'>'.$category_image.'</span>'
								    . '<span class="category-title">'.$category_title.'</span>'
								    . '</a>';
								// Get Children
								echo '<div class="hide" id="child_'. $category .'">';
								if (sizeof($category_info[3]) != 0)
								{
									echo '<ul>';
									foreach ($category_info[3] as $child => $child_info)
									{
										$child_title = html::escape($child_info[0]);
										$child_color = $child_info[1];
										$child_image = ($child_info[2] != NULL)
										    ? url::convert_uploaded_to_abs($child_info[2])
										    : NULL;
										$child_description = html::escape(Category_Lang_Model::category_description($child));
										$color_css = 'class="category-icon swatch" style="background-color:#'.$child_color.'"';
										if ($child_info[2] != NULL)
										{
											$child_image = html::image(array(
												'src' => $child_image
											));
											$color_css = 'class="category-icon"';
										}
										echo '<li>'
										    . '<a href="#" id="cat_'. $child .'" title="'.$child_description.'">'
										    . '<span '.$color_css.'>'.$child_image.'</span>'
										    . '<span class="category-title">'.$child_title.'</span>'
										    . '</a>'
										    . '</li>';
									}
									echo '</ul>';
								}
								echo '</div></li>';
							}
						?>
						</ul>
						<!-- / category filters -->

					</div>


					<?php
					// Action::main_filters - Add items to the main_filters
					Event::run('ushahidi_action.map_main_filters');
					?>
				</div>
</div>
				<!-- / filters -->

				<?php
				// Map and Timeline Blocks
				echo $div_map;
				// echo $div_timeline;
				?>
			</div>
		</div>
		<!-- / content column -->

	</div>
</div>

<script type="text/javascript">
	$("#mobile-category").click(
		function(e){
			var $e = $(e.currentTarget);
			if ($e.hasClass("active-toggle")) {
				$($(this).attr("href")).hide();
				$(this).removeClass("active-toggle");
			}
			else {
				$($(this).attr("href")).show();
				$(this).addClass("active-toggle");
			}
		return false;}
		);
			</script>

