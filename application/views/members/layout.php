<?php 
/**
 * Layout for the members interface.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Member View
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
	// Action::header_scripts_member - Additional Inline Scripts
	Event::run('ushahidi_action.header_scripts_member');
	?>
	<script type="text/javascript" charset="utf-8">
		<?php if ($form_error): ?>
			$(document).ready(function() { $("#addedit").show(); });
		<?php endif; ?>
	</script>

	<?php echo $header_block; ?>
</head>
<body>

	<?php echo $header_nav; ?>

	<div class="holder">
		<!-- header -->
		<div id="header">

			<!-- info-nav -->
			<div class="info-nav">
				<ul>
					<li><a href="http://forums.ushahidi.com/"><?php echo Kohana::lang('ui_admin.forum');?></a></li>
				</ul>
				<div class="info-search"><?php echo form::open('members/reports', array('id' => 'info-search', 'method' => 'get')); ?><input type="text" name="k" class="info-keyword" value=""> <a href="javascript:info_search();" class="btn"><?php echo Kohana::lang('ui_admin.search');?></a><?php echo form::close(); ?></div>
				<div style="clear:both"></div>
			</div>
			<!-- title -->
			<h1><?php echo $site_name ?></h1>
			<!-- nav-holder -->
			<div class="nav-holder">
				<!-- main-nav -->
				<ul class="main-nav">
					<?php foreach($main_tabs as $page => $tab_name): ?>
						<li><a href="<?php echo url::site(); ?>members/<?php echo $page; ?>" <?php if($this_page==$page) echo 'class="active"' ;?>><?php echo $tab_name; ?></a></li>
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
				<a href="http://www.ushahidi.com" target="_blank" title="Ushahidi Engine" alt="Ushahidi Engine">
                	<sup><?php echo Kohana::config('settings.ushahidi_version');?></sup>
            	</a>
			</strong>
		</div>
	</div>
</body>
</html>
