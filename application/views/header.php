<?php
/**
 * Header view page.
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php
		echo html::stylesheet('media/css/style','',true);
		echo html::stylesheet('media/css/jquery-ui-themeroller', '', true);
		echo "<!--[if lte IE 7]>".html::stylesheet('media/css/iehacks','',true)."<![endif]-->";
		echo "<!--[if IE 7]>".html::stylesheet('media/css/ie7hacks','',true)."<![endif]-->";
		echo "<!--[if IE 6]>".html::stylesheet('media/css/ie6hacks','',true)."<![endif]-->";

		// Load OpenLayers before jQuery!

		if ($map_enabled == 'streetmap')
		{

			echo html::script('media/js/OpenLayers', true);
			echo html::script('media/js/OpenStreetMap.js', true);
			echo "<script type=\"text/javascript\">OpenLayers.ImgPath = '".url::site().'media/img/openlayers/'."';</script>";
		}

		// Load jQuery

		echo html::script('media/js/jquery', true);
		echo html::script('media/js/jquery.ui.min', true);

		// Other stuff to load only we have the map enabled

		if ($map_enabled)
		{
			echo $api_url . "\n";

			if ($main_page || $this_page == 'alerts')
			{
				echo html::script('media/js/selectToUISlider.jQuery', true);
			}

			if ($main_page)
			{
				echo html::script('media/js/jquery.flot', true);
				echo html::script('media/js/timeline', true);
				echo "<!--[if IE]>".
					html::script('media/js/excanvas.pack', true)
					."<![endif]-->";
			}
		}

		if ($treeview_enabled)
		{
			echo html::script('media/js/jquery.treeview');
			echo html::stylesheet('media/css/jquery.treeview');
		}

		if ($validator_enabled)
		{
			echo html::script('media/js/jquery.validate.min');
		}

		if ($photoslider_enabled)
		{
			echo html::script('media/js/picbox', true);
			echo html::stylesheet('media/css/picbox/picbox');
		}

		if( $videoslider_enabled )
		{
			echo html::script('media/js/coda-slider.pack');
			echo html::stylesheet('media/css/videoslider');
		}

		// Load ProtoChart

		if ($protochart_enabled)
		{
			echo "<script type=\"text/javascript\">jQuery.noConflict()</script>";
			echo html::script('media/js/protochart/prototype', true);
			echo '<!--[if IE]>';
			echo html::script('media/js/protochart/excanvas-compressed', true);
			echo '<![endif]-->';
			echo html::script('media/js/protochart/ProtoChart', true);
		}

		if (Kohana::config('settings.allow_feed'))
		{
			echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".url::site()."feed/\" title=\"RSS2\" />";
		}

		//Custom stylesheet

		echo html::stylesheet(url::site().'themes/'.$site_style."/style.css");
	?>

	<!--[if IE 6]>
	<script type="text/javascript" src="js/ie6pngfix.js"></script>
	<script type="text/javascript">DD_belatedPNG.fix('img, ul, ol, li, div, p, a');</script>
	<![endif]-->
	<script type="text/javascript">
		var addthis_config = {
		   ui_click: true
		}
		<?php echo $js . "\n"; ?>
	</script>
</head>

<body id="page">
	<!-- wrapper -->
	<div class="rapidxwpr floatholder">

		<!-- header -->
		<div id="header">

			<!-- searchbox -->
			<div id="searchbox">
				<a class="share addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;pub=xa-4aee423643f8276e">Share</a>

				<!-- languages -->
				<div class="language-box">
					<form>
						<?php print form::dropdown('l', $locales_array, $l, ' onChange="this.form.submit()" '); ?>
					</form>
				</div>
				<!-- / languages -->

				<!-- searchform -->
				<div class="search-form">
					<form method="get" id="search" action="<?php echo url::site() . 'search/'; ?>">
						<ul>
							<li><input type="text" name="k" value="" class="text" /></li>
							<li><input type="submit" name="b" class="searchbtn" value="search" /></li>
						</ul>
					</form>
				</div>
				<!-- / searchform -->

			</div>
			<!-- / searchbox -->

			<!-- logo -->
			<div id="logo">
				<h1><?php echo $site_name; ?></h1>
				<span><?php echo $site_tagline; ?></span>
			</div>
			<!-- / logo -->
			
			<?php
			if (Kohana::config('settings.allow_reports'))
			{
				?><!-- submit incident -->
			<div class="submit-incident clearingfix">
				<a href="<?php echo url::site() . "reports/submit" ?>"><?php echo Kohana::lang('ui_main.submit'); ?></a>
			</div>
			<!-- / submit incident --><?php
			}
			?>
		</div>
		<!-- / header -->

		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">

				<!-- mainmenu -->
				<div id="mainmenu" class="clearingfix">
					<ul>
						<li><a href="<?php echo url::site() . "main" ?>" <?php if ($this_page == 'home') echo 'class="active"'; ?>><?php echo Kohana::lang('ui_main.home'); ?></a></li>
						<li><a href="<?php echo url::site() . "reports" ?>" <?php if ($this_page == 'reports') echo 'class="active"'; ?>><?php echo Kohana::lang('ui_main.reports'); ?></a></li>
						<?php
						if (Kohana::config('settings.allow_reports'))
						{
							?><li><a href="<?php echo url::site() . "reports/submit" ?>" <?php if ($this_page == 'reports_submit') echo 'class="active"'; ?>><?php echo Kohana::lang('ui_main.submit'); ?></a></li><?php
						}
						?>
						<li><a href="<?php echo url::site() . "alerts" ?>" <?php if ($this_page == 'alerts') echo 'class="active"'; ?>><?php echo Kohana::lang('ui_main.alerts'); ?></a></li>
						<?php
						// Contact Page
						if ($site_contact_page)
						{
							?>
							<li><a href="<?php echo url::site() . "contact" ?>" <?php if ($this_page == 'contact') echo 'class="active"'; ?>><?php echo Kohana::lang('ui_main.contact'); ?></a></li>
							<?php
						}

						// Help Page
						if ($site_help_page)
						{
							?>
							<li><a href="<?php echo url::site() . "help" ?>" <?php if ($this_page == 'help') echo 'class="active"'; ?>><?php echo Kohana::lang('ui_main.help'); ?></a></li>
							<?php
						}

						// Custom Pages
						foreach ($pages as $page)
						{
							$this_active = ($this_page == 'page_'.$page->id) ? 'class="active"' : '';
							echo "<li><a href=\"".url::site()."page/index/".$page->id."\" ".$this_active.">".$page->page_tab."</a></li>";
						}
						?>
					</ul>

				</div>
				<!-- / mainmenu -->
