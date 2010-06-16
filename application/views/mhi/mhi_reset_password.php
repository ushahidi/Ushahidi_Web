<?php
/**
 * MHI - About
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
		<div id="primary-content">
            <div class="twocol-left"><div class="content-shadow">
                <h2>Password Reset</h2>
                <p class="intro-para">
                <?php
					if($reset_flag == TRUE)
					{
				?>
						<strong>Check your inbox over the next few minutes for your new password!</strong>
				<?php
					}else{
				?>
						Enter your e-mail address and we will send you a message with a new password.
						
						<?php print form::open(url::site().'mhi/reset_password', array('id' => 'frm-MHI-Reset-Password', 'name' => 'frm-Reset-Password', 'class' => 'frm-content')); ?>
		                <table><tbody>	
							<tr>
								<td><label for="reset_email">Email</label></td>
								<td><input type="text" size="24" name="email" maxlength="42" id="email"/></td>
							</tr>
							</tbody></table>
							<p>
								<input class="button" type="submit" value="Reset Password" />
							</p>
				        <?php print form::close(); ?>
				<?php
					}
                ?>
                </p>
            </div></div>
            <div class="twocol-right">
                <!-- CB: We'll just leave this empty for now.
                
                <div class="side-bar-module rounded shadow">
                    <p>Sign-up Sidebar promo to go here</p>
                </div>
                -->
            </div>
            <div style="clear:both;"></div>
        </div>