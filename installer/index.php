<?php 
    require_once('install.php');
    global $install;
    
    //check if ushahidi is installed?.
    if( $install->is_ushahidi_installed())
    {
        header('Location:../');
    }
   
    $header = $install->_include_html_header();
    print $header;
 ?>
<body>
<div id="ushahidi_login_container">
    <div id="ushahidi_login_logo"><img src="../media/img/admin/logo_login.gif" /></div>
    <div id="ushahidi_login" class="clearfix">
    
	<p>Welcome to the Ushahidi server install process.  Choose which type of installation you would like to use below:</p>
		
	<a href="basic_summary.php" class="two-col-box tc-left btn-box">
		<span class="btn-box-title">BASIC INSTALLATION</span>
		<span class="btn-box-content">Simple and fast.  All you need is your website's root directory and your database information.  Choose this option if you want to get up and running quickly, and you can always configure everything else later.</span>
		<span class="last btn-action">Proceed with basic &rarr;</span>
	</a>
	<a href="advanced_summary.php" class="two-col-box tc-right btn-box">
		<span class="btn-box-title">ADVANCED INSTALLATION</span>
		<span class="btn-box-content">Get all the basic settings completed through this 5-step process.  This includes server, map, site name and contact details.</span>
		<span class="last btn-action">Proceed with advanced &rarr;</span><br /><br />
	</a>
	
	<!--Generic Box
	<div class="two-col-box tc-right">
		<h2>Title</h2>
		<p></p>
		<p class="last"><a href="#" class="btn">Proceed &rarr;</a></p>
	</div>-->    

        	
	</div>

</div>
</body>
</html>
