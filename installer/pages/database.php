<body>
	<div id="ushahidi_install_container" class="advanced">
		<div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
		<div id="ushahidi_login">
			<ol class="progress-meter clearfix">
				<li class="active"><span>Database</span></li>
				<li class=""><span>General</span></li>
		
				<?php if ($install_mode === 'advanced'): ?>
				<li class=""><span>Mail Server</span></li>
				<li class=""><span>Map</span></li>
				<?php endif; ?>
		
				<li class=""><span>Admin Password</span></li>
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
							<th scope="row"><label for="database">Database Name</label></th>
							<td><input type="text" value="<?php print $form['database']; ?>" size="25" id="db_name" name="database"/></td>
							<td>The name of the database you want to run Ushahidi in. </td>
						</tr>
						<tr>
							<th scope="row"><label for="username">User Name</label></th>
							<td><input type="text" value="<?php print $form['username']; ?>" size="25" id="username" name="username"/></td>
							<td>Your database username.</td>
						</tr>
						<tr>
							<th scope="row"><label for="password">Password</label></th>
							<td><input type="password" value="<?php print $form['password']; ?>" size="25" id="password" name="password"/></td>
							<td>Your database password.</td>
						</tr>
						
						<tr>
							<th scope="row"><label for="host">Database Host</label></th>
							<td><input type="text" value="<?php print $form['host']; ?>" size="25" id="host" name="host"/></td>
							<td>If you are running Ushahidi on your own computer, this will more than likely be "localhost". If you are running Ushahidi from a web server, you'll get your host information from your web hosting provider.</td>
						</tr>
						<tr>
							<th scope="row"><label for="table_prefix">Table Prefix</label></th>
							<td><input type="text" size="25" value="<?php print $form['table_prefix']; ?>" id="table_prefix" name="table_prefix"/></td>
							<td>Normally you won't change the table prefix.	 However, If you want to run multiple Ushahidi installations from a single database you can do that by changing the prefix here.</td>
						</tr>
						<input type="hidden" name="connection" />
						<input type="hidden" name="permission" />
						<input type="hidden" name="load_db_tpl" />
						<input type="hidden" name="load_htaccess_file" />
						<input type="hidden" name="config_perm" />
						<input type="hidden" name="htaccess_perm" />
					</tbody>
				</table>

				
				<div class="actions clearfix">
					<div class="next"><input type="submit" name="continue" value="Continue &rarr;" class="button" /></div>
					<div class="prev"><!-- <input type="submit" name="previous" value="&larr; Previous" class="button" /> --></div>
				</div>
			</form>
		</div>
	<div>
</body>
</html>