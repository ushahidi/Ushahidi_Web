<?php 
    require_once('install.php');
    global $install;
    
    $header = $install->_include_html_header();
    print $header;
 ?>
<body>
<div id="ushahidi_login_container" class="advanced">
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
        		<!--<div class="feedback error">
                	<p>Listed below is a summary of the errors we encountered:</p>
	   				<ul id="error-list">
                    	<li>Please make sure your <strong>Site Email Address</strong> is a valid email address.</li>
                        <li>Please make sure your <strong>Site Alert Email Address</strong> is a valid email address.</li>
                        <li>Please enter a <strong>Mail Server Username</strong>.</li>
                        <li>Please make sure your <strong>Mail Server Password</strong>.</li>
                        <li>Please enter a <strong>Mail Server Port</strong>.</li>
                        <li>Please make sure your <strong>Mail Server Host</strong>.</li>
					</ul>
				</div>-->
                
                <div class="feedback info"><a class="btn-close" href="#">x</a>
                	<p>This is an example of an informative message box that we would use to add a bit more context. Not sure if this page needs one or not.</p>
				</div>
                
				<table class="form-table fields">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="select_map_provider">Map Provider</label></th>
                            <td>
                            	<select id="select_map_provider" name="select_map_provider">
                                    <option value="google" url="http://code.google.com/apis/maps/signup.html" selected="selected">Google</option>
                                    <option value="bing" url="https://www.bingmapsportal.com/">Bing</option>
                                    <option value="yahoo" url="http://developer.yahoo.com/maps/">Yahoo</option>
                                    <option value="openstreetmaps" url="http://www.openstreetmap.org/user/new">Open Street Maps</option>
                                </select>                                
                            </td>
                            <td>Ushahidi works equally well with any of these four mapping providers: Google, Bing, Yahoo or Open Street Map.  Choose the one that has the most detail in your area.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label id="map-provider-label" for="map_provider_api_key"><span>Google</span> API Key</label></th>
                            <td><input type="text" value="" size="25" id="map_provider_api_key" name="map_provider_api_key"/></td>
                            <td>Anyone can get an api key. <a id="api-link" href="http://code.google.com/apis/maps/signup.html">Get yours now</a> (<span id="map-provider-title">Google</span>).
                            </td>
                        </tr> 
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><a class="button" href="advanced_mail_server_settings.php">&larr; Previous</a><!--<input type="button" class="button" value="&larr; Previous" />--></td>
                            <td class="prev"><a class="button" href="advanced_finished.php">Continue &rarr;</a><!--<input type="button" class="button" value="Continue &rarr;" />--></td>
                        </tr>
                	</tbody>
                </table>
        	</form>
            <p></p>
  </div>

</div>
</body>
</html>
