<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Ushahidi Web Installer</title>
<link href="<?php echo url::base() ?>media/css/admin/login.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="ushahidi_login_container">
    <div id="ushahidi_login_logo"><img src="<?php echo url::base() ?>media/img/admin/logo_login.gif" width="400" height="80" /></div>
    <div id="ushahidi_login">
      <table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
        <form method="POST" name="frm_login" style="line-height: 100%; margin-top: 0; margin-bottom: 0">     
			<?php
			//if ($form_error) { ?>
            <tr>
              	<td align="left" class="login_error">
				<?php
				    echo "Installer";
				/*foreach ($errors as $error_item => $error_description)
				{
					// print "<li>" . $error_description . "</li>";
					print (!$error_description) ? '' : "&#8226;&nbsp;" . $error_description . "<br />";
				}*/
				?>
				</td>
            </tr>
			<?php //} ?>
            <tr>
              <td><strong>Username:</strong><br />
              <?php print form::input('username', $form['username'], 'class="login_text"');?></td>
            </tr>
            <tr>
              <td><strong>Password:</strong><br />
              <?php print form::input('password',$form['password'], 'class="login_text"');?></td>
            </tr>
            <tr>
              <td>
                <strong>Host:</strong><br />
              <?php print form::input('host', $form['host'], 'class="login_text"');?></td>
            </tr>
            <tr>
              <td>
                <strong>Database Type:</strong><br />
                
              <?php print form::dropdown('select_db_type',$db_types,'', ' class="select" '); ?>
            </tr>
            <tr>
              <td><input type="submit" id="submit" name="submit" value="Install" class="login_btn" /></td>
            </tr>
        </form>
      </table>
  </div>
</div>
</body>
</html>
