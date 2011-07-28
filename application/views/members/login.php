<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Kohana::lang('ui_main.members');?></title>
<?php
echo html::stylesheet(url::file_loc('css').'media/css/jquery-ui-themeroller', '', true);
echo html::stylesheet(url::file_loc('css').'media/css/login', '', true);
echo html::stylesheet(url::file_loc('css').'media/css/openid', '', true);
echo html::script(url::file_loc('js').'media/js/jquery', true);
echo html::script(url::file_loc('js').'media/js/openid/openid-jquery', true);
echo html::script(url::file_loc('js').'media/js/openid/openid-jquery-en', true);
?>
<script type="text/javascript">
	<?php echo $js; ?>
</script>
</head>

<body>
<div id="openid_login_container">

	<div id="ushahidi_site_name" class="ui-corner-all">
    	<div id="logo">
			<h1><?php echo $site_name; ?></h1>
			<span><?php echo $site_tagline; ?></span>
		</div>
    </div>

	<div id="openid_login" class="ui-corner-all">
		<?php
		if ($form_error)
		{
			?><div class="login_error"><?php
			foreach ($errors as $error_item => $error_description)
			{
				echo (!$error_description) ? '' : "&#8226;&nbsp;" . $error_description . "<br />";
			}
			?></div><?php
		}
		
		if ($openid_error)
		{
			?><div class="login_error"><?php echo "&#8226;&nbsp;" . $openid_error;?></div><?php
		}
		
		if ($success)
		{
			?><div class="login_success"><?php echo "&#8226;&nbsp;" . Kohana::lang('ui_main.login_confirmation_sent');?></div><?php
		}
		?>
		<h2><a href="javascript:toggle('signin_userpass');"><?php echo Kohana::lang('ui_main.login_signin_userpass'); ?></a></h2>
		<div id="signin_userpass" class="signin_select ui-corner-all">
			<form method="post" id="userpass_form">
				<input type="hidden" name="action" value="signin">
				<table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.username');?>:</strong><br />
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
						<td><input type="submit" id="submit" name="submit" value="Log In" class="login_btn" /></td>
					</tr>
					<tr>
						<td><a href="javascript:toggle('signin_forgot');"> <?php echo Kohana::lang('ui_main.forgot_password');?></a></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="signin_forgot" class="signin_select ui-corner-all" style="margin-top:10px;">
			<form method="post" id="userforgot_form">
				<input type="hidden" name="action" value="forgot">
				<table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.password_reset_prompt');?></strong><br />
						<?php print form::input('resetemail', $form['resetemail'], ' class="login_text"'); ?></td>
					</tr>
					<tr>
						<td><input type="submit" id="submit" name="submit" value="Reset password" class="login_btn" /></td>
					</tr>
				</table>
			</form>
		</div>
		
		<h2><a href="javascript:toggle('signin_openid');"><?php echo Kohana::lang('ui_main.login_signin_openid'); ?></a></h2>
		<div id="signin_openid" class="signin_select ui-corner-all">
			<form method="post" id="openid_form">
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
			</form>
		</div>

		<?php echo Kohana::lang('ui_main.login_signup_text'); ?>, <a href="javascript:toggle('signin_new');"><?php echo Kohana::lang('ui_main.login_signup_click'); ?></a>
		<div id="signin_new" class="signin_select ui-corner-all" style="margin-top:10px;">
			<form method="post" id="usernew_form">
				<input type="hidden" name="action" value="new">
				<table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.name');?>:</strong><br />
						<?php print form::input('name', $form['name'], ' class="login_text"'); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.email');?>:</strong><br />
						<?php print form::input('email', $form['email'], ' class="login_text"'); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.username');?>:</strong><br />
						<?php print form::input('username', $form['username'], ' class="login_text"'); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.password');?>:</strong><br />
						<?php print form::password('password', $form['password'], ' class="login_text"'); ?></td>
					</tr>
					<tr>
						<td><strong><?php echo Kohana::lang('ui_main.password_again');?>:</strong><br />
						<?php print form::password('password_again', $form['password_again'], ' class="login_text"'); ?></td>
					</tr>
					<tr>
						<td><input type="submit" id="submit" name="submit" value="Sign Up" class="login_btn" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
</body>
</html>