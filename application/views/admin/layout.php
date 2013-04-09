<?php
/**
 * Layout for the admin interface.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<title><?php echo html::specialchars($site_name) ?></title>
	<?php
	// Action::header_scripts_admin - Additional Inline Scripts
	Event::run('ushahidi_action.header_scripts_admin');
	?>
	<script type="text/javascript" charset="utf-8">
		<?php if ($form_error): ?>
			$(document).ready(function() { $("#addedit").show(); });
		<?php endif; ?>
	</script>

	<?php echo $header_block; ?>
</head>
<body>

	<?php
		echo $header_nav;

		// Action::admin_header_top_left - Admin Header Menu
		Event::run('ushahidi_action.admin_header_top_left');

		// Action::admin_secondary_header_bar - Admin Secondary Menu
		Event::run('ushahidi_action.admin_secondary_header_bar');
	?>

	<div class="holder">
		<!-- header -->
		<div id="header">

			<!-- info-nav -->
			<div class="info-nav">
				<h3><?php echo Kohana::lang('ui_admin.get_help');?></h3>
				<ul>
					<li ><a href="http://wiki.ushahidi.com/"><?php echo Kohana::lang('ui_admin.wiki');?></a></li>
					<li><a href="http://ushahidi.com/community_resources/"><?php echo Kohana::lang('ui_admin.faqs');?></a></li>
					<li><a href="http://forums.ushahidi.com/"><?php echo Kohana::lang('ui_admin.forum');?></a></li>
				</ul>
				
				<!-- languages -->
				<?php echo $languages; ?>
				<!-- / languages -->
				<div class="info-search">
					<?php echo form::open('admin/reports', array('method' => 'get', 'id' => 'info-search')); ?>
					<input type="text" name="k" class="info-keyword" value=""> 
					<a href="javascript:info_search();" class="btn">
						<?php echo Kohana::lang('ui_admin.search_reports');?>
					</a>
					<?php echo form::close(); ?>
				</div>
				<div style="clear:both;"></div>
				<div class="info-buttons">
					<a class="button" href="<?php echo url::site().'admin/manage/publiclisting'; ?>">
						<?php echo Kohana::lang('ui_admin.manage_public_listing'); ?>
					</a>
				</div>
			</div>
			<!-- title -->
			<h1><?php echo $site_name ?></h1>
			<!-- nav-holder -->
			<div class="nav-holder">
				<!-- main-nav -->
				<ul class="main-nav">
					<?php foreach ($main_tabs as $page => $tab_name): ?>
						<li>
							<a href="<?php echo url::site(); ?>admin/<?php echo $page; ?>" <?php if($this_page==$page) echo 'class="active"' ;?>>
								<?php echo $tab_name; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<!-- sub-nav -->
				<ul class="sub-nav">
					<?php foreach ($main_right_tabs as $page => $tab_name): ?>
						<li>
							<a href="<?php echo url::site(); ?>admin/<?php echo $page; ?>" <?php if($this_page==$page) echo 'class="active"' ;?>>
								<?php echo $tab_name; ?>
							</a>
						</li>
					<?php endforeach; ?>
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
				<a href="http://www.ushahidi.com" target="_blank" title="Ushahidi Platform" alt="Ushahidi Platform">
                	<sup><?php echo Kohana::config('settings.ushahidi_version');?></sup>
            	</a>
			</strong>
		</div>
	</div>
<?php echo $footer_block; ?>
</body>
</html>
