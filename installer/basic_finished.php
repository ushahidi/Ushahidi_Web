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
		<li class=""><span>Database</span></li>
        <li class=""><span>General</span></li>
		<li class="active last"><span>Finished</span></li>
	</ol>
		<div class="feedback success">
        	<h2>Installation Successful!</h2>
		</div>
       	<p>To login, go to <a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"])."/admin";?>" target="_blank">http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"])."/admin";?></a> and use the following credentials:<br /><br />
        	<strong>Username:</strong> admin<br />
          	<strong>Password:</strong> admin</p>
          	<p><strong>Other next steps...</strong></p>
          	<ul>
            	<li><a href="http://<?php echo $_SERVER['SERVER_NAME'].":".$_SERVER["SERVER_PORT"]."/".$install->_get_base_path($_SERVER["REQUEST_URI"]); ?>" target="_blank">View your website</a></li>
                <li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/reports/edit" target="_blank">Upload report data</a></li>
               	<li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/settings" target="_blank">Configure your map</a></li>  
               	<li><a href="http://<?php echo $_SERVER['SERVER_NAME']."/".$install->_get_base_path($_SERVER["REQUEST_URI"]);?>/admin/settings/sms" target="_blank">Setup your SMS server</a></li>                          
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
