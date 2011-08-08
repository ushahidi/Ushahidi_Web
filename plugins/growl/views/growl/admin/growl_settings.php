<table style="width: 630px;" class="my_table">
	<tr>
		<td style="width:60px;">
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 1:</span>
		</td>
		<td>
			<h4 class="fix">Setup Instructions</h4>
			<div>In order for this plugin to work properly, you will need to do a few things outside of your Ushahidi deployment. Follow the steps outlined below.</div>
			<ol>
				<li><a href="http://growl.info/" target="_blank">Download the latest version of Growl</a></li>
				<li>Open your Growl settings and click on the "Network" tab.</li>
				<li>Check the two boxes for "Listen for incoming notifications" and "Allow remote application registration."</li>
				<li>Create a server password.</li>
				<li>Open your router settings and make sure port <strong>9887</strong> is forwarding to your computer. If you need help with this step, check out <a href="http://portforward.com/" target="_blank">http://portforward.com</a></li>
				<li>Get your <a href="http://externalipaddress.com/" target="_blank">external IP address</a> and enter it in the box below, along with the server password you created earlier.</li>
				<li>Drink a celebratory beverage.</li>
			</ol>
		</td>
	</tr>
	<tr>
		<td>
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 2:</span>
		</td>
		<td>
			<h4 class="fix">Growl Details</h4>
			<div class="row">
				<h4>IP:</h4>
				<?php print form::input('ips', $form['ips'], ' class="text title_2"'); ?>
			</div>
			<div class="row">
				<h4>Password:</h4>
				<?php print form::password('passwords', $form['passwords'], ' class="text title_2"'); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 3:</span>
		</td>
		<td>
			<h4 class="fix">Select Notification Settings</h4>
			<div class="row">
				<h5>You are currently subscribed for everything the plugin supports. Check for updates to see if you will have a little more control in what notifications you receive.</h5>
			</div>
		</td>
	</tr>							
</table>