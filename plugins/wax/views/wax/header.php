<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	
	<link rel="stylesheet" type="text/css" href="http://localhost:8888/ushahidi/media/css/jquery-ui-themeroller.css" />
<link rel="stylesheet" type="text/css" href="http://localhost:8888/ushahidi/themes/default/css/style.css" />
<!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="http://localhost:8888/ushahidi/media/css/iehacks.css" />
<![endif]--><!--[if IE 7]><link rel="stylesheet" type="text/css" href="http://localhost:8888/ushahidi/media/css/ie7hacks.css" />
<![endif]--><!--[if IE 6]><link rel="stylesheet" type="text/css" href="http://localhost:8888/ushahidi/media/css/ie6hacks.css" />
<![endif]--><link rel="stylesheet" type="text/css" href="http://localhost:8888/ushahidi/media/css/openlayers.css" />
<link rel="alternate" type="application/rss+xml" href="http://localhost:8888/ushahidi/feed/" title="RSS2" /><script type="text/javascript" src="http://localhost:8888/ushahidi/media/js/OpenLayers.js"></script>
<script type="text/javascript">OpenLayers.ImgPath = 'http://localhost:8888/ushahidi/media/img/openlayers/';</script><script type="text/javascript" src="http://localhost:8888/ushahidi/media/js/jquery.js"></script>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://localhost:8888/ushahidi/media/js/jquery.pngFix.pack.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.2&amp;sensor=false"></script>
<script type="text/javascript" src="http://localhost:8888/ushahidi/media/js/selectToUISlider.jQuery.js"></script>
<script type="text/javascript" src="http://localhost:8888/ushahidi/media/js/jquery.flot.js"></script>
<script type="text/javascript" src="http://localhost:8888/ushahidi/media/js/timeline.js"></script>
<!--[if IE]><script type="text/javascript" src="http://localhost:8888/ushahidi/media/js/excanvas.min.js"></script>
<![endif]-->
	
	
	<!--
	<script type="text/javascript" src="http://localhost:8888/ushahidi/plugins/wax/media/js/modestmaps.min.js"></script>
	<script type="text/javascript" src="http://localhost:8888/ushahidi/plugins/wax/media/js/wax.mm.js"></script>
	<link rel="stylesheet" type="text/css" href="http://localhost:8888/ushahidi/plugins/wax/media/css/controls.css" />
	-->

	<script type="text/javascript" src="http://localhost:8888/ushahidi/plugins/wax/media/js/leaflet.js"></script>
	<script type="text/javascript" src="http://localhost:8888/ushahidi/plugins/wax/media/js/wax.leaf.js"></script>
	<link rel="stylesheet" type="text/css" href="http://localhost:8888/ushahidi/plugins/wax/media/css/leaflet.css" />

	
	
</head>

<body id="page">
	<!-- wrapper -->
	<div class="rapidxwpr floatholder">

		<!-- header -->
		<div id="header">

			<!-- searchbox -->
			<div id="searchbox">
				
				<!-- user actions -->
				<div id="loggedin_user_action" class="clearingfix">
					<?php if($loggedin_username != FALSE){ ?>
						<a href="<?php echo url::site().$loggedin_role;?>"><?php echo $loggedin_username; ?></a> [<a href="<?php echo url::site();?>logout/front"><?php echo Kohana::lang('ui_admin.logout');?></a>]
					<?php } else { ?>
						<a href="<?php echo url::site()."members/";?>"><?php echo Kohana::lang('ui_main.login'); ?></a>
					<?php } ?>
				</div><br/>
				<!-- / user actions -->
				
				<!-- languages -->
				<?php echo $languages;?>
				<!-- / languages -->

				<!-- searchform -->
				<?php echo $search; ?>
				<!-- / searchform -->

			</div>
			<!-- / searchbox -->
			
			<!-- logo -->
			<div id="logo">
				<h1><a href="<?php echo url::site();?>"><?php echo $site_name; ?></a></h1>
				<span><?php echo $site_tagline; ?></span>
			</div>
			<!-- / logo -->
			
			<!-- submit incident -->
			<?php echo $submit_btn; ?>
			<!-- / submit incident -->
			
		</div>
		<!-- / header -->

		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">

				<!-- mainmenu -->
				<div id="mainmenu" class="clearingfix">
					<ul>
						<?php nav::main_tabs($this_page); ?>
					</ul>

				</div>
				<!-- / mainmenu -->
