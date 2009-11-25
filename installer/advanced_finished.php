<?php 
    require_once('install.php');
    global $install;
    
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
                <p>To login to your site, go to http://[websiteurl]/admin and use the following credentials:<br /><br />
                <strong>Username:</strong> admin<br />
                <strong>Password:</strong> admin</p>
                <p><strong>Other next steps...</strong></p>
             
                <ul>
                    <li><a href="#">View your website</a></li>
                    <li><a href="#">Upload report data</a></li>
                    <li><a href="#">setup your SMS server</a></li>                        
                </ul>
           
  </div>

</div>
</body>
</html>
