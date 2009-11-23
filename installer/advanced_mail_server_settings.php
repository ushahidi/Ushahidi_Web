<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mail Server Settings / Ushahidi Web Installer</title>
<link href="../media/css/admin/login.css" rel="stylesheet" type="text/css" />
</head>
<script src="../media/js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="../media/js/login.js" type="text/javascript" charset="utf-8"></script>

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
        		<div class="feedback error"><a class="btn-close" href="#">x</a>
                	<p>Listed below is a summary of the errors we encountered:</p>
	   				<ul id="error-list">
                    	<li>Please make sure your <strong>Site Email Address</strong> is a valid email address.</li>
                        <li>Please make sure your <strong>Site Alert Email Address</strong> is a valid email address.</li>
                        <li>Please enter a <strong>Mail Server Username</strong>.</li>
                        <li>Please make sure your <strong>Mail Server Password</strong>.</li>
                        <li>Please enter a <strong>Mail Server Port</strong>.</li>
                        <li>Please make sure your <strong>Mail Server Host</strong>.</li>
					</ul>
                    
				</div>
                
                <div id="" class="feedback info">
                	<p>This is an example of an informative message box that we would use to add a bit more context. Not sure if this page needs one or not.</p>
				</div>
               
				<table class="form-table fields">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="site_alert_email">Site Alert Email Address</label></th>
                            <td><input type="text" value="alerts@email.org" size="25" id="site_alert_email" name="site_alert_email"/></td>
                            <td>When your site visitors sign up for email alerts, they will recieve emails from this address. This email address does not have to be the same as the Site Email Address.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_username">Mail Server Username</label></th>
                            <td><input type="text" value="" size="25" id="mail_server_username" name="mail_server_username"/></td>
                            <td>If you're using Gmail, Hotmail, or Yahoo Mail, enter a full email address as a username.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_pwd">Mail Server Password </label></th>
                            <td><input type="password" value="" size="25" id="mail_server_pwd" name="mail_server_pwd"/></td>
                            <td>The password you normally use to login in to your email.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_port">Mail Server Port</label></th>
                            <td><input type="text" value="25" size="25" id="mail_server_port" name="mail_server_port"/></td>
                            <td>Common Ports: 25, 110, 995 (Gmail POP3 SSL), 993 (Gmail IMAP SSL) .</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_host">Mail Server Host</label></th>
                            <td><input type="text" value="mailserver.yourwebsite.com" size="25" id="mail_server_host" name="mail_server_host"/></td>
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
                            <td>Internet Message Access Protocol (IMAP) or Post Office Protocol (POP). <a href="http://saturn.med.nyu.edu/it/help/email/imap/index.html">What's the difference?</a></td>
                        </tr>
                        
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><a class="button" href="advanced-general-settings.html">&larr; Previous</a><!--<input type="button" class="button" value="&larr; Previous" />--></td>
                            <td class="prev"><a class="button" href="advanced-map-configuration.html">Continue &rarr;</a><!--<input type="button" class="button" value="Continue &rarr;" />--></td>
                        </tr>
                	</tbody>
                </table>
        	</form>
            <p></p>
  </div>

</div>
</body>
</html>
