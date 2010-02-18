<?php 
    require_once('install.php');
    global $install;
    
    if(!isset( $_SESSION['advanced_finished']) && $_SESSION['advanced_finished'] != "advanced_map"){
    	header('Location:advanced_mail_server.php');
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
		<li class=""><span>Mail Server</span></li>
		<li class=""><span>Map</span></li>
		<li class="active last"><span>Finished</span></li>
	</ol>

        	<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
                <div class="feedback success">
                	<h2>Installation Successful!</h2>
				</div>
		<p>Please restart your Apache Server.</p>
                <p>To login, go to <a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"])."/admin/";?>" target="_blank">http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"])."/admin/";?></a> and use the following credentials:<br /><br />
                <strong>Username:</strong> admin<br />
                <strong>Password:</strong> admin</p>
                <p><strong>Other next steps...</strong></p>
             
                <ul>
                    <li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>" target="_blank">View your website</a></li>
                    <li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/reports/edit" target="_blank">Upload report data</a></li>
                    <li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/settings/sms" target="_blank">Setup your SMS server</a></li>                        
                </ul>
           
  </div>
</div>
<?php
	// clear all set sessions
	unset($_SESSION['advanced_mail_server_settings']);
	// send the database info to the next page for updating the settings table.
	unset($_SESSION['select_map_provider']);
	unset($_SESSION['map_provider_api_key']);
	
	unset($_SESSION['site_alert_email']);
	unset($_SESSION['mail_server_username']);
	unset($_SESSION['mail_server_pwd']);
	unset($_SESSION['mail_server_port']);
	unset($_SESSION['mail_server_host']);
	unset($_SESSION['select_mail_server_type']);
	unset($_SESSION['select_mail_server_ssl']);
	unset($_SESSION['map_settings']);
	
	unset($_SESSION['mail_server']);
	    	
	// set it up in case someone want to goes the previous page.
	unset($_SESSION['site_name']);
	unset($_SESSION['site_tagline']);
	unset($_SESSION['select_language']);
	unset($_SESSION['site_email']);
	
	unset($_SESSION['general_settings']);
	    	
	// send the database info to the next page for updating the settings table.
	unset($_SESSION['username']);
	unset($_SESSION['password']);
	unset($_SESSION['host']);
	unset($_SESSION['db_name']);
	unset($_SESSION['table_prefix']); 
	          
?>
</body>
</html>
