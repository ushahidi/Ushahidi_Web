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
                            <th scope="row"><label for="select_map_provider"><?php echo Kohana::lang('installer.map_provider');?></label></th>
                            <td>
                            	<select id="select_map_provider" name="select_map_provider">
                                    <option value="1" url="http://code.google.com/apis/maps/signup.html" selected="selected">Google</option>
                                    <option value="2" url="https://www.bingmapsportal.com/">Bing</option>
                                    <option value="3" url="http://developer.yahoo.com/maps/">Yahoo</option>
                                    <option value="4" url="http://www.openstreetmap.org/user/new">Open Street Maps</option>
                                </select>                                
                            </td>
                            <td><?php echo Kohana::lang('installer.map_provider_description');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label id="map-provider-label" for="map_provider_api_key"><?php echo Kohana::lang('installer.google_key');?></label></th>
                            <td><input type="text" value="<?php print $form->value('map_provider_api_key') == "" ? $_SESSION['map_provider_api_key'] : $form->value('map_provider_api_key'); ?>" size="25" id="map_provider_api_key" name="map_provider_api_key"/></td>
                            <td><?php echo Kohana::lang('installer.google_key_description');?> (<span id="map-provider-title">Google</span>).
                            </td>
                        </tr> 
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><a class="button" href="advanced_mail_server_settings.php">&larr; <?php echo Kohana::lang('installer.previous');?></a><!--<input type="button" class="button" value="&larr; Previous" />--></td>
                            <td class="prev"><input type="submit" id="advanced_map_config" name="advanced_map_config" value="Continue &rarr;" class="button"  /><!--<input type="button" class="button" value="Continue &rarr;" />--></td>
                        </tr>
                	</tbody>
                </table>
        	</form>
  </div>

</div>
</body>
</html>
