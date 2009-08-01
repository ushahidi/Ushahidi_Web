<?php 
/**
 * Layout for the admin interface.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<title>UshahidiEngine</title>
	<style type="text/css" media="all" >@import "<?php echo url::base() ?>index.php/media/css/admin/all.css";</style>
	<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="<?php echo url::base() ?>index.php/media/css/admin/ie6.css" media="screen"/><![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo url::base() ?>index.php/media/css/datepicker/ui.datepicker.css" media="screen"/>
	<?php
	
	// Load OpenLayers
	if ($map_enabled)
	{
		echo html::script('index.php/media/js/OpenLayers/OpenLayers');
		echo $api_url . "\n";
	}
	
	// Load jQuery
	echo html::script('index.php/media/js/jquery');
	echo html::script('index.php/media/js/jquery.form');
	echo html::script('index.php/media/js/jquery.validate.min');
	echo html::script('index.php/media/js/jquery.ui.min');
	
	// Load Flot
	if ($flot_enabled)
	{
		echo html::script('index.php/media/js/jquery.flot');
		echo html::script('index.php/media/js/excanvas.pack');
		echo html::script('index.php/media/js/timeline.js');
	}
	
	// Load ColorPicker
	if ($colorpicker_enabled)
	{
		echo html::stylesheet('index.php/media/css/colorpicker');
		echo html::script('index.php/media/js/colorpicker');
	}
	?>
	<script type="text/javascript" charset="utf-8">
		<?php echo $js . "\n"; ?>
	</script>
</head>
<body>
	<div class="holder">
		<!-- header -->
		<div id="header">
			<!-- top-area -->
			<div class="top">
				<strong><?php echo Kohana::lang('layout.version')?></strong>
				<ul>
					<li class="none-separator"> <?php echo Kohana::lang('layout.welcome');echo $admin_name; ?>!</li>
					<li class="none-separator"><a href="<?php echo url::base() ?>" title="View the home page">
						<?php echo Kohana::lang('layout.view_site');?></a>					
					<li class="none-separator"><a href="#"><?php echo Kohana::lang('layout.my_profile');?></a></li>
					<li><a href="log_out"><?php echo Kohana::lang('layout.logout');?></a></li>
				</ul>
			</div>
			<!-- info-nav -->
			<div class="info-nav">
				<h3><?php echo Kohana::lang('layout.get_help');?></h3>
				<ul>
					<li ><a href="http://wiki.ushahididev.com/"><?php echo Kohana::lang('layout.wiki');?></a></li>
					<li><a href="http://wiki.ushahididev.com/doku.php?id=how_to_use_ushahidi_alpha"><?php echo Kohana::lang('layout.faqs');?></a></li>
					<li><a href="http://forums.ushahidi.com/"><?php echo Kohana::lang('layout.forum');?></a></li>
				</ul>
			</div>
			<!-- title -->
			<h1><?php echo $site_name ?></h1>
			<!-- nav-holder -->
			<div class="nav-holder">
				<!-- main-nav -->
				<ul class="main-nav">
					<li><a href="<?php echo url::base() ?>admin/dashboard" <?php if($this_page=="dashboard") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('layout.dashboard');?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/reports" <?php if($this_page=="reports") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('layout.reports');?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/comments" <?php if($this_page=="comments") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('layout.comments');?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/messages" <?php if($this_page=="messages") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('layout.messages');?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/feedback" <?php if($this_page=="feedback") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('layout.feedback')?>
						</a></li>
				</ul>
				<!-- sub-nav -->
				<ul class="sub-nav">
					<?php if ($this->auth->logged_in('admin')){ ?>
					<li><a href="<?php echo url::base() ?>admin/settings/site"><?php echo Kohana::lang('layout.settings');?></a></li>
					<li><a href="<?php echo url::base() ?>admin/manage"><?php echo Kohana::lang('layout.manage');?></a></li>
					<li><a href="<?php echo url::base() ?>admin/users"><?php echo Kohana::lang('layout.users');?></a></li>
					<?php 
					} ?>
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
		<div class="holder">
			<strong>
				<a href="http://www.ushahidi.com" target="_blank" title="Ushahidi Engine" alt="Ushahidi Engine">
					<?php echo Kohana::lang('layout.ushahidi');?>
				</a>
			</strong>
		</div>
	</div>
</body>
</html>
