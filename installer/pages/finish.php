<body>
	<div id="ushahidi_install_container" class="advanced">
		<div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
		<div id="ushahidi_login">
			<ol class="progress-meter clearfix">
				<li><span>Database</span></li>
				<li><span>General</span></li>
				<?php if ($install_mode === 'advanced'): ?>
				<li><span>Mail Server</span></li>
				<li><span>Map</span></li>
				<?php endif; ?>
				<li><span>Admin Password</span></li>
				<li class="active last"><span>Finished</span></li>
			</ol>
			<form method="POST" name="frm_install" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">
				<div class="feedback success">
					<h2>Installation Successful!</h2>
				</div>
				<p>To login, go to <a href="<?php echo $base_url; ?>admin" target="_blank">
				<?php echo $base_url."admin"; ?></a> and use the following credentials:<br /><br />
				<strong>Login Email:</strong> <?php echo $admin_email; ?><br />
				<strong>Password:</strong> (not shown)</p>
				<p><strong>Other next steps...</strong></p>
				<ul>
					<li><a href="<?php echo $base_url; ?>" target="_blank">View your website</a></li>
					<li><a href="<?php echo $base_url; ?>admin/reports/edit" target="_blank">Upload report data</a></li>
					<li><a href="<?php echo $base_url; ?>admin/settings" target="_blank">Configure your map</a></li>
					<li><a href="<?php echo $base_url; ?>admin/settings/sms" target="_blank">Setup your SMS server</a></li>
				</ul>
			</form>
		</div>
	</div>
</body>
</html>