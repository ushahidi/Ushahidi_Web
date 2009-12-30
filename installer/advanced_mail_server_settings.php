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
		<li class="active"><span><?php echo Kohana::lang('installer.database');?></span></li>
		<li class=""><span><?php echo Kohana::lang('installer.general');?></span></li>
		<li class=""><span><?php echo Kohana::lang('installer.mail_server');?></span></li>
		<li class=""><span><?php echo Kohana::lang('installer.map');?></span></li>
		<li class="last"><span><?php echo Kohana::lang('installer.finished');?></span></li>
	</ol>

        	<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
        		<?php if ($form->num_errors > 0 ) { ?>
        		<div class="feedback error"><a class="btn-close" href="#">x</a>
                	<p><?php echo Kohana::lang('installer.error_summary');?>:</p>
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
                            <th scope="row"><label for="site_alert_email"><?php echo Kohana::lang('installer.site_email_alerts');?></label></th>
                            <td><input type="text" value="<?php print $form->value('site_alert_email') == "" ? $_SESSION['site_alert_email'] : $form->value('site_alert_email'); ?>" size="25" id="site_alert_email" name="site_alert_email"/></td>
                            <td><?php echo Kohana::lang('installer.site_email_alerts_description');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_username"><?php echo Kohana::lang('installer.mail_server_username');?></label></th>
                            <td><input type="text" value="<?php print $form->value('mail_server_username') == "" ? $_SESSION['mail_server_username'] : $form->value('mail_server_username'); ?>" size="25" id="mail_server_username" name="mail_server_username"/></td>
                            <td><?php echo Kohana::lang('installer.mail_server_username_description');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_pwd"><?php echo Kohana::lang('installer.mail_server_password');?> </label></th>
                            <td><input type="password" value="<?php print $form->value('mail_server_pwd') == "" ? $_SESSION['mail_server_pwd'] : $form->value('mail_server_pwd'); ?>" size="25" id="mail_server_pwd" name="mail_server_pwd"/></td>
                            <td><?php echo Kohana::lang('ui_main.<?php echo Kohana::lang('installer.mail_server_password_description');?>');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_port"><?php echo Kohana::lang('installer.mail_server_port');?></label></th>
                            <td><input type="text" value="<?php print $form->value('mail_server_port') == "" ? $_SESSION['mail_server_port']: $form->value('mail_server_port'); ?>" size="25" id="mail_server_port" name="mail_server_port"/></td>
                            <td><?php echo Kohana::lang('installer.mail_server_port_description');?></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_server_host"><?php echo Kohana::lang('installer.mail_server_host');?></label></th>
                            <td><input type="text" value="<?php print $form->value('mail_server_host') == "" ? $_SESSION['mail_server_host']: $form->value('mail_server_host'); ?>" size="25" id="mail_server_host" name="mail_server_host"/></td>
                            <td><?php echo Kohana::lang('installer.mail_server_host_description');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="select_mail_server_type"><?php echo Kohana::lang('installer.mail_server_type');?></label></th>
                            <td>
                            	<select name="select_mail_server_type">
                                    <option value="imap" selected="selected">IMAP</option>
                                    <option value="pop">POP</option>
                                </select>
                            </td>
                            <td><?php echo Kohana::lang('installer.mail_server_type_description');?></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="select_mail_server_ssl"><?php echo Kohana::lang('installer.select_mail_server_ssl');?></label></th>
                            <td>
                            	<select name="select_mail_server_ssl">
                                    <option value="0" selected="selected"><?php echo Kohana::lang('installer.disable');?></option>
                                    <option value="1"><?php echo Kohana::lang('installer.enable');?></option>
                                </select>
                            </td>
                            <td><?php echo Kohana::lang('installer.select_mail_server_ssl_description');?>.</td>
                        </tr>
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><a class="button" href="advanced_general_settings.php">&larr; <?php echo Kohana::lang('installer.previous');?></a></td>
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
