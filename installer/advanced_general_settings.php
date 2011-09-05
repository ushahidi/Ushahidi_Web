<?php 
	require_once('install.php');
	global $install;
	
	if(!isset( $_SESSION['general_settings']) && $_SESSION['general_settings'] != "general_settings"){
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
		<li class="active"><span>General</span></li>
		<li class=""><span>Mail Server</span></li>
		<li class=""><span>Map</span></li>
		<li class="last"><span>Finished</span></li>
	</ol>

			<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
				<input type="hidden" name="table_prefix" value="<?php echo $_SESSION['table_prefix']; ?>">
				<?php if ($form->num_errors > 0 ) { ?>
				<div class="feedback error"><a class="btn-close" href="#">x</a>
					<p>Listed below is a summary of the errors we encountered:</p>
					<ul id="error-list">
						<?php
							print ( $form->error('site_name') == "" ) ? '' : 
							"<li>".$form->error('site_name')."</li>";
							
							print ( $form->error('site_tagline') == "" ) ? '' : 
							"<li>".$form->error('site_tagline')."</li>";
							
							print ( $form->error('select_language') == "" ) ? '' : 
							"<li>".$form->error('select_language')."</li>";
							
							print ( $form->error('site_email') == "" ) ? '' : 
							"<li>".$form->error('site_email')."</li>";
							
						?>
					</ul>
				</div>
				<?php } ?>
				
				<table class="form-table fields">
					<tbody>
						<tr>
							<th scope="row"><label for="site_name">Site Name</label></th>
							<td><input type="text" value="<?php print $form->value('site_name') == "" ? !empty($_SESSION['site_name']) : $form->value('site_name'); ?>" size="25" id="site_name" name="site_name"/></td>
							<td>The name of your site.</td>
						</tr>
						<tr>
							<th scope="row"><label for="site_tagline">Site Tagline.</label></th>
							<td><input type="text" value="<?php print $form->value('site_tagline') == "" ? !empty($_SESSION['site_tagline']) : $form->value('site_tagline'); ?>" size="25" id="site_tagline" name="site_tagline"/></td>
							<td>Your tagline </td>
						</tr>
						 <tr>
							<th scope="row"><label for="select_language">Default Language (Locale)</label></th>
							<td>
								<select name="select_language">
									<option value="en_US" selected="selected">English (US)</option>
									<option value="fr_FR">Français</option>
								</select>
							</td>
							<td>Each instance of Ushahidi comes with a set of built in language translations. You can also <a href="http://wiki.ushahidi.com/doku.php?id=localisation_l10n_internationlisation_i18n&s[]=language#enabling_new_languages" target="_blank">add your own</a>.</td>
						</tr>
						<tr>
							<th scope="row"><label for="site_email">Site Email Address</label></th>
							<td><input type="text" value="<?php print $form->value('site_email') == "" ? !empty($_SESSION['site_email']) : $form->value('site_email'); ?>" size="25" id="site_email" name="site_email"/></td>
							<td>Site wide email communication will be funneled through this address.</td>
						</tr>
					   	<tr>
							<th scope="row"><label for="enable_clean_url">Enable Clean URLs</label></th>
							<?php if( !$install->_check_for_clean_url() ) { ?>
							<td>
								<select name="enable_clean_url" disabled="true">
									<option value="1" >Yes</option>
									<option value="0" selected="selected">No</option>
								</select>
							</td>
							<td>It looks like your server is not configured to handle clean URLs. You will need to change the configuration of your server before you can enable clean URLs. See more info on how to enable clean URLs at this forum <a href="http://forums.ushahidi.com/topic/server-configuration-for-apache-mod-rewrite" target="_blank">post</a> </td>		
							<?php }else {?>
							<td>
								<select name="enable_clean_url">
									<option value="1" selected="selected">Yes</option>
									<option value="0">No</option>
								</select>
							</td>
							<td>This option makes Ushahidi to be accessed via "clean" URLs without "index.php" in the URL.</td>
							<?php }?>
						</tr>
					</tbody>
				</table>
				<table class="form-table">
					<tbody>
						<tr>
							<td class="next"><a class="button" href="advanced_db_info.php">&larr; Previous</a><!--<input type="button" class="button" value="&larr; Previous" />--></td>
							<td class="prev"><input type="submit" id="advanced_general_settings" name="advanced_general_settings" value="Continue &rarr;" class="button"  /><!--<input type="button" class="button" value="Continue &rarr;" />--></td>
						</tr>
					</tbody>
				</table>
			</form>
			<p></p>
  </div>

</div>
</body>
</html>
