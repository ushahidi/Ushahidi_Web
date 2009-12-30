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
    
	<p><?php echo Kohana::lang('installer.index.welcome');?>:</p>
		
	<a href="basic_summary.php" class="two-col-box tc-left btn-box">
		<span class="btn-box-title"><?php echo Kohana::lang('installer.index.basic_installation');?></span>
		<span class="btn-box-content"><?php echo Kohana::lang('installer.index.basic_installation_description');?>.</span>
		<span class="last btn-action"><?php echo Kohana::lang('installer.index.proceed');?> &rarr;</span>
	</a>
	<a href="advanced_summary.php" class="two-col-box tc-right btn-box">
		<span class="btn-box-title"><?php echo Kohana::lang('installer.index.advanced_installation');?></span>
		<span class="btn-box-content"><?php echo Kohana::lang('installer.index.advanced_installation_description');?>.</span>
		<span class="last btn-action"><?php echo Kohana::lang('installer.index.proceed');?> &rarr;</span><br /><br />
	</a>
	
	<!--Generic Box
	<div class="two-col-box tc-right">
		<h2><?php echo Kohana::lang('ui_main.title');?></h2>
		<p></p>
		<p class="last"><a href="#" class="btn"><?php echo Kohana::lang('installer.index.proceed');?> &rarr;</a></p>
	</div>-->    

        	
	</div>

</div>
</body>
</html>
