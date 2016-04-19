<body>
	<div id="ushahidi_install_container" class="advanced">
		<div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
		<div id="ushahidi_login">
			<ol class="progress-meter clearfix">
				<li><span>Database</span></li>
				<li class="active"><span>General</span></li>
				<?php if ($install_mode === 'advanced'): ?>
				<li><span>Mail Server</span></li>
				<li><span>Map</span></li>
				<?php endif; ?>
				<li><span>Admin Password</span></li>
				<li class="last"><span>Finished</span></li>
			</ol>

			<form method="POST" name="frm_install" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">
				<?php if (isset($errors)): ?>
		   		<div class="feedback error"><a class="btn-close" href="#">x</a>
		       		<p>Listed below is a summary of the errors we encountered:</p>
		   				<ul id="error-list">
						<?php foreach ($errors as $error): ?>
							<li><?php print $error; ?></li>
						<?php endforeach; ?>
						</ul>
					</p>
				</div>
				<?php endif; ?>
				
				<table class="form-table fields">
					<tbody>
						<tr>
							<th scope="row"><label for="site_name">Site Name</label></th>
							<td><input type="text" value="<?php print $form['site_name']; ?>" size="25" id="site_name" name="site_name"/></td>
							<td>The name of your site.</td>
						</tr>
						<tr>
							<th scope="row"><label for="site_tagline">Site Tagline.</label></th>
							<td><input type="text" value="<?php print $form['site_tagline']; ?>" size="25" id="site_tagline" name="site_tagline"/></td>
							<td>Your tagline </td>
						</tr>
						 <tr>
							<th scope="row"><label for="site_language">Default Language (Locale)</label></th>
							<td>
								<select name="site_language">
									<option value="en_US" selected="selected">English (US)</option>
									<option value="fr_FR">Français</option>
								</select>
							</td>
							<td>Each Ushahidi deployment comes with a set of built in language translations. You can also <a href="https://wiki.ushahidi.com/display/WIKI/Localization" target="_blank">add your own</a>.</td>
						</tr>
						<tr>
							<th scope="row"><label for="site_email">Site Email Address</label></th>
							<td><input type="text" value="<?php print $form['site_email']; ?>" size="25" id="site_email" name="site_email"/></td>
							<td>Site wide email communication will be funneled through this address.</td>
						</tr>
					   	<tr>
							<th scope="row"><label for="enable_clean_url">Enable Clean URLs</label></th>
							<?php if ( ! $enable_clean_urls): ?>
							<td>
								<select name="enable_clean_urls" disabled="true">
									<option value="1" >Yes</option>
									<option value="0" selected="selected">No</option>
								</select>
							</td>
							<td>It looks like your server is not configured to handle clean URLs. You will need to change the configuration of your server before you can enable clean URLs. See more info on how to enable clean URLs at this forum <a href="http://forums.ushahidi.com/topic/server-configuration-for-apache-mod-rewrite" target="_blank">post</a> </td>		
							<?php else: ?>
							<td>
								<select name="enable_clean_urls">
									<option value="1" selected="selected">Yes</option>
									<option value="0">No</option>
								</select>
							</td>
							<td>This option makes Ushahidi to be accessed via "clean" URLs without "index.php" in the URL.</td>
							<?php endif; ?>
						</tr>
					</tbody>
				</table>
				<div class="actions clearfix">
					<div class="next"><input type="submit" name="continue" value="Continue &rarr;" class="button" /></div>
					<div class="prev"><input type="submit" name="previous" value="&larr; Previous" class="button" /></div>
				</div>

			</form>

		</div>
	</div>
</body>
</html>
