<h2><?php print Kohana::lang('upgrade.upgrade_db_title'); ?></h2>
<div class="table-holder">
	<?php if ($form_error): ?>
		<!-- red-box -->
		<div class="red-box">
			<h3><?php echo Kohana::lang('ui_main.error');?></h3>
			<ul>
				<?php
					foreach ($errors as $error_item => $error_description)
					{
						print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
					}
				?>
			</ul>
		</div>
	<?php elseif ($form_saved): ?>
		<!-- green-box -->
		<div class="green-box" id="submitStatus" style="margin: 0;">
			<h3><?php echo $message; ?></h3>
			<div class="btns"><ul><li><a href="<?php echo url::site('admin'); ?>"><?php echo Kohana::lang('upgrade.upgrade_continue_btn_text');?></a></li></ul></div>
		</div>
		
	<?php elseif ( Kohana::config('version.ushahidi_db_version') > Kohana::config('settings.db_version') ): ?>
		<p><?php print Kohana::lang('upgrade.upgrade_db_info', Kohana::config('version.ushahidi_db_version') ); ?></p>
		<?php print form::open(NULL, array('id' => 'upgrade-db', 'name' => 'upgrade-db')); ?>
		<p>
			<?php print form::label('chk_db_backup_box', Kohana::lang('upgrade.upgrade_db_text_5'));?>
			<?php print form::checkbox('chk_db_backup_box', '1', 1);?>
		</p>
		<p>
			<?php print form::submit('submit', Kohana::lang('upgrade.upgrade_db_btn_text')); ?>
		</p>
		<?php print form::close(); ?>
	<?php else: ?>
		<p><?php print Kohana::lang('upgrade.upgrade_db_up_to_date'); ?></p>
	<?php endif; ?>
	<small><?php print Kohana::lang('upgrade.upgrade_title_text', array($current_version, $current_db_version, $environment)); ?></small>
</div>
<style>
	/* Ugly hack to hide nav */
	.nav-holder, .info-nav {display:none;}
</style>