<table style="width: 630px;" class="my_table">
	<tr>
		<td style="width:60px;">
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 1:</span>
		</td>
		<td>
			<h4 class="fix"><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_flsms_download"); ?>"><?php echo Kohana::lang('settings.sms.flsms_download');?></a></h4>
			<p>
				<?php echo Kohana::lang('settings.sms.flsms_description');?>.
			</p>
			<a href="http://www.frontlinesms.com/the-software/frontlinesms-version-1/" class="no_border">
				<img src="<?php echo url::base() ?>media/img/admin/download_frontline_engine.gif" />
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 2:</span>
		</td>
		<td>
			<h4 class="fix"><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_flsms_synchronize"); ?>"><?php echo Kohana::lang('settings.sms.flsms_synchronize');?></a></h4>
			<p>
				<?php echo Kohana::lang('settings.sms.flsms_instructions');?>.
			</p>
			<p class="sync_key">
				<?php echo Kohana::lang('settings.sms.flsms_key');?>: <span><?php echo $frontlinesms_key; ?></span><br /><br />
				<?php echo Kohana::lang('settings.sms.flsms_link');?>:<br /><span><?php echo $frontlinesms_link; ?></span>
			</p>
		</td>
	</tr>	
</table>
