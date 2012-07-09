<body>
	<div id="ushahidi_install_container" class="advanced">
		<div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
		<div id="ushahidi_login">
			<ol class="progress-meter clearfix">
				<li><span>Database</span></li>
				<li><span>General</span></li>
		
				<?php if ($install_mode === 'advanced'): ?>
				<li class="active"><span>Mail Server</span></li>
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
							<th scope="row"><label for="alerts_email">Site Alert Email Address</label></th>
							<td><input type="text" value="<?php print $form['alerts_email']; ?>" size="25" name="alerts_email"/></td>
							<td>When your site visitors sign up for email alerts, they will recieve emails from this address. This email address does not have to be the same as the Site Email Address.</td>
						</tr>
						<tr>
							<th scope="row"><label for="email_host">Mail Server Host</label></th>
							<td><input type="text" value="<?php print $form['email_host']; ?>" size="25" name="email_host"/></td>
							<td>Examples: mail.yourwebsite.com, imap.gmail.com, pop.gmail.com.</td>
						</tr>
						<tr>
							<th scope="row"><label for="email_username">Mail Server Username</label></th>
							<td><input type="text" value="<?php print $form['email_username']; ?>" size="25" name="email_username"/></td>
							<td>If you're using Gmail, Hotmail, or Yahoo Mail, enter a full email address as a username.</td>
						</tr>
						<tr>
							<th scope="row"><label for="email_password">Mail Server Password </label></th>
							<td><input type="password" value="<?php print $form['email_password']; ?>" size="25" name="email_password"/></td>
							<td>The password you normally use to login in to your email.</td>
						</tr>
						<tr>
							<th scope="row"><label for="email_port">Mail Server Port</label></th>
							<td><input type="text" value="<?php print $form['email_port']; ?>" size="25" name="email_port"/></td>
							<td>Common Ports: 25, 110, 995 (Gmail POP3 SSL), 993 (Gmail IMAP SSL) .</td>
						</tr>
						<tr>
							<th scope="row"><label for="select_mail_server_type">Mail Server Type</label></th>
							<td>
								<select name="select_mail_server_type">
									<option value="imap" selected="selected">IMAP</option>
									<option value="pop">POP</option>
								</select>
							</td>
							<td>Internet Message Access Protocol (IMAP) or Post Office Protocol (POP). <a href="http://www1.umn.edu/adcs/guides/email/imapvspop.html" target="_blank">What's the difference?</a></td>
						</tr>
						<tr>
							<th scope="row"><label for="email_ssl">Enable or disable SSL</label></th>
							<td>
								<select name="email_ssl">
									<option value="0" selected="selected">Disable</option>
									<option value="1">Enable</option>
								</select>
							</td>
							<td>Some mail servers give you the option of using <abbr title="Secure Sockets Layer">SSL</abbr> when making a connection. Using SSL is recommended as it gives you an added level of security.</td>
						</tr>
					</tbody>
				</table>
				
				<div class="actions clearfix">
					<div class="next"><input type="submit" name="continue" value="Continue &rarr;" class="button" /></div>
					<div class="prev"><input type="submit" name="previous" value="&larr; Previous" class="button" /></div>
				</div>

			</form>
		</div>
	<div>
</body>
</html>