<div class="bg">
	<h2>
		<?php admin::reports_subtabs("analysis"); ?>
	</h2>
	
	<div class="report-form">		
		<div class="head">
			<h3><?php echo Kohana::lang('analysis.analysis') ?></h3>
		</div>
		<!-- column -->
		
		<!-- column -->		
		<div class="settings_holder">
		
			<?php print form::open(url::base().'admin/reports/edit', array('id'=>'analysis-form', 'method'=>'get')); ?>
			<div class="analysis-report">
				<form>
					<div class="analysis-about">
						<h4><?php echo Kohana::lang('analysis.how_analysis_works'); ?></h4>
						<ul>
							<li><?php echo Kohana::lang('analysis.how_analysis_works_1'); ?></li>
							<li><?php echo Kohana::lang('analysis.how_analysis_works_2'); ?></li>
							<li><?php echo Kohana::lang('analysis.how_analysis_works_3'); ?></li>
						</ul>
						<a href="javascript:hideAbout();"><?php echo Kohana::lang('ui_main.hide'); ?></a>
					</div>
					
					<a name="results"></a>
					<div class="analysis-results"><?php echo Kohana::lang('ui_main.search_results'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:checkAll('a_id[]')"><?php echo Kohana::lang('analysis.select_all'); ?></a>&nbsp;|&nbsp;<a href="javascript:checkNone('a_id[]')"><?php echo Kohana::lang('analysis.select_none'); ?></a></div>
					<div class="analysis-generated" id="analysis-generated">
						<h4><?php echo utf8::strtoupper(Kohana::lang('analysis.search_to_find_related')); ?></h4>
					</div>
					<div id="analysis-assess"></div>
					<div id="analysis-report"></div>
					
				</form>
			</div>
			<?php print form::close(); ?>
			
			<?php print form::open(NULL, array('id'=>'analysis-filter')); ?>
			<input type="hidden" name="latitude" id="latitude" value="<?php echo $latitude; ?>">
			<input type="hidden" name="longitude" id="longitude" value="<?php echo $longitude; ?>">
			<div class="analysis-filter">
				<div class="tab">
					<ul>
						<li style="line-height: 24px;"><?php echo utf8::strtoupper(Kohana::lang('analysis.search_for_related')); ?></li>
						<li><a href="javascript:generateReports();" class="analysis-btn-search"><?php echo utf8::strtoupper(Kohana::lang('ui_main.search')); ?></a></li>
						<li><a href="javascript:reset()" class="analysis-btn-reset"><?php echo utf8::strtoupper(Kohana::lang('ui_main.reset')); ?></a></li>
					</ul>
				</div>
				<div class="analysis-filter-items">
									
					<h4><?php echo utf8::strtoupper(Kohana::lang('analysis.distance_filter')); ?> <span><?php echo Kohana::lang('analysis.drag_map_to_resize'); ?></span></h4>
					<div class="analysis-filter-block">
						<div class="analysis-map" id="analysis-map"></div>
						<div class="analysis-slider">
							<select name="analysis_radius" id="analysis_radius">
								<option value="1">1 KM</option>
								<option value="5">5 KM</option>
								<option value="10">10 KM</option>
								<option value="20" selected="selected">20 KM</option>
								<option value="50">50 KM</option>
								<option value="100">100 KM</option>
							</select>
						</div>
					</div>
					
					<h4><?php echo utf8::strtoupper(Kohana::lang('analysis.time_filter')); ?></h4>
					<div class="analysis-filter-block">
						<div class="analysis-date">
							<strong><?php echo Kohana::lang('analysis.start_date'); ?>:</strong><br />
							<?php print form::input('start_date', '', ' class="text"'); ?>
						</div>
						<div class="analysis-date">
							<strong><?php echo Kohana::lang('analysis.end_date'); ?>:</strong><br />
							<?php print form::input('end_date', '', ' class="text"'); ?>
						</div>
						<div style="clear:both;"></div>
						<?php print $date_picker_js; ?>
					</div>
					
					<h4><?php echo utf8::strtoupper(Kohana::lang('analysis.category_filter')); ?></h4>
					<div class="analysis-filter-block">
						<select id="analysis_category" name="analysis_category"  style="width:300px;">
							<option value="0"><?php echo utf8::strtoupper(Kohana::lang('ui_main.all')); ?></option>
							<?php
							foreach ($categories as $category => $category_info)
							{
								echo "<option value=\"".$category."\">".$category_info[0]."</option>";
								foreach ($category_info[1] as $child => $child_info)
								{
									echo "<option value=\"".$child."\">&nbsp;&nbsp;|--&nbsp;".$child_info[0]."</option>";
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="tab">
					<ul>
						<li style="line-height: 24px;"><?php echo utf8::strtoupper(Kohana::lang('analysis.search_for_related')); ?></li>
						<li><a href="javascript:generateReports();" class="analysis-btn-search"><?php echo utf8::strtoupper(Kohana::lang('ui_main.search')); ?></a></li>
						<li><a href="javascript:reset()" class="analysis-btn-reset"><?php echo utf8::strtoupper(Kohana::lang('ui_main.reset')); ?></a></li>
					</ul>
				</div>
			</div>
			<?php print form::close(); ?>

		</div>

		<div class="simple_border"></div>		
	</div>
	
</div>
