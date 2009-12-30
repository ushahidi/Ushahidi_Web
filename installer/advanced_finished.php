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
		<li class="active"><span><?php echo Kohana::lang('installer.database');?></span></li>
		<li class=""><span><?php echo Kohana::lang('installer.general');?></span></li>
		<li class=""><span><?php echo Kohana::lang('installer.mail_server');?></span></li>
		<li class=""><span><?php echo Kohana::lang('installer.map');?></span></li>
		<li class="last"><span><?php echo Kohana::lang('installer.finished');?></span></li>
	</ol>

        	<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
                <div class="feedback success">
                	<h2><?php echo Kohana::lang('installer.installation_successful');?></h2>
				</div>
		<p><?php echo Kohana::lang('installer.restart_apache');?>.</p>
                <p><?php echo Kohana::lang('installer.to_login');?> <a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"])."/admin/";?>" target="_blank">http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"])."/admin/";?></a> <?php echo Kohana::lang('installer.use_credentials');?>:<br /><br />
                <strong><?php echo Kohana::lang('installer.Username');?>:</strong> admin<br />
                <strong><?php echo Kohana::lang('installer.Password');?>:</strong> admin</p>
                <p><strong><?php echo Kohana::lang('installer.other_steps');?></strong></p>
             
                <ul>
                    <li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>" target="_blank"><?php echo Kohana::lang('installer.view_website');?></a></li>
                    <li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/reports/edit" target="_blank"><?php echo Kohana::lang('installer.upload_data');?></a></li>
                    <li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/settings/sms" target="_blank"><?php echo Kohana::lang('installer.setup_sms');?></a></li>                        
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
