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
		
				<li class="active"><span>Admin Password</span></li>
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
				
				<div class="feedback info">
				 	<p>You can make use of this <a href="http://strongpasswordgenerator.com/">strong password generator</a>. Please note that the only allowable symbols in your password are the # and @symbol.</p> 
				</div> 
				<table class="form-table fields">
					<tbody>
						<!-- The design of the password form -->
						<tr>
							<th scope="row"><label for="email">Admin Email</label></th>
							<td><input value="<?php print $form['email']; ?>" size="25" name="email" /></td>
							<td>The administrative user will log in with this email address.</td>
						</tr>
						<tr>
							<th scope="row"><label for="password">Password</label></th>
							<td><input type="password" value="" size="25" name="password" /></td>
							<td>Please type in your preferred password for your deployment's administrative user.</td>
						</tr>
						<tr>
							<th scope="row"><label for="confirm_password">Confirm Password</label></th>
							<td><input type="password" value="" size="25" name="confirm_password"></td>
							<td>Please re-type your password for confirmation.</td>
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