<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Kohana::lang('ui_main.login');?></title>
<?php
echo html::stylesheet(url::file_loc('css').'media/css/jquery-ui-themeroller', '', TRUE);
echo html::stylesheet(url::file_loc('css').'media/css/login', '', TRUE);
echo html::stylesheet(url::file_loc('css').'media/css/openid', '', TRUE);
echo html::stylesheet(url::file_loc('css').'media/css/global', '', TRUE);
echo html::script(url::file_loc('js').'media/js/jquery', TRUE);
echo html::script(url::file_loc('js').'media/js/openid/openid-jquery', TRUE);
echo html::script(url::file_loc('js').'media/js/openid/openid-jquery-en', TRUE);
echo html::script(url::file_loc('js').'media/js/global', TRUE);
?>
<script type="text/javascript">
	<?php echo $js; ?>
</script>
</head>

<body>

<?php echo $header_nav; ?>

<div id="openid_login_container">

	<div id="ushahidi_site_name" class="ui-corner-all">
    	<div id="logo">
			<h1><?php echo $site_name; ?></h1>
			<span><?php echo $site_tagline; ?></span>
		</div>
    </div>

    <?php if ($message): ?>
		<div class="<?php echo $message_class; ?> ui-corner-all">&#8226;&nbsp;<?php echo $message; ?></div>
	<?php endif; ?>

	<?php if ($form_error): ?>
		<div class="login_error ui-corner-all">
		<?php foreach ($errors as $error_item => $error_description): ?>
			<?php echo (!$error_description) ? '' : "&#8226;&nbsp;" . $error_description . "<br />"; ?>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if (isset($_GET["reset"])): ?>
	<div id="password_reset_change_form" class="ui-corner-all">
		<h2><?php echo Kohana::lang('ui_main.create_new_password'); ?></h2>
		<?php echo form::open(NULL, array('id' => "changepass_form")); ?>
			<input type="hidden" name="action" value="changepass">
			<input type="hidden" name="changeid" value="<?php echo $changeid; ?>">

			<table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
				<?php
					$hidden = 'hidden';
					if (empty($token)) { $hidden = ''; }
				?>
				<tr class="<?php echo $hidden; ?>">
					<td><strong><?php echo Kohana::lang('ui_main.token');?>:</strong><br />
					<?php echo form::input('token', $token, 'class="login_text new_email"'); ?></td>
				</tr>
				<tr>
					<td><strong><?php echo Kohana::lang('ui_main.password');?>:</strong><br />
					<?php echo form::password('password', $form['password'], 'class="login_text new_password"'); ?></td>
				</tr>
				<tr>
					<td><strong><?php echo Kohana::lang('ui_main.password_again');?>:</strong><br />
					<?php echo form::password('password_again', $form['password_again'], 'class="login_text new_password_again"'); ?></td>
				</tr>
				<tr>
					<td><input type="submit" id="submit" name="submit" value="<?php echo Kohana::lang('ui_main.change_password'); ?>" class="login_btn" /></td>
				</tr>
			</table>

		<?php echo form::close(); ?>
	</div>
	<?php endif; ?>

	<div id="openid_login" class="ui-corner-all">

		<?php if ($new_confirm_email_form): ?>
			<h2><?php echo Kohana::lang('ui_main.resend_confirm_email'); ?>:</h2>
			<div id="resend_confirm_email" class="signin_select ui-corner-all" style="margin-top:10px;">
				<?php echo form::open(NULL, array('id'=>"resendconfirm_form")); ?>
					<input type="hidden" name="action" value="resend_confirmation">
					<table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
						<tr>
							<td><strong><?php echo Kohana::lang('ui_main.registered_email');?></strong><br />
							<?php print form::input('confirmation_email', $form['confirmation_email'], ' class="login_text"'); ?></td>
						</tr>
						<tr>
							<td><input type="submit" id="submit" name="submit" value="<?php echo Kohana::lang('ui_main.send_confirmation'); ?>" class="login_btn" /></td>
						</tr>
					</table>
				<?php echo form::close(); ?>
			</div>
		<?php endif; ?>

		<h2><?php echo Kohana::lang('ui_main.login_with'); ?>:</h2>

		<h2><a href="javascript:toggle('signin_userpass');"><?php echo Kohana::lang('ui_main.login_userpass'); ?></a></h2>
		<div id="signin_userpass" class="signin_select ui-corner-all">
			<?php echo form::open(NULL, array('id'=>"userpass_form")); ?>
				<input type="hidden" name="action" value="signin">
				<table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.email');?>:</strong><br />
						<input type="text" name="username" id="username" class="login_text" /></td>
					</tr>
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.password');?>:</strong><br />
						<input name="password" type="password" class="login_text" id="password" size="20" /></td>
					</tr>
					<tr>
						<td><input type="checkbox" id="remember" name="remember" value="1" checked="checked" /><?php echo Kohana::lang('ui_main.password_save');?></td>
					</tr>
					<tr>
						<td><input type="submit" id="submit" name="submit" value="<?php echo Kohana::lang('ui_main.login'); ?>" class="login_btn" /></td>
					</tr>
					
					<tr>
						<td><a href="javascript:toggle('signin_forgot');"> <?php echo Kohana::lang('ui_main.forgot_password');?></a></td>
					</tr>

				</table>
			<?php echo form::close(); ?>
		</div>
		
		<div id="signin_forgot" class="signin_select ui-corner-all" style="margin-top:10px;">
			<?php echo form::open(NULL, array('id'=>"userforgot_form")); ?>
				<input type="hidden" name="action" value="forgot">
				<table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.registered_email');?></strong><br />
						<?php print form::input('resetemail', $form['resetemail'], ' class="login_text"'); ?></td>
					</tr>
					<tr>
						<td><input type="submit" id="submit" name="submit" value="<?php echo Kohana::lang('ui_main.reset_password'); ?>" class="login_btn" /></td>
					</tr>
				</table>
			<?php echo form::close() ?>
		</div>

		<?php if (kohana::config('config.allow_openid') == TRUE): ?>
		<h2><a href="javascript:toggle('signin_openid');"><?php echo Kohana::lang('ui_main.login_openid'); ?></a></h2>
		<div id="signin_openid" class="signin_select ui-corner-all">
			<?php echo form::open(NULL, array('id'=>"openid_form")); ?>
				<input type="hidden" name="action" value="openid">
				<div id="openid_choice">
					<p><?php echo Kohana::lang('ui_main.login_select_openid'); ?>:</p>
					<div id="openid_btns"></div>
				</div>

				<div id="openid_input_area">
					<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
					<input id="openid_submit" type="submit" value="Sign-In"/>
				</div>
				<noscript>
					<p>OpenID is service that allows you to log-on to many different websites using a single indentity.
					Find out <a href="http://openid.net/what/">more about OpenID</a> and <a href="http://openid.net/get/">how to get an OpenID enabled account</a>.</p>
				</noscript>
			<?php echo form::close(); ?>
		</div>
		<?php endif; ?>
	</div>

	<div id="create_account" class="ui-corner-all">

		<h2><a href="javascript:toggle('signin_new');"><?php echo Kohana::lang('ui_main.login_signup_click'); ?></a></h2>

		<?php echo Kohana::lang('ui_main.login_signup_text'); ?>
		<div id="signin_new" class="signin_select ui-corner-all" style="margin-top:10px;">
			<?php echo form::open(NULL,  array('id' => "usernew_form")); ?>
				<input type="hidden" name="action" value="new">
				<table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.name'); ?>:</strong><br/><small><?php echo Kohana::lang('ui_main.identify_you');?></small><br />
						<?php print form::input('name', $form['name'], 'class="login_text new_name"'); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.email'); ?>:</strong><br />
						<?php print form::input('email', $form['email'], 'class="login_text new_email"'); ?></td>
					</tr>
					<tr class="riverid_email_already_set" style="display:none;">
						<td class="riverid_email_already_set_copy"></td>
					</tr>
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.password'); ?>:</strong><br />
						<?php print form::password('password', $form['password'], 'class="login_text new_password"'); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.password_again'); ?>:</strong><br />
						<?php print form::password('password_again', $form['password_again'], 'class="login_text new_password_again"'); ?></td>
					</tr>
					<?php 
						//for plugins that want to add some extra stuff to this lovely view
						Event::run('ushahidi_action.login_new_user_form');
					?>
					<tr>
						<td><input type="submit" id="submit" name="submit" value="<?php echo Kohana::lang('ui_main.login_signup');?>" class="login_btn new_submit" /></td>
					</tr>
				</table>
			<?php echo form::close(); ?>
		</div>

	</div>

	<?php if (kohana::config('riverid.enable') == TRUE): ?>
	<div style="text-align:center;margin-top:20px;" id="openid_login" class="ui-corner-all">
		<small><?php echo $riverid_information; ?> 
			<a href="<?php echo $riverid_url; ?>"><?php echo Kohana::lang('ui_main.more_information'); ?></a>
		</small>
	</div>
	<?php endif; ?>

</div>
</body>
</html>