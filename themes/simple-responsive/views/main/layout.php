<script type="text/javascript">
$(function(){

	// show/hide report filters and layers boxes on home page map
	$("a.toggle").click(
		function(e) {
			var $e = $(e.currentTarget);
			e.preventDefault();

			if ($e.hasClass("active-toggle")) {
				$($(this).attr("href")).hide();
				$(this).removeClass("active-toggle");
			} else {
				$($(this).attr("href")).show();
				$(this).addClass("active-toggle");
			}
			return false;
		}
	);

	var $blockContent = $('.content-block-column'),
		$blocks = $blockContent.find('li'),
		count = 12 / $blocks.length;

	if (count < 3) count = 3;
	$blocks.addClass("col-sm-"+count+" col-xs-12");
});

</script>
<!-- main body -->
<div id="main" class="clearingfix container">

	<div id="mainmiddle" class="row">

		<!-- menu para mobile -->
		<div class="main-responsive-mobile">
			<!-- right column -->
			<div>

				<a class="btn toggle responsive-menu" id="filter-menu-toggle" class="" href="#the-filters">
					<?php echo Kohana::lang('ui_main.filter_reports_by'); ?><span class="btn-icon ic-right">&raquo;</span>
				</a>

				<!-- filters box -->
				<div id="the-filters" class="map-menu-box">

					<?php
					// Action::main_sidebar_pre_filters - Add Items to the Entry Page before filters
					Event::run('ushahidi_action.main_sidebar_pre_filters');
					?>

					<!-- report category filters -->
					<div id="report-category-filter">
						<h3><?php echo Kohana::lang('ui_main.category');?></h3>

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

					<?php
					// Action::main_sidebar_post_filters - Add Items to the Entry Page after filters
					Event::run('ushahidi_action.main_sidebar_post_filters');
					?>

				</div>
				<!-- / filters box -->

				<?php
				if ($layers)
				{
					?>
					<div id="layers-box">
						<a class="btn toggle responsive-menu" id="layers-menu-toggle" class="" href="#kml_switch"><?php echo Kohana::lang('ui_main.layers');?> <span class="btn-icon ic-right">&raquo;</span></a>
						<!-- Layers (KML/KMZ) -->
						<ul id="kml_switch" class="layers-filters category-filters map-menu-box">
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
								echo '<li><a href="#" id="layer_'. $layer .'">
								<span class="swatch" style="background-color:#'.$layer_color.'"></span>
								<span class="layer-name">'.$layer_name.'</span></a></li>';
							}
							?>
						</ul>
					</div>
					<!-- /Layers -->
					<?php
				}
				?>
			</div>
			<!-- / right column -->
		</div>


		<div class="divmap">
			<?php
			// Map and Timeline Blocks
			echo $div_map;
			echo $div_timeline;
			?>
		</div>

		<!-- menu para desktops -->

		<div class="main-responsive-desktop">
			<h3><?php echo Kohana::lang('ui_main.category'); ?></h3>
			<?php
			// Action::main_sidebar_pre_filters - Add Items to the Entry Page before filters
			Event::run('ushahidi_action.main_sidebar_pre_filters');
			?>

			<!-- report category filters -->
			<div >

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

			<!-- layers -->
			<?php
			if ($layers)
			{
				?>
				<div id="layers-box">
					<?php echo Kohana::lang('ui_main.layers');?>
					<!-- Layers (KML/KMZ) -->
					<ul id="kml_switch" class="layers-filters category-filters">
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
							echo '<li><a href="#" id="layer_'. $layer .'">
							<span class="swatch" style="background-color:#'.$layer_color.'"></span>
							<span class="layer-name">'.$layer_name.'</span></a></li>';
						}
						?>
					</ul>
				</div>
				<!-- /Layers -->
				<?php
			}
			?>

		</div>

		</div>
		<!-- / content column -->

		<div class="row how-report">
		<!-- additional content -->
		<?php
		if (Kohana::config('settings.allow_reports'))
		{
			?>
			<div class="col-xs-12 text-center">
				<h2>
					<?php echo Kohana::lang('ui_main.how_to_report'); ?>
				</h2>
			</div>
				<!-- / additional content -->
				<div class="how-to-report-methods">

					<!-- Phone -->
					<?php if (!empty($phone_array)) { ?>
					<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						<strong><?php echo Kohana::lang('ui_main.report_option_1'); ?></strong><br/>
						<?php foreach ($phone_array as $phone) { ?>
							<!-- <?php echo $phone; ?><br/> -->
							<img src="<?php print URL::base() ?>themes/simple-responsive/images/home/sms.png">
						<?php } ?>
					</div>
					<?php } ?>

					<!-- External Apps -->
					<?php if (count($external_apps) > 0) { ?>
						<?php foreach ($external_apps as $app) { ?>
						<div class="col-xs-12 col-sm-6 col-md-3 text-center">
							<strong><?php echo Kohana::lang('ui_main.report_option_external_apps'); ?></strong><br/>
								<a href="<?php echo $app->url; ?>"><img src="<?php print URL::base() ?>themes/simple-responsive/images/home/<?php echo strtolower($app->name); ?>.png"></a>
						</div>
						<?php } ?>
					<?php } ?>

					<!-- Email -->
					<?php if (!empty($report_email)) { ?>
					<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						<strong><?php echo Kohana::lang('ui_main.report_option_2'); ?></strong><br/>
						<a href="mailto:<?php echo $report_email?>"><img src="<?php print URL::base() ?>themes/simple-responsive/images/home/email.png"></a>
					</div>
					<?php } ?>

					<!-- Twitter -->
					<?php if (!empty($twitter_hashtag_array)) { ?>
					<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						<strong><?php echo Kohana::lang('ui_main.report_option_3'); ?></strong><br/>
						<img src="<?php print URL::base() ?>themes/simple-responsive/images/home/twitter.png">
						<br />
						<?php foreach ($twitter_hashtag_array as $twitter_hashtag) { ?>
							<span>#<?php echo $twitter_hashtag; ?></span>
							<?php if ($twitter_hashtag != end($twitter_hashtag_array)) { ?>
								<br />
							<?php } ?>
						<?php } ?>
					</div>
					<?php } ?>

					<!-- Web Form -->
					<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						<strong><?php echo Kohana::lang('ui_main.report_option_4'); ?></strong><br/>
						<a href="<?php echo url::site() . 'reports/submit/'; ?>"><img src="<?php print URL::base() ?>themes/simple-responsive/images/home/webform.png"></a>
					</div>

				</div>


		<?php } ?>
		<!-- additional content -->
		</div>

	</div>
<!-- / main body -->


<!-- content -->
<div class="content-container">

	<!-- content blocks -->
	<div class="content-blocks clearingfix container">
		<ul class="content-block-column row">
			<?php blocks::render(); ?>
		</ul>
	</div>
	<!-- /content blocks -->

</div>
<!-- content -->
