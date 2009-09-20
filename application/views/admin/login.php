<?php 
/**
 * Login view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Ushahidi Admin</title>
<link href="<?php echo url::base() ?>media/css/admin/login.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="ushahidi_login_container">
    <div id="ushahidi_login_logo"><img src="<?php echo url::base() ?>media/img/admin/logo_login.gif" width="400" height="80" /></div>
    <div id="ushahidi_login">
      <table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
        <form method="POST" name="frm_login" style="line-height: 100%; margin-top: 0; margin-bottom: 0">     
			<?php
			 if ($form_error) { ?>
            <tr>
              	<td align="left" class="login_error">
				<?php
				foreach ($errors as $error_item => $error_description)
				{
					print (!$error_description) ? '' : "&#8226;&nbsp;" . $error_description . "<br />";
				}
				?>
				</td>
            </tr>
			<?php } ?>
            <tr>
              <td><strong>Username:</strong><br />
              <input type="text" name="username" id="username" class="login_text" /></td>
            </tr>
            <tr>
              <td><strong>Password:</strong><br />
              <input name="password" type="password" class="login_text" id="password" size="20" /></td>
            </tr>
            <tr>
              <td><input type="checkbox" id="remember" name="remember" value="1" checked="checked" />Stay logged in on this computer?</td>
            </tr>
            <tr>
              <td><input type="submit" id="submit" name="submit" value="Log In" class="login_btn" /></td>
            </tr>
            <tr>
            <td><a href="<?php echo url::base()?>login/resetpassword"> <?php echo Kohana::lang('auth.forgot_password');?></a></td>
            </tr>
        </form>
      </table>
  </div>
</div>
</body>
</html>
