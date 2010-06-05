<?php 
    require_once('install.php');
    global $install;
    
    $header = $install->_include_html_header();
    print $header;
 ?>
<body>
<div id="ushahidi_install_container">
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
							
							print ( $form->error('curl') == "" ) ? '' : 
							"<li>".$form->error('curl')."</li>";
							
							print ( $form->error('pcre') == "" ) ? '' : 
							"<li>".$form->error('pcre')."</li>";
							
							print ( $form->error('iconv') == "" ) ? '' : 
							"<li>".$form->error('iconv')."</li>";
							
							print ( $form->error('mcrypt') == "" ) ? '' : 
							"<li>".$form->error('mcrypt')."</li>";
							
							print ( $form->error('spl') == "" ) ? '' : 
							"<li>".$form->error('spl')."</li>";
	   				    ?>
					</ul>
			</div>
    	<?php } ?>
    <div class="feedback info"> 
    	<p>The files and folders listed below need to be writable by your webserver.</p>
    	<p>More information on changing file permissions can be found at the following 
			links: <a href="http://www.washington.edu/computing/unix/permissions.html" target="_blank">
			Unix/Linux</a>, <a href="http://support.microsoft.com/kb/308419\" target="_blank">Windows.</a></p>
            <ul>
                <li>application/config/config.php</li>
                <li>application/config</li>
                <li>application/cache</li>
                <li>application/logs</li>
                <li>media/uploads</li>
                <li>.htaccess</li>
            </ul>

		<p>Ushahidi will run in almost any environment with minimal configuration. The following PHP extensions are required.</p>
			<ul>
				<li><a href="http://php.net/curl" target="_blank">cURL</a> for getting or sending files using the URL syntax.</li>
				<li><a href="http://php.net/pcre" target="_blank">PCRE</a> compiled with <code>–enable-utf8</code> and <code>–enable-unicode-properties</code> for UTF-8 functions to work properly.</li>
				<li><a href="http://php.net/iconv" target="_blank">iconv</a> for UTF-8 transliteration.</li>
				<li><a href="http://php.net/mcrypt" target="_blank">mcrypt</a> for encryption.</li>
				<li><a href="http://php.net/spl" target="_blank">SPL</a> for several core libraries.</li>
			</ul>
    </div>
		
	<p>Before you get started, you will need to make sure the following files and folders are writable by your webserver. This involves changing file permissions.</p>
	<div class="two-col tc-left">
        <h3>Database <a href="http://wiki.ushahidi.com/doku.php?id=a_brief_word_on_databases" target="_blank">what's this?</a></h3>
        <ol>	
            <li>Database name</li>
            <li>Database username</li>
            <li>Database password</li>
            <li>Database host</li>
        </ol>
        
        <h3>General</h3>
        <ol>	
            <li>Site name &amp; tagline</li>
            <li>Site Email Address</li>
        </ol>
    </div>
    <div class="two-col tc-right last">
    	<h3>Mail Server</h3>
        <ol>
            <li>Site alert email address</li>
            <li>Mail Server Username</li>
            <li>Mail Server Password</li>
            <li>Mail Server Port</li>
            <li>Mail Server Host</li>
            <li>Mail Server Type</li>
        </ol>
        <h3>Map</h3>
        <ol>	
            <li>Map Provider</li>
            <li>API Key</li>
        </ol>
	</div>	
	
    
    
	
	<p><a class="button" href="index.php">&larr; Go back</a>&nbsp;&nbsp;
	<input type="submit" id="advanced_perm_pre_check" name="advanced_perm_pre_check" value="Let's get started!" class="button"  /></p>
	</div>
	</form>
</div>
</body>
</html>
