<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>UshahidiEngine</title>
	<style type="text/css" media="all" >@import "<?php echo url::base() ?>media/css/admin/all.css";</style>
	<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="<?php echo url::base() ?>media/css/admin/ie6.css" media="screen"/><![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo url::base() ?>media/css/datepicker/ui.datepicker.css" media="screen"/>
	<script src="http://maps.google.com/maps?file=api&v=2&key=ABQIAAAAjsEM5UsvCPCIHp80spK1kBQKW7L4j6gYznY0oMkScAbKwifzxxRhJ3SP_ijydkmJpN3jX8kn5r5fEQ" type="text/javascript"></script>
	<?php echo html::script('media/js/jquery'); ?>
	<?php echo html::script('media/js/jquery.form'); ?>
	<?php echo html::script('media/js/ui.datepicker'); ?>
	<?php echo html::script('media/js/extra'); ?>
	<?php echo html::script('media/js/ui.highlightfade'); ?>
	<?php echo html::script('media/js/mapstraction'); ?>
	<?php echo html::script('media/js/mapstraction-geocode'); ?>
	<?php echo html::script('media/js/mapstraction-route'); ?>
</head>
<body>
	<div class="holder">
		<!-- header -->
		<div id="header">
			<!-- top-area -->
			<div class="top">
				<strong>Ushahidi Engine v1.0</strong>
				<ul>
					<li class="none-separator">Welcome, <?php echo $admin_name; ?>!</li>
					<li class="none-separator"><a href="#">Admin</a></li>
					<li><a href="log_out">Logout</a></li>
				</ul>
			</div>
			<!-- info-nav -->
			<div class="info-nav">
				<h3>Get help</h3>
				<ul>
					<li ><a href="#">Wiki</a></li>
					<li><a href="#">FAQ’s</a></li>
					<li><a href="#">Forum</a></li>
				</ul>
			</div>
			<!-- title -->
			<h1><?php echo $site_name ?></h1>
			<!-- nav-holder -->
			<div class="nav-holder">
				<!-- main-nav -->
				<ul class="main-nav">
					<li><a href="<?php echo url::base() ?>admin/dashboard" <?php if($this_page=="dashboard") echo "class=\"active\"" ;?>>Dashboard </a></li>
					<li><a href="<?php echo url::base() ?>admin/reports" <?php if($this_page=="reports") echo "class=\"active\"" ;?>>Reports</a></li>
					<li><a href="<?php echo url::base() ?>admin/manage" <?php if($this_page=="manage") echo "class=\"active\"" ;?>>Manage</a></li>
				</ul>
				<!-- sub-nav -->
				<ul class="sub-nav">
					<li><a href="#">Settings</a></li>
					<li><a href="#">Users</a></li>
					<li><a href="#">Plugins</a></li>
				</ul>
			</div>
		</div>
		<!-- content -->
		<div id="content">
			<div class="bg">
				<?php print $content; ?>
			</div>
		</div>
	</div>
	<div id="footer">
		<div class="holder"><strong><a href="http://www.ushahidi.com" target="_blank" title="Ushahidi Engine" alt="Ushahidi Engine">Ushahidi</a></strong></div>
	</div>
</body>
</html>