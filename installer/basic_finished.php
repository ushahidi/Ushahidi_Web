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
		<li class="active last"><span>Finished</span></li>
	</ol>

        	<form method="POST" name="frm_install" action="process.php" style="line-height: 100%; margin-top: 0; margin-bottom: 0;">  
        		<!--<div class="feedback error">
                	<p>Listed below is a summary of the errors we encountered:</p>
	   				<ul id="error-list">
                    	<li>Please make sure your <strong>Site Email Address</strong> is a valid email address.</li>
                        <li>Please make sure your <strong>Site Alert Email Address</strong> is a valid email address.</li>
                        <li>Please enter a <strong>Mail Server Username</strong>.</li>
                        <li>Please make sure your <strong>Mail Server Password</strong>.</li>
                        <li>Please enter a <strong>Mail Server Port</strong>.</li>
                        <li>Please make sure your <strong>Mail Server Host</strong>.</li>
					</ul>
				</div>-->
                
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
                    <li><a href="#">Configure your map</a></li>
                    <li><a href="#">setup your SMS server</a></li>                        
                </ul>
               
  </div>

</div>
</body>
</html>
