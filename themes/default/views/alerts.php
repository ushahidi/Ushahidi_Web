<div id="content">
	<div class="content-bg">
		<!-- start block -->
		<div class="big-block">
			<h1><?php echo Kohana::lang('ui_main.alerts_get'); ?></h1>
			<?php
			if ($form_error) {
			?>
			<!-- red-box -->
			<div class="red-box">
				<h3>Error!</h3>
				<ul>
					<?php
					foreach ($errors as $error_item => $error_description)
					{
					// print "<li>" . $error_description . "</li>";
					print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
					}
					?>
				</ul>
			</div>
			<?php
			}
			?>
			<?php print form::open() ?>
			<div class="step-1">
				<h2><?php echo Kohana::lang('ui_main.alerts_step1_select_city'); ?></h2>
				<div class="map">
					<p><?php echo Kohana::lang('ui_main.alerts_place_spot'); ?></p>
					<div class="map-holder" id="divMap"></div>
				</div>
				<div class="report-find-location">
					<div class="alert_slider">
						<select name="alert_radius" id="alert_radius">
							<option value="1">1 KM</option>
							<option value="5">5 KM</option>
							<option value="10">10 KM</option>
							<option value="20" selected="selected">20 KM</option>
							<option value="50">50 KM</option>
							<option value="100">100 KM</option>
						</select>
					</div>
					<?php print form::input('location_find', '', ' title="City, State and/or Country" class="findtext"'); ?>
					<div style="float:left;margin:9px 0 0 5px;"><input type="button" name="button" id="button" value="<?php echo Kohana::lang('ui_main.find_location'); ?>" class="btn_find" /></div>
					<div id="find_loading" class="report-find-loading"></div>
					<div style="clear:both;" id="find_text">* <?php echo Kohana::lang('ui_main.alerts_place_spot2'); ?></div>
				</div>
			</div>
			<input type="hidden" id="alert_lat" name="alert_lat" value="<?php echo $form['alert_lat']; ?>">
			<input type="hidden" id="alert_lon" name="alert_lon" value="<?php echo $form['alert_lon']; ?>">
			<div class="step-2-holder">
				<div class="step-2">
					<h2><?php echo Kohana::lang('ui_main.alerts_step2_send_alerts'); ?></h2>
					<div class="holder">
						<?php if($show_mobile == TRUE) { ?>
						<div class="box">
							<label>
								<?php
									if ($form['alert_mobile_yes'] == 1) {
										$checked = true;
									}
									else
									{
									 	$checked = false;
									}
									print form::checkbox('alert_mobile_yes', '1', $checked);
								?>
								<span><strong><?php echo Kohana::lang('ui_main.alerts_mobile_phone'); ?></strong><br /><?php echo Kohana::lang('ui_main.alerts_enter_mobile'); ?></span>
							</label>
							<span><?php print form::input('alert_mobile', $form['alert_mobile'], ' class="text long"'); ?></span>
						</div>
						<?php } ?>
						<div class="box">
							<label>
								<?php
									if ($form['alert_email_yes'] == 1) {
										$checked = true;
									}
									else
									{
										$checked = false;
									}
									print form::checkbox('alert_email_yes', '1', $checked);
								?>
								<span><strong><?php echo Kohana::lang('ui_main.alerts_email'); ?></strong><br /><?php echo Kohana::lang('ui_main.alerts_enter_email'); ?></span>
							</label>
							<span><?php print form::input('alert_email', $form['alert_email'], ' class="text long"'); ?></span>
						</div>
					</div>
				</div>
				<div class="step-3">
					<h2><?php echo Kohana::lang('ui_main.alerts_step3_select_catgories'); ?></h2>
					<div class="holder">
						<div class="box">
					                <div class="report_category" id="categories">
						                <?php
						                        $selected_categories = array();
                                                                        if (!empty($form['alert_category']) && is_array($form['alert_category'])) {
							                  $selected_categories = $form['alert_category'];
						                        }
						                        echo category::tree($categories, $selected_categories, 'alert_category', 2, true);
						                ?>
					                </div>
					        </div>
				        </div>
				</div>
				<input id="btn-send-alerts" class="btn_submit" type="submit" value="<?php echo Kohana::lang('ui_main.alerts_btn_send'); ?>" />
				<BR /><BR />
				<a href="<?php echo url::site()."alerts/confirm";?>"><?php echo Kohana::lang('ui_main.alert_confirm_previous'); ?></a>
			</div>
			<?php print form::close(); ?>
										<?php
				if ($allow_feed == 1 )
				{
			?>
			<div class="step-2-holder">
				<div class="feed">
					<h2><?php echo Kohana::lang('ui_main.alerts_rss'); ?></h2>
					<div class="holder">
						<div class="box" style="text-align:center;">
							<a href="<?php echo url::site(); ?>feed/"><img src="<?php echo url::base(); ?>media/img/icon-feed.png" style="vertical-align: middle;" border="0"></a>&nbsp;<strong><a href="<?php echo url::site(); ?>feed/"><?php echo url::site(); ?>feed/</a></strong>
						</div>
					</div>
				</div>
			</div>
			<?php
				}
			?>
		</div>
		<!-- end block -->
	</div>
</div>