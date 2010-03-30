<?php
/**
 * MHI - MHI Account Management
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
	<div class="shadow">
		
		<h3>Account Settings</h3>
		
		<a href="<?php echo url::base() ?>mhi/logout"><?php echo Kohana::lang('ui_admin.logout');?></a>

		<a href="<?php echo url::base() ?>mhi/signup">Create New Instance</a>
		
		<a href="<?php echo url::base() ?>mhi/account">Account Settings</a>
		
		<a href="<?php echo url::base() ?>mhi/account">Your Sites</a>
		
		<?php print form::open(url::base().'mhi/account', array('id' => 'frm-MHI-Account', 'name' => 'frm-MHI-Account', 'class' => 'frm-content')); ?>
	
		<table><tbody>
	
			<?php
			if ($form_error) {
			?>
	        <tr>
	          	<td align="left" class="error" colspan="2">
				<?php
				foreach ($errors as $error_item => $error_description)
				{
					echo '&#8226; '.$error_description.'<br />';
				}
				?>
				</td>
	        </tr>
			<?php } ?>
	
		    <tr>
		      <td><label for="firstname">First name</label></td>
		      <td><input type="text" size="24" name="firstname" maxlength="42" id="firstname" value="<?php echo $user->firstname; ?>" />
		      <span><em>Required.</em></span></td>
		    </tr>
		    <tr>
		      <td><label for="lastname">Last name</label></td>
		      <td><input type="text" size="24" name="lastname" maxlength="42" id="lastname" value="<?php echo $user->lastname; ?>" />
		      <span><em>Required.</em></span></td>
		    </tr>
		    <tr>
		      <td><label for="email">Email</label></td>
		      <td><input type="text" size="24" name="email" maxlength="42" id="email" value="<?php echo $user->email; ?>" />
		      <span><em>Required.</em> Also be your username.</span></td>
		    </tr>
		    <tr>
		      <td><label for="password">Password</label></td>
		      <td><input type="password" size="24" name="password" maxlength="42" id="password"/>
		      <span>Use 4 to 32 characters.</span></td>
		    </tr>
		    <tr>
		      <td><label for="signup_confirm_password">Confirm Password</label></td>
		      <td><input type="password" size="24" name="confirm_password" maxlength="42" id="confirm_password"/></td>
		    </tr>
		</tbody></table>
	
		<p>
			<input class="button" type="submit" value="Update Account" />
		</p>
	
	<?php print form::close(); ?>
		
	</div>
</div>
<div class="twocol-right">
	<div class="side-bar-module">
		<h4>Side Bar</h4>
		<div class="side-bar-content">
			<p>Content goes here. This will scroll with the page.</p>
		</div>
	</div>
</div>