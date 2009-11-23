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
		<li class="active"><span>General</span></li>
		<li class=""><span>Mail Server</span></li>
		<li class=""><span>Map</span></li>
		<li class="last"><span>Finished</span></li>
	</ol>

        	<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
        		<div class="feedback error"><a class="btn-close" href="#">x</a>
                	<p>Listed below is a summary of the errors we encountered:</p>
	   				<ul id="error-list">
                    	<li>Please make sure your <strong>Site Email Address</strong> is a valid email address.</li>
                        <li>Please make sure your <strong>Site Alert Email Address</strong> is a valid email address.</li>
					</ul>
				</div>
                
				<table class="form-table fields">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="site_name">Site Name</label></th>
                            <td><input type="text" value="Name your ushahidi instance" size="25" id="site_name" name="site_name"/></td>
                            <td>The name of your site.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="site_tagline">Site Tagline.</label></th>
                            <td><input type="text" value="Insert your tagline here" size="25" id="site_tagline" name="site_tagline"/></td>
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
                            <td>Each instance of Ushahidi comes with a set of built in language translations. You can also <a href="http://wiki.ushahidi.com/doku.php?id=localisation_l10n_internationlisation_i18n&s[]=language#enabling_new_languages">add your own</a>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="site_email">Site Email Address</label></th>
                            <td><input type="text" value="email@address.org" size="25" id="site_email" name="site_email"/></td>
                            <td>Site wide email communication will be funneled through this address.</td>
                        </tr>
                       
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><a class="button" href="advanced_db_info.php">&larr; Previous</a><!--<input type="button" class="button" value="&larr; Previous" />--></td>
                            <td class="prev"><a class="button" href="advanced_mail_server_settings.php">Continue &rarr;</a><!--<input type="button" class="button" value="Continue &rarr;" />--></td>
                        </tr>
                	</tbody>
                </table>
        	</form>
            <p></p>
  </div>

</div>
</body>
</html>
