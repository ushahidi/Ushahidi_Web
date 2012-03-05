<?php 
/**
 * Reset password view page.
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
<title><?php echo Kohana::lang('ui_main.reset_password');?></title>
<link href="<?php echo url::file_loc('css'); ?>media/css/login.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="ushahidi_login_container">
    <div id="ushahidi_login_logo"><img src="<?php echo url::file_loc('img'); ?>media/img/admin/logo_login.gif" width="400" height="80" /></div>
    <div id="ushahidi_login">
      <table width="100%" border="0" cellspacing="3" cellpadding="4" background="" id="ushahidi_loginbox">
      <?php print form::open(NULL,array('id' => 'frm_reset',
			'name' => 'frm_reset',
			'method' => 'post', 
			'style' => 'line-height: 100%; margin-top: 0; margin-bottom: 0' )); ?>     
			<?php
			 if ($form_error) { ?>
            <tr>
              	<td align="left" class="login_error">
				<?php
				foreach ($errors as $error_item => $error_description)
				{
					if ($error_description)
					{
						echo '&#8226;&nbsp;'.$error_description.'<br />';
					}
				}
				?>
				</td>
            </tr>
			<?php }
			if ($password_reset) { ?>
			<tr>
				<td align="left">
					<!-- green-box -->
					<div class="green-box">
						<h3><?php echo Kohana::lang('ui_main.password_reset_confirm'); ?></h3>
						<br />
						<a href="<?php echo url::site().'login'?>"><?php echo Kohana::lang('ui_main.login');?></a>
					</div>
				</td>
			</tr>
			<?php } else { ?>
            <tr>
              <td><strong><?php echo Kohana::lang('ui_main.registered_email')?></strong><br />
              <?php print form::input('resetemail', '', 
						' class="login_text"'); ?></td>
            </tr>
            <tr>
              <td><input type="submit" id="resetemail" name="submit" value="Reset password" class="login_btn" />
              <br /><br />
              <a href="<?php echo url::site().'login'?>">Login</a>
              </td>
            </tr>
            <?php } ?>
        <?php print form::close(); ?>
      </table>
  </div>
</div>
</body>
</html>