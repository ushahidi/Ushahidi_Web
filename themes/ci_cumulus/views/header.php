<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php
		$dont_show_css = Kohana::config("settings.site_style_css");
		$dont_show_css = $dont_show_css[0];
		echo str_ireplace('<link rel="stylesheet" type="text/css" href="'.$dont_show_css.'" />','',$header_block);
	?>
	
	<script type="text/javascript" src="<?php echo url::site(); ?>themes/ci_cumulus/js/jquery.timeago.js"></script>
	
	<?php
		// Action::header_scripts - Additional Inline Scripts from Plugins
		Event::run('ushahidi_action.header_scripts');

		if(isset($_GET['widget'])){
			echo '<link rel="stylesheet" type="text/css" href="'.url::site().'themes/ci_cumulus/css/widget/widget.css" />';
		}
	?>
</head>
<body>

<div id="header">
	<div class="content-wrapper">
			<div class="logo">
				<h1><a href="/"><?php echo $site_name; ?></a></h1>
				<h2><?php echo $site_tagline; ?></h2>
			</div>
			
			<div id="menu">
				<ul>
					<?php nav::main_tabs($this_page,array('reports','reports_submit','alerts','contact')); ?>
				</ul>
			</div>
	</div>
</div>

<!-- END HEADER -->