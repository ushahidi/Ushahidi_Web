<div id="content">
	<div class="content-bg">
		<!-- start block -->
		<div class="big-block">
			<h1><?php echo Kohana::lang('ui_main.alerts_get'); ?></h1>
			<?php if ($form_error): ?>
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
			<?php endif; ?>
			<div class="step-1">
				<h2><?php echo Kohana::lang('ui_main.alerts_step1_select_city'); ?></h2>
				<div class="location">
					<form action="">
						<label><?php echo Kohana::lang('ui_main.alerts_alert_me'); ?></label>
						<?php print form::dropdown('alert_city',$cities,''); ?>
					</form>
				</div>
				<div class="map">
					<p><?php echo Kohana::lang('ui_main.alerts_place_spot'); ?></p>
					<div class="map-holder" id="divMap"></div>
				</div>
			</div>
			<?php print form::open() ?>
			<input type="hidden" id="alert_lat" name="alert_lat" value="<?php echo $form['alert_lat']; ?>">
			<input type="hidden" id="alert_lon" name="alert_lon" value="<?php echo $form['alert_lon']; ?>">
			<div class="step-2-holder">
				<div class="step-2">
					<h2><?php echo Kohana::lang('ui_main.alerts_step2_send_alerts'); ?></h2>
					<div class="holder">
						<div class="box">
							<label>
								<?php $checked = ($form['alert_mobile_yes'] == 1) ?>
								<?php print form::checkbox('alert_mobile_yes', '1', $checked); ?>
								<span>
									<strong><?php echo Kohana::lang('ui_main.alerts_mobile_phone'); ?></strong><br />
									<?php echo Kohana::lang('ui_main.alerts_enter_mobile'); ?>
								</span>
							</label>
							<span><?php print form::input('alert_mobile', $form['alert_mobile']); ?></span>
						</div>
						<div class="box">
							<label>
								<?php $checked = ($form['alert_email_yes'] == 1) ?>
								<?php print form::checkbox('alert_email_yes', '1', $checked); ?>
								<span>
									<strong><?php echo Kohana::lang('ui_main.alerts_email'); ?></strong><br />
									<?php echo Kohana::lang('ui_main.alerts_enter_email'); ?>
								</span>
							</label>
							<span><?php print form::input('alert_email', $form['alert_email']); ?></span>
						</div>
						<?php if ($allow_feed == 1 ): ?>
						<div class="box">
							<label>
								<input type="checkbox" checked="checked" readonly="readonly" />
								<span><?php echo Kohana::lang('ui_main.alerts_rss'); ?></span>
							</label>
							<span><input type="text" value="<?php echo url::site()?>" readonly="readonly" /></span>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<input id="btn-send-alerts" type="submit" value="<?php echo Kohana::lang('ui_main.alerts_btn_send'); ?>" />
			</div>
			<?php print form::close(); ?>
		</div>
		<!-- end block -->
	</div>
</div>