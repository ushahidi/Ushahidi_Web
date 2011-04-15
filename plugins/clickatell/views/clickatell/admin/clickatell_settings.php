<table style="width: 630px;" class="my_table">
	<tr>
		<td style="width:60px;">
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 1:</span>
		</td>
		<td>
			<h4 class="fix"><?php echo Kohana::lang('settings.sms.clickatell_text_1');?>. <sup><a href="#">?</a></sup></h4>
		</td>
	</tr>
	<tr>
		<td>
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 2:</span>
		</td>
		<td>
			<h4 class="fix"><?php echo Kohana::lang('settings.sms.clickatell_text_2');?>. <sup><a href="#">?</a></sup></h4>
			<div class="row">
				<h4><?php echo Kohana::lang('settings.sms.clickatell_api');?>:</h4>
				<?php print form::input('clickatell_api', $form['clickatell_api'], ' class="text title_2"'); ?>
			</div>
			<div class="row">
				<h4><?php echo Kohana::lang('settings.sms.clickatell_username');?>:</h4>
				<?php print form::input('clickatell_username', $form['clickatell_username'], ' class="text title_2"'); ?>
			</div>
			<div class="row">
				<h4><?php echo Kohana::lang('settings.sms.clickatell_password');?>:</h4>
				<?php print form::password('clickatell_password', $form['clickatell_password'], ' class="text title_2"'); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<span class="big_blue_span">Step 3:</span>
		</td>
		<td>
			<h4 class="fix"><?php echo Kohana::lang('settings.sms.clickatell_check_balance');?>. <sup><a href="#">?</a></sup></h4>
			<div class="row">
				<h4><a href="javascript:clickatellBalance()"><?php echo Kohana::lang('settings.sms.clickatell_load_balance');?></a>&nbsp;<span id="balance_loading"></span></h4>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 4:</span>
		</td>
		<td>
			<h4 class="fix"><a href="#" class="tooltip" title="">Working with Clickatell 2-Way</a></h4>
			<p>
				If you sign up for Clickatell 2-Way service they will ask you for a 'Primary Callback URL'. Use the URL below as the 'Target Address' and select 'HTTP POST' from the drop down.
			</p>
			<p class="sync_key">
				<span><?php echo $clickatell_link; ?></span>
			</p>
		</td>
	</tr>								
</table>