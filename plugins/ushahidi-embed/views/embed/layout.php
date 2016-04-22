<!-- main body -->
<div id="main" class="clearingfix">
	<div id="mainmiddle" class="floatbox">

		<!-- content column -->
		<div id="content" class="clearingfix">
			<div class="floatbox">

				<!-- filters -->
				<div class="filters clearingfix">
					<div class="media-filters">
						<strong><?php echo Kohana::lang('ui_main.filters'); ?></strong>
						<ul>
							<li><a id="media_0" class="active" href="#"><span><?php echo Kohana::lang('ui_main.all'); ?></span></a></li>
							<li><a id="media_4" href="#"><span><?php echo Kohana::lang('ui_main.news'); ?></span></a></li>
							<li><a id="media_1" href="#"><span><?php echo Kohana::lang('ui_main.pictures'); ?></span></a></li>
							<li><a id="media_2" href="#"><span><?php echo Kohana::lang('ui_main.video'); ?></span></a></li>
						</ul>
					</div>


					<?php
					// Action::main_filters - Add items to the main_filters
					Event::run('ushahidi_action.map_main_filters');
					?>
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
<!-- / main body -->

