<?php
	require_once('install.php');
    global $install;
    
    if(!isset( $_SESSION['basic_db_info']) && $_SESSION['basic_db_info'] != "basic_summary"){
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
		<li class="last"><span><?php echo Kohana::lang('installer.finished');?></span></li>
	</ol>

        	<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
        		<?php if ($form->num_errors > 0 ) { ?>
        		<div class="feedback error"><a class="btn-close" href="#">x</a>
                	<p><?php echo Kohana::lang('installer.error_summary');?>:</p>
	   				<ul id="error-list">
	   				    <?php
	   				    	print ( $form->error('username') == "" ) ? '' : 
							"<li>".$form->error('username')."</li>";
							
							print ( $form->error('host') == "" ) ? '' : 
							"<li>".$form->error('host')."</li>";
							
							print ( $form->error('db_name') == "" ) ? '' : 
							"<li>".$form->error('db_name')."</li>";
							
							print ( $form->error('permission') == "" ) ? '' : 
							"<li>".$form->error('permission')."</li>";
							
							print ( $form->error('load_htaccess_file') == "" ) ? '' : 
							"<li>".$form->error('load_htaccess_file')."</li>";
							
							print ( $form->error('connection') == "" ) ? '' : 
							"<li>".$form->error('connection')."</li>";
							
							print ( $form->error('htaccess_perm') == "" ) ? '' : 
							"<li>".$form->error('htaccess_perm')."</li>";
							
							print ( $form->error('config_perm') == "" ) ? '' : 
							"<li>".$form->error('config_perm')."</li>";
	   				    ?>
					</ul>
				</div>
				<?php } ?>
                <div class="feedback info">
                	<p><?php echo Kohana::lang('installer.db_information_link');?>.</p>
                </div>
                
				<table class="form-table fields">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="base_path"><?php echo Kohana::lang('installer.base_path');?></label></th>
                            <td><input type="text" value="<?php print $form->value('base_path') == "" ? $install->_get_base_path($_SERVER["REQUEST_URI"]) : $form->value('base_path'); ?>" size="25" id="base_path" name="base_path"/></td>
                            <td><?php echo Kohana::lang('installer.files_location_text');?>.
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="db_name"><?php echo Kohana::lang('installer.database_name');?></label></th>
                            <td><input type="text" value="<?php print $form->value('db_name'); ?>" size="25" id="db_name" name="db_name"/></td>
                            <td><?php echo Kohana::lang('installer.database_name_description');?>. </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="username"><?php echo Kohana::lang('installer.username');?></label></th>
                            <td><input type="text" value="<?php print $form->value('username'); ?>" size="25" id="username" name="username"/></td>
                            <td><?php echo Kohana::lang('installer.username_description');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="pwd"><?php echo Kohana::lang('installer.password');?></label></th>
                            <td><input type="password" value="<?php print $form->value('password'); ?>" size="25" id="password" name="password"/></td>
                            <td><?php echo Kohana::lang('installer.password_description');?>.</td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="host"><?php echo Kohana::lang('installer.database_host');?></label></th>
                            <td><input type="text" value="<?php print $form->value('host') == '' ? 'localhost':$form->value('host'); ?>" size="25" id="host" name="host"/></td>
                            <td><?php echo Kohana::lang('installer.database_host_description');?>.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="table_prefix"><?php echo Kohana::lang('installer.table_prefix');?></label></th>
                            <td><input type="text" size="25" value="<?php print $form->value('table_prefix'); ?>" id="table_prefix" name="table_prefix"/></td>
                            <td><?php echo Kohana::lang('installer.table_prefix_description');?>.</td>
                        </tr>
                        <input type="hidden" name="connection" />
                        <input type="hidden" name="permission" />
                        <input type="hidden" name="load_db_tpl" />
                        <input type="hidden" name="load_htaccess_file" />
                        <input type="hidden" name="config_perm" />
                        <input type="hidden" name="htaccess_perm" />
                	</tbody>
                </table>
                <table class="form-table">
                	<tbody>
                    	<tr>
                        	<td class="next"><!--<input type="button" class="button" value="&larr; Previous" />--></td>
                            <td class="prev"><input type="submit" id="install" name="basic_db_info" value="Continue &rarr;" class="button"  /></td>
                        </tr>
                	</tbody>
                </table>
        	</form>
  </div>

</div>
</body>
</html>
