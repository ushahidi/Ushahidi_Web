<?php 
	require_once('install.php');
	global $install;
	
	if(!isset( $_SESSION['map_settings']) && $_SESSION['map_settings'] != "map_settings"){
		header('Location:advanced_mail_server_settings.php');
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
		<li class="active"><span>Map</span></li>
		<li class="last"><span>Finished</span></li>
	</ol>

			<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
				<input type="hidden" name="table_prefix" value="<?php echo $_SESSION['table_prefix']; ?>">
				<?php if ($form->num_errors > 0 ) { ?>
				<div class="feedback error"><a class="btn-close" href="#">x</a>
					<p>Listed below is a summary of the errors we encountered:</p>
					<ul id="error-list">
						<?php
							print ( $form->error('select_map_provider') == "" ) ? '' : 
							"<li>".$form->error('select_map_provider')."</li>";
							
							print ( $form->error('map_provider_api_key') == "" ) ? '' : 
							"<li>".$form->error('map_provider_api_key')."</li>";
							
						?>
					</ul>
				</div>
				<?php } ?>
				
				<div class="feedback info"><a class="btn-close" href="#">x</a>
					<p>This is an example of an informative message box that we would use to add a bit more context. Not sure if this page needs one or not.</p>
				</div>
				
				<table class="form-table fields">
					<tbody>
						<tr>
							<th scope="row"><label for="select_map_provider">Map Provider</label></th>
							<td>
								<select id="select_map_provider" name="select_map_provider">
									<option value="1" url="http://code.google.com/apis/maps/signup.html" selected="selected">Google</option>
									<option value="2" url="https://www.bingmapsportal.com/">Bing</option>
									<option value="3" url="http://developer.yahoo.com/maps/">Yahoo</option>
									<option value="4" url="http://www.openstreetmap.org/user/new">OpenStreetMap</option>
								</select>								 
							</td>
							<td>Ushahidi works equally well with any of these four mapping providers: Google, Bing, Yahoo or OpenStreetMap.  Choose the one that has the most detail in your area.</td>
						</tr>
						<tr>
							<th scope="row"><label id="map-provider-label" for="map_provider_api_key"><span>Google</span> API Key</label></th>
							<td><input type="text" value="<?php print $form->value('map_provider_api_key') == "" ? !empty($_SESSION['map_provider_api_key']) : $form->value('map_provider_api_key'); ?>" size="25" id="map_provider_api_key" name="map_provider_api_key"/></td>
							<td>Anyone can get an api key. <a id="api-link" href="http://code.google.com/apis/maps/signup.html" target="_blank">Get yours now</a> (<span id="map-provider-title">Google</span>).
							</td>
						</tr> 
					</tbody>
				</table>
				<table class="form-table">
					<tbody>
						<tr>
							<td class="next"><a class="button" href="advanced_mail_server_settings.php">&larr; Previous</a><!--<input type="button" class="button" value="&larr; Previous" />--></td>
							<td class="prev"><input type="submit" id="advanced_map_config" name="advanced_map_config" value="Continue &rarr;" class="button"  /><!--<input type="button" class="button" value="Continue &rarr;" />--></td>
						</tr>
					</tbody>
				</table>
			</form>
  </div>

</div>
</body>
</html>
