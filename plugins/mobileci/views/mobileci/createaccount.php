<h2>Create Account</h2>
    
<div>
	<table width="100%" border="0" cellspacing="3" cellpadding="4">
	<form action="<?php echo url::site() ?>mobileci/createaccount" method="POST" name="frm_createaccount">
		<tr>
			<td><strong><?php echo Kohana::lang('ui_main.name');?>:</strong><br />
			<input type="text" name="name" id="name" /></td>
		</tr>
		<tr>
			<td><strong><?php echo Kohana::lang('ui_main.email');?>:</strong><br />
			<input type="text" name="email" id="email" /></td>
		</tr>
		<tr>
			<td><strong><?php echo Kohana::lang('ui_main.password');?>:</strong><br />
			<input name="password" type="password" id="password" size="20" /></td>
		</tr>
		<tr>
			<td><input type="submit" id="submit" name="submit" value="Create Account" /></td>
		</tr>
	</form>
	</table>
</div>