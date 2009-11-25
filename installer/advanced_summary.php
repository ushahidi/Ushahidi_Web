<?php 
    require_once('install.php');
    global $install;
    
    $header = $install->_include_html_header();
    print $header;
 ?>
<body>
<div id="ushahidi_login_container">
    <div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
    <div id="ushahidi_login" class="clearfix">
    
	<p>Before you get started, please have the following bits of information on hand:</p>
	<div class="two-col tc-left">
        <h3>Database</h3>
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
	
    
    
	
	<p><a class="button" href="index.php">&larr; Go back</a>&nbsp;&nbsp;<a class="button" href="advanced_db_info.php">Let's get started!</a></p>

        	
	</div>

</div>
</body>
</html>
