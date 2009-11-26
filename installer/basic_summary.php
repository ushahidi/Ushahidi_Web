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
<div id="ushahidi_login_container">
    <div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
    <div id="ushahidi_login" class="clearfix">
    <form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
    	<?php if ($form->num_errors > 0 ) { ?>
       		<div class="feedback error"><a class="btn-close" href="#">x</a>
           		<p>Listed below is a summary of the errors we encountered:</p>
	   				<ul id="error-list">
                    	<?php
	   				    	print ( $form->error('htaccess_perm') == "" ) ? '' : 
							"<li>".$form->error('htaccess_perm')."</li>";
							
							print ( $form->error('config_folder_perm') == "" ) ? '' : 
							"<li>".$form->error('config_folder_perm')."</li>";
							
							print ( $form->error('config_file_perm') == "" ) ? '' : 
							"<li>".$form->error('config_file_perm')."</li>";
							
							print ( $form->error('cache_perm') == "" ) ? '' : 
							"<li>".$form->error('cache_perm')."</li>";
							
							print ( $form->error('logs_perm') == "" ) ? '' : 
							"<li>".$form->error('logs_perm')."</li>";
							
							print ( $form->error('uploads_perm') == "" ) ? '' : 
							"<li>".$form->error('uploads_perm')."</li>";
							
	   				    ?>
					</ul>
			</div>
    	<?php } ?>
    <div class="feedback info"> 
    	<p>The files and folders listed below needs to be writable by the webserver(777)</p>
    	<p>More information on changing file permissions can be found at the following 
			links: <a href=\"http://www.washington.edu/computing/unix/permissions.html\">
			Unix/Linux</a>, <a href=\"http://support.microsoft.com/kb/308419\">Windows.</a></p>
    </div>
		<ul>
			<li>application/config/config.php</li>
			<li>application/config</li>
			<li>application/cache</li>
			<li>application/logs</li>
			<li>media/uploads</li>
			<li>.htaccess</li>
		</ul> 
    
	<p>Before you get started, please have the following bits of information on hand.</p>	
	<h3>Database <a href="http://wiki.ushahidi.com/doku.php?id=a_brief_word_on_databases">what's this?</a></h3>
	<ol>	
		<li>Database name</li>
	    <li>Database username</li>
	    <li>Database password</li>
	    <li>Database host</li>
	    
	</ol>
		<p><a class="button" href="index.php">&larr; Go back</a>&nbsp;&nbsp;
		<input type="submit" id="basic_perm_pre_check" name="basic_perm_pre_check" value="Let's get started!" class="button"  /></p>
	</div>
	</form>
</div>
</body>
</html>
