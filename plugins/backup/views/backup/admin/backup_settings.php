<table style="width: 630px;" class="my_table">

	<?php if($show_setup) { ?>

	<h2>Step 1.</h2>
	<iframe src="http://backup.ushahidi.com/signup.php?url=<?php echo $url; ?>" width="100%" height="300" style="border:0px;">
		<p>Your browser does not support iframes.</p>
	</iframe>
	
	<h2>Step 2.</h2>
	Enter signup details from above.
	
	<?php }else{ ?>
	
	<h2>Manage Backups</h2>
	<iframe src="http://backup.ushahidi.com/login.php" width="100%" height="400" style="border:0px;">
		<p>Your browser does not support iframes.</p>
	</iframe>
	
	<h2>Manage Settings</h2>
	
	<?php } ?>
	
	<h4>Email:</h4>
	<?php print form::input('email', $form['email'], ' class="text title_2"'); ?>
	<br/>
	
	<h4>Password:</h4>
	<?php print form::password('password', $form['password'], ' class="text title_2"'); ?>
	<br/>
	
	<h4>Site Key:</h4>
	<?php print form::input('key', $form['key'], ' class="text title_2"'); ?>
	<br/><br/>
	
	<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
	

</table>