<?php 
    require_once('install.php');
    global $install;
    
    if(!isset( $_SESSION['advanced_db_info']) && $_SESSION['advanced_db_info'] != "advanced_summary"){
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
		<li class="active"><span>Database</span></li>
		<li class=""><span>General</span></li>
		<li class=""><span>Mail Server</span></li>
		<li class=""><span>Map</span></li>
		<li class="last"><span>Finished</span></li>
	</ol>

        	<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
        		<?php if ($form->num_errors > 0 ) { ?>
        		<div class="feedback error"><a class="btn-close" href="#">x</a>
                	<p>Listed below is a summary of the errors we encountered:</p>
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
							
							print ( $form->error('load_db_tpl') == "" ) ? '' : 
							"<li>".$form->error('load_db_tpl')."</li>";
							
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
				<table class="form-table fields">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="base_path">Base Path</label></th>
                            <td><input type="text" value="<?php print $form->value('base_path') == "" ? $install->_get_base_path($_SERVER["REQUEST_URI"]) : $form->value('base_path'); ?>" size="25" id="base_path" name="base_path"/></td>
                            <td>The location on your server where you placed your Ushahidi files. <strong>We have automatically detected this value, please make sure that it is correct.</strong> If the field is empty, do not worry, it means ushahidi is installed at the top level directory.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="db_name">Database Name</label></th>
                            <td><input type="text" value="<?php print $form->value('db_name') == "" ? $_SESSION['db_name'] : $form->value('db_name'); ?>" size="25" id="db_name" name="db_name"/></td>
                            <td>The name of the database you want to run Ushahidi in. </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="username">User Name</label></th>
                            <td><input type="text" value="<?php print $form->value('username') == "" ? $_SESSION['username'] : $form->value('username'); ?>" size="25" id="username" name="username"/></td>
                            <td>Your database username.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="pwd">Password</label></th>
                            <td><input type="password" value="<?php print $form->value('password') == "" ? $_SESSION['password'] : $form->value('password'); ?>" size="25" id="password" name="password"/></td>
                            <td>Your database password.</td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="host">Database Host</label></th>
                            <td><input type="text" value="<?php print $form->value('host') == "" ? $_SESSION['host'] : $form->value('host'); ?>" size="25" id="host" name="host"/></td>
                            <td>If you are running Ushahidi on your own computer, this will more than likely be "localhost". If you are running Ushahidi from a web server, you'll get your host information from your web hosting provider.</td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="table_prefix">Table Prefix</label></th>
                            <td><input type="text" size="25" value="<?php print $form->value('table_prefix') == "" ? $_SESSION['table_prefix'] : $form->value('table_prefix'); ?>" id="table_prefix" name="table_prefix"/></td>
                            <td>Normally you won't change the table prefix.  However, If you want to run multiple Ushahidi installations from a single database you can do that by changing the prefix here.</td>
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
                            <td class="prev"><!--<input type="button" class="button" value="Continue &rarr;" value="submit"  />-->
                      		<input type="submit" id="install" name="advanced_db_info" value="Continue &rarr;" class="button"  /></td>
                        </tr>
                	</tbody>
                </table>
        	</form>
  </div>

</div>
</body>
</html>
