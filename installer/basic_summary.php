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
    
	<p>Before you get started, please have the following bits of information on hand.</p>
	
		
	<h3>Database <a href="http://wiki.ushahidi.com/doku.php?id=a_brief_word_on_databases">what's this?</a></h3>
	<ol>	
		<li>Database name</li>
	    <li>Database username</li>
	    <li>Database password</li>
	    <li>Database host</li>
	</ol>
	<p><a class="button" href="index.php">&larr; Go back</a>&nbsp;&nbsp;<a class="button" href="basic_db_info.php">Let's get started!</a></p>

        	
	</div>

</div>
</body>
</html>
