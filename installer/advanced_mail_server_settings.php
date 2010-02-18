<?php 
    require_once('install.php');
    global $install;
    
    if(!isset( $_SESSION['mail_server']) && $_SESSION['mail_server'] != "mail_server"){
    	header('Location:advanced_general_settings.php');
    }
    
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
		<li class="active"><span>Mail Server</span></li>
		<li class=""><span>Map</span></li>
		<li class="last"><span>Finished</span></li>
	</ol>

        	<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
        		<?php if ($form->num_errors > 0 ) { ?>
        		<div class="feedback error"><a class="btn-close" href="#">x</a>
                	<p>Listed below is a summary of the errors we encountered:</p>
	   				<ul id="error-list">
                    	<?php
	   				    	print ( $form->error('site_alert_email') == "" ) ? '' : 
							"<li>".$form->error('site_alert_email')."</li>";
							
							print ( $form->error('mail_server_username') == "" ) ? '' : 
							"<li>".$form->error('mail_server_username')."</li>";
							
							print ( $form->error('mail_server_pwd') == "" ) ? '' : 
							"<li>".$form->error('mail_server_pwd')."</li>";
							
							print ( $form->error('mail_server_port') == "" ) ? '' : 
							"<li>".$form->error('mail_server_port')."</li>";
							
							print ( $form->error('mail_server_host') == "" ) ? '' : 
							"<li>".$form->error('mail_server_host')."</li>";
							
							print ( $form->error('select_mail_server_type') == "" ) ? '' : 
							"<li>".$form->error('select_mail_server_type')."</li>";							
	   				    ?>
					</ul>
				</div>
                <?php } ?>
				<table class="form-table fields">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="site_alert_email">Site Alert Email Address</label></th>
                            <td><input type="text" value="<?php print $form->value('site_alert_email') == "" ? $_SESSION['site_alert_email'] : $form->value('site_alert_email'); ?>" size="25" id="site_alert_email" name="site_alert_email"/></td>
                            <td>When your site visitors sign up for email alerts, they will recieve emails from this address. This email address does not have to be the same as the Site Email Address.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_username">Mail Server Username</label></th>
                            <td><input type="text" value="<?php print $form->value('mail_server_username') == "" ? $_SESSION['mail_server_username'] : $form->value('mail_server_username'); ?>" size="25" id="mail_server_username" name="mail_server_username"/></td>
                            <td>If you're using Gmail, Hotmail, or Yahoo Mail, enter a full email address as a username.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_pwd">Mail Server Password </label></th>
                            <td><input type="password" value="<?php print $form->value('mail_server_pwd') == "" ? $_SESSION['mail_server_pwd'] : $form->value('mail_server_pwd'); ?>" size="25" id="mail_server_pwd" name="mail_server_pwd"/></td>
                            <td>The password you normally use to login in to your email.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_port">Mail Server Port</label></th>
                            <td><input type="text" value="<?php print $form->value('mail_server_port') == "" ? $_SESSION['mail_server_port']: $form->value('mail_server_port'); ?>" size="25" id="mail_server_port" name="mail_server_port"/></td>
                            <td>Common Ports: 25, 110, 995 (Gmail POP3 SSL), 993 (Gmail IMAP SSL) .</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_host">Mail Server Host</label></th>
                            <td><input type="text" value="<?php print $form->value('mail_server_host') == "" ? $_SESSION['mail_server_host']: $form->value('mail_server_host'); ?>" size="25" id="mail_server_host" name="mail_server_host"/></td>
                            <td>Examples: mail.yourwebsite.com, imap.gmail.com, pop.gmail.com.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="select_mail_server_type">Mail Server Type</label></th>
                            <td>
                            	<select name="select_mail_server_type">
                                    <option value="imap" selected="selected">IMAP</option>
                                    <option value="pop">POP</option>
                                </select>
                            </td>
                            <td>Internet Message Access Protocol (IMAP) or Post Office Protocol (POP). <a href="http://saturn.med.nyu.edu/it/help/email/imap/index.html" target="_blank">What's the difference?</a></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="select_mail_server_ssl">Enable or disable SSL</label></th>
                            <td>
                            	<select name="select_mail_server_ssl">
                                    <option value="0" selected="selected">Disable</option>
                                    <option value="1">Enable</option>
                                </select>
                            </td>
                            <td>Some mail servers give you the option of using <abbr title="Secure Sockets Layer">SSL</abbr> when making a connection. Using SSL is recommended as it gives you an added level of security.</td>
                        </tr>
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><a class="button" href="advanced_general_settings.php">&larr; Previous</a></td>
                            <td class="prev"><input type="submit" id="advanced_mail_server_settings" name="advanced_mail_server_settings" value="Continue &rarr;" class="button"  /></td>
                        </tr>
                	</tbody>
                </table>
        	</form>
            <p></p>
  </div>

</div>
</body>
</html>
