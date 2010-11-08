<table style="width: 630px;" class="my_table">
	<tr>
		<td style="width:60px;">
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 1:</span>
		</td>
		<td>
			<h4 class="fix">Download the "SMSSync" app from the Android Market</h4>
			<p>
				Scan this QR Code with your phone to download the app from the Android Market
			</p>
			<div><img src="<?php echo url::base();?>plugins/smssync/views/images/smssync.png"></div>
		</td>
	</tr>
	
	<tr>
		<td>
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 2:</span>
		</td>
		<td>
			<h4 class="fix">Android App Settings</h4>
			<p>
				Turn on SMSSync and use the following link as the website
			</p>
			<p class="sync_key">
				<span><?php echo $smssync_url; ?></span>
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 3:</span>
		</td>
		<td>
			<h4 class="fix">*Optional - Specify a secret so that only authorized users can use the SMSSync Gateway.</h4>
			<div class="row">
				<h4>SMSSync Secret</h4>
				<?php print form::input('smssync_secret', $form['smssync_secret'], ' class="text title_2"'); ?>
			</div>
		</td>
	</tr>							
</table>