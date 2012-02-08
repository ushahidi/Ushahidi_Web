<header>
	<hgroup>
		<ul id="header_nav_left">

			<li><span class="bignext">&raquo;</span><a href="<?php echo url::site();?>"><?php echo $site_name; ?></a></li>

		</ul>
	</hgroup>
	<nav>
		<ul id="header_nav_right">
			<li class="header_nav_user header_nav_has_dropdown">
			<?php if($loggedin_user != FALSE){ ?>

				<a href="<?php echo url::site().$loggedin_role;?>"><span class="header_nav_label"><?php echo $loggedin_user->username; ?></span> <img src="<?php echo members::gravatar($loggedin_user->email,20); ?>" width="20" /></a>

				<ul class="header_nav_dropdown" style="display:none;">

					<li><a href="<?php echo url::site().$loggedin_role;?>/profile"><?php echo Kohana::lang('ui_main.manage_your_account'); ?></a></li>

					<li><a href="<?php echo url::site().$loggedin_role;?>"><?php echo Kohana::lang('ui_main.your_dashboard'); ?></a></li>

					<li><a href="<?php echo url::site();?>profile/user/<?php echo $loggedin_user->username; ?>"><?php echo Kohana::lang('ui_main.view_public_profile'); ?></a></li>

					<li><a href="<?php echo url::site();?>logout"><em><?php echo Kohana::lang('ui_admin.logout');?></em></a></li>

				</ul>

			<?php } else { ?>

				<a href="<?php echo url::site()."members/";?>" style="float:right;padding-top:8px;"><span class="header_nav_label"><strong><?php echo Kohana::lang('ui_main.login'); ?></strong></span></a>

				<ul class="header_nav_dropdown" style="display:none;">

					<form method="post" id="userpass_form" action="<?php echo url::site()."login/";?>">
					<input type="hidden" name="action" value="signin">

					<li><label for="username"><?php echo Kohana::lang('ui_main.email');?></label><input type="text" name="username" id="username" class="login_text" /></li>

					<li><label for="password"><?php echo Kohana::lang('ui_main.password');?></label><input name="password" type="password" class="login_text" id="password" size="20" /></li>

					<li><input type="submit" id="submit" name="submit" value="<?php echo Kohana::lang('ui_main.login'); ?>" class="header_nav_login_btn" /></li>
					</form>

					<li><hr/></li>

					<li><a href="<?php echo url::site()."login/?newaccount";?>"><?php echo Kohana::lang('ui_main.login_signup_click'); ?></a></li>

					<li><a href="#" id="header_nav_forgot" onclick="return false"><?php echo Kohana::lang('ui_main.forgot_password');?></a>

						<form method="post" id="header_nav_userforgot_form" action="<?php echo url::site()."login/";?>">
						<input type="hidden" name="action" value="forgot">

						<label for="resetemail"><?php echo Kohana::lang('ui_main.password_reset_prompt');?></label>
						<input type="text" id="resetemail" name="resetemail" value="" />

						<input type="submit" id="submit" name="submit" value="<?php echo Kohana::lang('ui_main.reset_password'); ?>" class="header_nav_login_btn" />

						<br/>

						</form>



					</li>

				</ul>

			<?php } ?>
			</li>
		</ul>
	</nav>
</header>