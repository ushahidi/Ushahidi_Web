<?php
/**
 * MHI - Signup
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
	
	<h3>Create New Instance</h3>
	
	<p>Fill out the form below to set up your own instance of <?php echo $site_name; ?>.
	<?php print form::open(url::base().'mhi/create', array('id' => 'frm-MHI-Signup', 'name' => 'frm-MHI-Signup', 'class' => 'frm-content')); ?>
	
	<?php if($logged_in == FALSE){ ?>
	
    	<img src="<?php echo url::base(); ?>media/img/mhi/step-1.gif" align="left" class="step" /> <h2 class="step-1">Create Your Account</h2>
       
        <table><tbody>
            <tr>
              <td><label for="signup_first_name">First name</label></td>
              <td><input type="text" size="24" name="signup_first_name" maxlength="42" id="signup_first_name"/></td>
            </tr>
            <tr>
              <td><label for="signup_last_name">Last name</label></td>
              <td><input type="text" size="24" name="signup_last_name" maxlength="42" id="signup_last_name"/></td>
            </tr>
            <tr>
              <td><label for="signup_email">Email</label></td>
              <td><input type="text" size="24" name="signup_email" maxlength="42" id="signup_email"/>
              <span>This will also be your username.</span></td>
            </tr>
            <tr>
              <td><label for="signup_password">Password</label></td>
              <td><input type="password" size="24" name="signup_password" maxlength="42" id="signup_password"/>
              <span>Use 4 to 32 characters.</span></td>
            </tr>
            <tr>
              <td><label for="signup_confirm_password">Confirm Password</label></td>
              <td><input type="password" size="24" name="signup_confirm_password" maxlength="42" id="signup_confirm_password"/></td>
            </tr>
        </tbody></table>
        
        <hr />
        
	<?php }else{ ?>
	
		<img src="<?php echo url::base(); ?>media/img/mhi/step-1.gif" align="left" class="step" /> <h2 class="step-1">Verify Your Account</h2>
		
		<p>
        	<label for="verify_password">Account Password</label><br/>
        	<input type="password" size="24" name="verify_password" maxlength="42" id="verify_password" />
        	
        </p>
	
	<?php } ?>
        
        <img src="<?php echo url::base(); ?>media/img/mhi/step-2.gif" align="left"  class="step"/> <h2 class="step-2">Create Your Instance Address</h2>
        <p class="desc">Each instance has it's own web address. <strong>No spaces, use letters and numbers only.</strong></p>
        <p class="url">http://<input type="text" size="20" onfocus="this.style.color = 'black'" name="signup_subdomain" maxwidth="30" id="signup_subdomain"/>.<?php echo $domain_name; ?></p>
        
        <hr />
        
        <img src="<?php echo url::base(); ?>media/img/mhi/step-3.gif" align="left" class="step" /> <h2 class="step-3">Enter Your Instance Details</h2>
        <p>
        	<label for="signup_instance_name">Instance Name</label><br/>
        	<input type="text" size="30" name="signup_instance_name" maxlength="40" id="signup_instance_name" autocomplete="off"/>
        	
        </p>
        <p>
        	<label for="signup_instance_tagline">Instance Tagline</label><br/>
        	<input type="text" size="30" name="signup_instance_tagline" maxlength="40" id="signup_instance_tagline" autocomplete="off"/>
        	
        </p>
        
        <p>
        	<input class="button" type="submit" value="Finish &amp; Create Instance" />
        </p>
        
<?php print form::close(); ?>
          
</div></div>
<div class="twocol-right">
	<div class="side-bar-module">
    	<h4>Side Bar</h4>
        <div class="side-bar-content">
        	<p>Content goes here.</p>
        </div>
    </div>
</div>
