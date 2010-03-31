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
<div class="twocol-left">
	<div class="intro-slideshow">
    	<p class="slide">
        	<img src="<?php echo url::base(); ?>media/img/mhi/info_0-intro.jpg" />
        </p>
        <p class="slide">
        	<img src="<?php echo url::base(); ?>media/img/mhi/info_01-intro.jpg" />
        </p>
        <p class="slide">
        	<img src="<?php echo url::base(); ?>media/img/mhi/info_02-intro.jpg" />
        </p>
       
        <p class="slide">
        	<img src="<?php echo url::base(); ?>media/img/mhi/info_6-example.jpg" />
            <a href="#">Ushahidi-Haiti</a> was used to crowdsource and visualize crisis information after the earthquake. <a class="cycle-Resume" href="#">Replay animation</a>.
        </p>
    </div>
</div>
<div class="twocol-right">
	<h2 class="title-lead-in">Use Ushahidi to CROWDSOURCE and VISUALIZE information.</h2>
    <p class="sub-title">Nothing to install, minimum configuration.</p>
    <p><a class="button btn_sign-up" href="<?php echo url::base()."mhi/signup" ?>">Sign Up Now!</a></p>
    
    <div class="footer-links">
    	<ul>
            <li class="first"><a href="<?php echo url::base() ?>mhi/about">About</a></li>
            <li><a href="#">Contact Us</a></li>
            <li class="regular">Copyright &copy; <?php echo date('Y'); ?> Ushahidi</li>
        </ul>
    </div>
</div>
</div>





<table style="clear:both;">
	<form method="POST" name="frm_login" style="line-height: 100%; margin-top: 0; margin-bottom: 0">

		<?php
		if ($form_error) {
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

