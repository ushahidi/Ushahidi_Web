<body>
	<div id="ushahidi_install_container" class="advanced">
		<div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
		<div id="ushahidi_login">
			<ol class="progress-meter clearfix">
				<li><span>Database</span></li>
				<li><span>General</span></li>
				<?php if ($install_mode === 'advanced'): ?>
				<li><span>Mail Server</span></li>
				<li class="active"><span>Map</span></li>
				<?php endif; ?>
				<li><span>Admin Password</span></li>
				<li class="last"><span>Finished</span></li>
			</ol>

			<form method="POST" name="frm_install" action="" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">
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
							<th scope="row"><label for="default_map">Map Provider</label></th>
							<td>
								<select name="default_map" id="default_map">
									<option selected="selected">--Select Map Provider</option>
									<option value="osm_mapnik" url="http://www.openstreetmap.org/user/new">OpenStreetMap</option>
									<option value="google_normal" url="https://developers.google.com/maps/signup">Google</option>
									<option value="bing_road" url="https://www.bingmapsportal.com/">Bing Maps</option>
								</select>								 
							</td>
							<td>Ushahidi works equally well with any of these three mapping providers: Google, Bing or OpenStreetMap. Choose the one that has the most detail in your area.</td>
						</tr>
						<tr id="api_key_row">
							<th scope="row"><label id="map_provider_label" for="api_key"><span>Google</span> API Key</label></th>
							<td><input type="text" value="" size="25" name="api_key"/></td>
							<td>Anyone can get an api key. <a id="api-link" href="http://code.google.com/apis/maps/signup.html" target="_blank">Get yours now</a> (<span id="map-provider-title">Google</span>).
							</td>
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
<body>
<script type="text/javascript">
	jQuery(function(){
		$("#api_key_row").hide();
		
		$("#default_map").change(function(){
			if ($(this).val() == "" || $(this).val() == null)
				return;
				
			if ($(this).val() == 'bing_road') {
				var providerTitle = $(":selected", this).text();
				var labelHTML = "<span>"+providerTitle+"</span> API Key";
				$("#api_key_row").show();
				$("#map_provider_label", "#api_key_row").html(labelHTML);
				
				// Get the url
				$("#api-link").attr({href: $(":selected", this).attr("url")});
				$("#map-provider-title").html(providerTitle);

			} else {

				$("#api_key_row").hide();
			}
		});
	});
</script>
</html>
