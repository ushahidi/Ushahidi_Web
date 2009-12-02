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
	<title><?php echo $site_name ?></title>
	<?php
	echo html::stylesheet('media/css/admin/all', '', true);
	echo html::stylesheet('media/css/jquery-ui-themeroller', '', true);
	echo "<!--[if lt IE 7]>".
		html::stylesheet('media/css/ie6', '', true)
		."<![endif]-->";
	
	// Load OpenLayers
	if ($map_enabled)
	{
		echo html::script('media/js/OpenLayers', true);
		echo $api_url . "\n";
		echo "<script type=\"text/javascript\">
			OpenLayers.ImgPath = '".url::base().'media/img/openlayers/'."';
			</script>";
	}
	
	// Load jQuery
	echo html::script('media/js/jquery', true);
	echo html::script('media/js/jquery.form', true);
	echo html::script('media/js/jquery.validate.min', true);
	echo html::script('media/js/jquery.ui.min', true);
	echo html::script('media/js/selectToUISlider.jQuery', true);
	echo html::script('media/js/jquery.hovertip-1.0', true);
	echo html::stylesheet('media/css/jquery.hovertip-1.0', '', true);
	echo "<script type=\"text/javascript\">
		$(function() {
			if($('.tooltip[title]') != null)
			$('.tooltip[title]').hovertip();
		});
	</script>";
	
	// Load Flot
	if ($flot_enabled)
	{
		echo html::script('media/js/jquery.flot', true);
		echo html::script('media/js/excanvas.pack', true);
		echo html::script('media/js/timeline.js', true);
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
	
	// Load Raphael
	if($raphael_enabled)
	{
		// The only reason we include prototype is to keep the div element naming convention consistent
		//echo html::script('media/js/protochart/prototype', true);
		echo html::script('media/js/raphael', true);
		echo '<script type="text/javascript" charset="utf-8">';
		echo 'var impact_json = { '.$impact_json .' };';
		echo '</script>';
		echo html::script('media/js/raphael-ushahidi-impact', true);
	}
	
	// Load ColorPicker
	if ($colorpicker_enabled)
	{
		echo html::stylesheet('media/css/colorpicker', '', true);
		echo html::script('media/js/colorpicker', true);
	}
	
	// Load TinyMCE
	if ($editor_enabled)
	{
		echo html::script('media/js/tinymce/tiny_mce', true);
	}
	?>
	<script type="text/javascript" charset="utf-8">
		<?php echo $js . "\n"; ?>
		function info_search(){
			$("#info-search").submit();
		}
	</script>
</head>
<body>
	<div class="holder">
		<!-- header -->
		<div id="header">
			<!-- top-area -->
			<div class="top">
				<strong><?php echo Kohana::lang('ui_admin.version')?></strong>
				<ul>
					<li class="none-separator"> <?php echo Kohana::lang('ui_admin.welcome');echo $admin_name; ?>!</li>
					<li class="none-separator"><a href="<?php echo url::base() ?>" title="View the home page">
						<?php echo Kohana::lang('ui_admin.view_site');?></a>					
					<li class="none-separator"><a href="<?php echo url::base()."admin/profile/" ?>"><?php echo Kohana::lang('ui_admin.my_profile');?></a></li>
					<li><a href="log_out"><?php echo Kohana::lang('ui_admin.logout');?></a></li>
				</ul>
                        </div>
                        <?php if( ( $version != "" ) && ( url::current() != "admin/upgrade" ) ) { ?>
                        <div id="update-info">
                        Ushahidi <?php echo $version; ?> 
                            <?php echo Kohana::lang('ui_admin.version_available');?> 
        <a href="<?php echo url::base() ?>admin/upgrade" title="upgrade ushahidi">
                             <?php echo Kohana::lang('ui_admin.update_link');?>
                            </a>.
                        </div>
                        <?php } ?>

			<!-- info-nav -->
			<div class="info-nav">
				<h3><?php echo Kohana::lang('ui_admin.get_help');?></h3>
				<ul>
					<li ><a href="http://wiki.ushahididev.com/"><?php echo Kohana::lang('ui_admin.wiki');?></a></li>
					<li><a href="http://wiki.ushahididev.com/doku.php?id=how_to_use_ushahidi_alpha"><?php echo Kohana::lang('ui_admin.faqs');?></a></li>
					<li><a href="http://forums.ushahidi.com/"><?php echo Kohana::lang('ui_admin.forum');?></a></li>
				</ul>
				<div class="info-search"><form action="<?php echo url::base() ?>admin/reports" id="info-search"><input type="text" name="k" class="info-keyword" value=""> <a href="javascript:info_search();" class="btn">Search</a></form></div>
				<div style="clear:both"></div>
			</div>
			<!-- title -->
			<h1><?php echo $site_name ?></h1>
			<!-- nav-holder -->
			<div class="nav-holder">
				<!-- main-nav -->
				<ul class="main-nav">
					<li><a href="<?php echo url::base() ?>admin/dashboard" <?php if($this_page=="dashboard") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('ui_admin.dashboard');?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/reports" <?php if($this_page=="reports") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('ui_admin.reports');?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/comments" <?php if($this_page=="comments") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('ui_admin.comments');?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/messages" <?php if($this_page=="messages") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('ui_admin.messages');?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/feedback" <?php if($this_page=="feedback") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('ui_admin.feedback')?>
						</a></li>
					<li><a href="<?php echo url::base() ?>admin/stats" <?php if($this_page=="stats") echo "class=\"active\"" ;?>>
						<?php echo Kohana::lang('ui_admin.stats')?>
						</a></li>
				</ul>
				<!-- sub-nav -->
				<ul class="sub-nav">
					<?php if ($this->auth->logged_in('superadmin')){ ?>
						<li><a href="<?php echo url::base() ?>admin/settings/site"><?php echo Kohana::lang('ui_admin.settings');?></a></li>
						<li><a href="<?php echo url::base() ?>admin/manage"><?php echo Kohana::lang('ui_admin.manage');?></a></li>
						<li><a href="<?php echo url::base() ?>admin/users"><?php echo Kohana::lang('ui_admin.users');?></a></li>
					<?php 
					} else if($this->auth->logged_in('admin')) { ?>
						<li><a href="<?php echo url::base() ?>admin/manage"><?php echo Kohana::lang('ui_admin.manage');?></a></li>
						<li><a href="<?php echo url::base() ?>admin/users"><?php echo Kohana::lang('ui_admin.users');?></a></li> 
					<?php
					} else { ?> 
					
					<?php 
					} 
					?>
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
                	<sup><?php echo Kohana::config('version.ushahidi_version');?></sup>
            	</a>
			</strong>
		</div>
	</div>
</body>
</html>
