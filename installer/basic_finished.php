<?php 
    require_once('install.php');
    global $install;
    
    if(!isset( $_SESSION['basic_general_settings']) && $_SESSION['basic_general_settings'] != "basic_finished"){
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
		<div class="feedback success">
        	<h2><?php echo Kohana::lang('installer.installation_successful');?></h2>
		</div>
       	<p><?php echo Kohana::lang('installer.to_login');?> <a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"])."/admin";?>" target="_blank">http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"])."/admin";?></a> <?php echo Kohana::lang('installer.use_credentials');?>:<br /><br />
        	<strong>Username:</strong> admin<br />
          	<strong>Password:</strong> admin</p>
          	<p><strong><?php echo Kohana::lang('installer.other_steps');?></strong></p>
          	<ul>
            	<li><a href="http://<?php echo $_SERVER['SERVER_NAME'].":".$_SERVER["SERVER_PORT"]."/".$install->_get_base_path($_SERVER["REQUEST_URI"]); ?>" target="_blank"><?php echo Kohana::lang('installer.view_site');?></a></li>
                <li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/reports/edit" target="_blank"><?php echo Kohana::lang('installer.upload_data');?></a></li>
               	<li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/settings" target="_blank"><?php echo Kohana::lang('settings.configure_map');?></a></li>  
               	<li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/settings/sms" target="_blank"><?php echo Kohana::lang('installer.setup_sms');?></a></li>                          
          	</ul>
               
  </div>

</div>
<?php 
	// clear all set sessions
	unset($_SESSION['basic_finished']);
	unset($_SESSION['site_name']);
	unset($_SESSION['site_tagline']);
	unset($_SESSION['select_language']);
	unset($_SESSION['site_email']);
	unset($_SESSION['basic_general_settings']);

	unset($_SESSION['username']);
	unset($_SESSION['password']);
	unset($_SESSION['host']);
	unset($_SESSION['db_name']);
	unset($_SESSION['table_prefix']); 
?>
</body>
</html>
