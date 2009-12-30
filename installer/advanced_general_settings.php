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
                            <th scope="row"><label for="site_name"><?php echo Kohana::lang('installer.site_name');?></label></th>
                            <td><input type="text" value="<?php print $form->value('site_name') == "" ? $_SESSION['site_name'] : $form->value('site_name'); ?>" size="25" id="site_name" name="site_name"/></td>
                            <td><?php echo Kohana::lang('installer.site_name_description');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="site_tagline"><?php echo Kohana::lang('installer.site_tagline');?>.</label></th>
                            <td><input type="text" value="<?php print $form->value('site_tagline') == "" ? $_SESSION['site_tagline'] : $form->value('site_tagline'); ?>" size="25" id="site_tagline" name="site_tagline"/></td>
                            <td><?php echo Kohana::lang('installer.site_tagline_description');?> </td>
                        </tr>
                         <tr>
                            <th scope="row"><label for="select_language"><?php echo Kohana::lang('installer.default_language');?></label></th>
                            <td>
                            	<select name="select_language">
                                    <option value="en_US" selected="selected">English (US)</option>
                                    <option value="fr_FR">Français</option>
                                    <option value="es_AR">Español</option>
                                </select>
                            </td>
                            <td><?php echo Kohana::lang('installer.default_language_description');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="site_email"><?php echo Kohana::lang('installer.site_email');?></label></th>
                            <td><input type="text" value="<?php print $form->value('site_email') == "" ? $_SESSION['site_email'] : $form->value('site_email'); ?>" size="25" id="site_email" name="site_email"/></td>
                            <td><?php echo Kohana::lang('installer.site_email_description');?>.</td>
                        </tr>
                       
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><a class="button" href="advanced_db_info.php">&larr; <?php echo Kohana::lang('installers.previous');?></a><!--<input type="button" class="button" value="&larr; Previous" />--></td>
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
