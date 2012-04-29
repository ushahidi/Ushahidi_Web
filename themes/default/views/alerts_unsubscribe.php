<div id="content">
	<div class="content-bg">
		<!-- start block -->
		<div class="big-block">
			<h1><?php echo Kohana::lang('ui_main.alerts_get') ?></h1>
				<?php
					if ($unsubscribed)
					{
						echo '<div class="green-box">';
						echo '<div class="alert_response" align="center">';
						$settings = kohana::config('settings');
						echo Kohana::lang('alerts.unsubscribed')
								.$settings['site_name']; 
						echo '</div>';
						echo '</div>';
					}
					else
					{
						echo '<div class="red-box">';
						echo '<div class="alert_response" align="center">';
						echo Kohana::lang('alerts.unsubscribe_failed');
						echo '</div>';
						echo '</div>';
					}
				?>
		</div>
		<!-- end block -->
	</div>
</div>
