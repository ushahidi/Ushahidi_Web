<h2>Log In</h2>
    
<div>
	<table width="100%" border="0" cellspacing="3" cellpadding="4">
	<form action="<?php echo url::site() ?>login" method="POST" name="frm_login">
	<input type="hidden" name="redirect_to" id="redirect_to" value="<?php echo url::site() ?>mobileci" />
		<tr>
			<td><strong><?php echo Kohana::lang('ui_main.email');?>:</strong><br />
			<input type="text" name="username" id="username" /></td>
		</tr>
		<tr>
			<td><strong><?php echo Kohana::lang('ui_main.password');?>:</strong><br />
			<input name="password" type="password" id="password" size="20" /></td>
		</tr>
		<tr>
			<td><input type="checkbox" id="remember" name="remember" value="1" checked="checked" />
			<?php echo Kohana::lang('ui_main.password_save');?></td>
		</tr>
		<tr>
			<td><input type="submit" id="submit" name="submit" value="Log In" /></td>
		</tr>
		<tr>
			<td><a href="<?php echo url::site()?>mobileci/createaccount"> Create Account</a></td>
		</tr>
		<tr>
			<td><a href="<?php echo url::site()?>login/resetpassword"> <?php echo Kohana::lang('ui_main.forgot_password');?></a></td>
		</tr>
	</form>
	</table>
</div>