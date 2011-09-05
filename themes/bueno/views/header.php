<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700' rel='stylesheet' type='text/css'>
	<?php echo $header_block; ?>
		<?php
	// Action::header_scripts - Additional Inline Scripts from Plugins
	Event::run('ushahidi_action.header_scripts');
	?>
</head>

<body id="page">
  <!-- top bar-->
  <div id="top-bar">
    <!-- searchbox -->
		<div id="searchbox">
			
			<!-- languages -->
			<?php echo $languages;?>
			<!-- / languages -->

			<!-- searchform -->
			<?php echo $search; ?>
			<!-- / searchform -->
			
			<!-- user actions -->
			<div id="loggedin_user_action" class="clearingfix">
				<?php if($loggedin_username != FALSE){ ?>
					<a href="<?php echo url::site().$loggedin_role;?>"><?php echo $loggedin_username; ?></a> <a href="<?php echo url::site();?>logout/front"><?php echo Kohana::lang('ui_admin.logout');?></a>
				<?php } else { ?>
					<a href="<?php echo url::site()."members/";?>"><?php echo Kohana::lang('ui_main.login'); ?></a>
				<?php } ?>
			</div>
			<!-- / user actions -->
    </div>
  </div>
  <!-- / searchbox -->


	<!-- wrapper -->
	<div class="rapidxwpr floatholder">

		<!-- header -->
		<div id="header">
			
			<!-- logo -->
			<?php if($banner == NULL){ ?>
			<div id="logo">
				<h1><a href="<?php echo url::site();?>"><?php echo $site_name; ?></a></h1>
				<span><?php echo $site_tagline; ?></span>
			</div>
			<?php }else{ ?>
			<a href="<?php echo url::site();?>"><img src="<?php echo url::base().Kohana::config('upload.relative_directory')."/".$banner; ?>" alt="<?php echo $site_name; ?>" /></a>
			<?php } ?>
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
