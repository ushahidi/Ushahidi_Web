<?php
/**
 * MHI
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Page View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>

	<p>Hi. MHI is cool. If you also want to be cool and you don't already have an MHI account, <a href="<?php echo url::base() . "mhi/signup" ?>">SIGN UP HERE</a>. Otherwise, use the form below to sign in.</p>

	<table>
	<form method="POST" name="frm_login" style="line-height: 100%; margin-top: 0; margin-bottom: 0">
		<?php
		if ($form_error) {
		//if(TRUE) {
		?>


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
          <td><strong><?php echo Kohana::lang('ui_main.username');?>:</strong><br />
          <input type="text" name="username" id="username" class="login_text" /></td>
        </tr>
        <tr>
          <td><strong><?php echo Kohana::lang('ui_main.password');?>:</strong><br />
          <input name="password" type="password" class="login_text" id="password" size="20" /></td>
        </tr>
        <tr>
          <td><input type="submit" id="submit" name="submit" value="Log In" class="login_btn" /></td>
        </tr>
    </form>
    </table>