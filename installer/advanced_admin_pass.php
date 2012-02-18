<?php
	require_once('install.php');
	global $install;
	
	if(!isset( $_SESSION['advanced_admin_pass']) && $_SESSION['advanced_admin_pass'] != 'map_settings'){
		header('Location:.');
	}
	
	$header = $install->_include_html_header();
	print $header;
?>
<body>
<div id="ushahidi_install_container" class="advanced">
	<div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
	<div id="ushahidi_login">
		<ol class="progress-meter clearfix">
			<li class=""><span>Database</span></li>
			<li class=""><span>General</span></li>
			<li class=""><span>Mail Server</span></li>
			<li class=""><span>Map</span></li>
			<li class="active"><span>Admin Password</span></li>
			<li class="last"><span>Finished</span></li>
		</ol>
		<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
			<input type="hidden" name="table_prefix" value="<?php echo $_SESSION['table_prefix']; ?>">
			<?php if($form->num_errors > 0): ?>
				<div class="feedback error"><a class="btn-close" href="#">x</a>
					<p>Listed below is a summary of all the errors we encountered:</p>
					<ul id="error-list">
						<?php
						//print out the errors once you've set out which errors they'll be
						print ( $form->error('password') == "" ) ? '' : 
						"<li>".$form->error('password')."</li>";
						print ( $form->error('confirm') == "" ) ? '' : 
						"<li>".$form->error('confirm')."</li>";
						print ( $form->error('match') == "" ) ? '' : 
						"<li>".$form->error('match')."</li>";
						print ( $form->error('length') == "" ) ? '' : 
						"<li>".$form->error('length')."</li>";
						print ( $form->error('invalid') == "" ) ? '' : 
						"<li>".$form->error('invalid')."</li>";
						?>
					</ul>
				</div>
			<?php endif ?>
			<div class="feedback info">
			 	<p>You can make use of this <a href="http://strongpasswordgenerator.com/">strong password generator</a>. Please note that the only allowable symbols in your password are the # and @symbol.</p> 
			</div> 
			<table class="form-table fields">
				<tbody>
					<!-- The design of the password form -->
					<tr>
						<th scope="row"><label for="password">Password</label></th>
						<td><input type="password" value="<?php print $form->value('admin_password') == ""?
						(empty($_SESSION['admin_password']) ?null: $_SESSION['admin_password']) : $form->value('admin_password')?>" size="25" id="admin_password" name="admin_password" /></td>
						<td>Please type in your preferred password for your deployment's administrative user.</td>
					</tr>
					<tr>
						<th scope="row"><label for="confirm_password">Confirm Password</label></th>
						<td><input type="password" value="<?php print $form->value('admin_password_again') == ""?
						(empty($_SESSION['admin_password_again']) ?null: $_SESSION['admin_password_again']) : $form->value('admin_password_again')?>" size="25" id="admin_password_again" name="admin_password_again"></td>
						<td>Please re-type your password for confirmation.</td>
					</tr>
					<!-- TO DO: Add a password strength indicator
					<tr>
						<th scope="row"><label for="strength_indicator">Strength Indicator</label></th>
						<td>Strength indicator</td>
						<td>Make sure that you password is no less than 8 characters long, and contains alphabetical characters, numbers, the # and @ symbol, dashes and underscores only.</td> 
					</tr> -->
				</tbody>
			</table>
			<table class="form-table">
				<tbody>
					<tr>
						<td class="next"><a class="button" href="advanced_map_configuration.php">&larr; Previous</a></td>
						<td class="prev"><input type="submit" id="advanced_admin_pass" name="advanced_admin_pass" value="Continue &rarr;" class="button" /></td>
					</tr>
				</tbody>
			</table>
		</form>
		<p></p>
	</div>
</div>
</body>
</html>