<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $site_name; ?></title>
	<style media="all" type="text/css">@import "<?php echo url::base() ?>media/css/all.css";</style>
	<style media="all" type="text/css">@import "<?php echo url::base() ?>media/css/photoslider.css";</style>
	<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="<?php echo url::base() ?>/media/css/ie6.css" media="screen"/><![endif]-->
	
	<?php echo html::script('media/js/jquery'); ?>
	<?php echo html::script('media/js/jquery.validate.min'); ?>
	<?php echo html::script('media/js/jquery.ui.min'); ?>
	<?php
	if ($map_enabled)
	{
		echo html::script('media/js/OpenLayers/OpenLayers');
		echo html::script('media/js/OpenLayers/LoadingPanel');
		echo $api_url . "\n";
		if ($main_page) {
			echo html::script('media/js/accessibleUISlider.jQuery');
			echo html::script('media/js/jquery.flot');
			?>
			<!--[if IE]><script language="javascript" type="text/javascript" src="<?php echo url::base() ?>/media/js/excanvas.pack.js"></script><![endif]-->
			<?php
			echo html::stylesheet('media/css/jquery-ui-themeroller');
		}
	}
	if ($datepicker_enabled)
	{
		echo html::stylesheet('media/css/datepicker/ui.datepicker');
	}
	echo html::script('media/js/photoslider.js');
	?>
	<script type="text/javascript">
		<?php echo $js . "\n"; ?>
	</script>
</head>
<body>
	<div id="main">
		<!-- start header block -->
		<div id="header">
			<div class="header-info">
				<strong><a href ="<?php echo url::base(); ?>"><?php echo $site_name; ?></a></strong>
				<p>A brief description of the project can go here.</p>
			</div>
			<ul>
				<li class="first"><a <?php if ($this_page == 'home') echo 'class="active"'; ?> href="<?php echo url::base() . "main" ?>">Home</a></li>
				<li><a <?php if ($this_page == 'reports') echo 'class="active"'; ?> href="<?php echo url::base() . "reports/submit" ?>">Report an Incident</a></li>
				<li><a <?php if ($this_page == 'alerts') echo 'class="active"'; ?> href="<?php echo url::base() . "alerts" ?>">Get Alerts</a></li>
				<li class="last"><a <?php if ($this_page == 'help') echo 'class="active"'; ?> href="<?php echo url::base() . "help" ?>">How to Help</a></li>
			</ul>
		</div>
		<!-- end header block <> start content block -->
