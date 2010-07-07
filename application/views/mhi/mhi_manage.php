<?php
/**
 * MHI - Manage Account
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
                <h2>Manage Your Account</h2>
				<div class="tabs">
                	<ul>
                    	<li><a class="ab-active" href="<?php echo url::site() ?>mhi/manage">Your Deployments</a></li>
                    	<li><a class="" href="<?php echo url::site() ?>mhi/account">Account Settings</a></li>
                    </ul>
                </div>
				<h3>Your Deployments
                	<span id="deployment-filter" class="one-line-select">
                        <span id="site-filter-all" class="select-item selected first-child">All</span><span id="site-filter-active" class="select-item">Active</span><span id="site-filter-inactive" class="select-item last-child">Inactive</span>
                    </span>
                </h3>
                <p>View and manage your deployments.</p>
				
                <div id="deployments">
                <?php foreach($sites as $site) { ?>
                <div class="deployment <?php if($site->site_active == 1) { ?>d-active<?php }else{?>d-inactive<?php } ?> clearfix">
                	<div class="d-left">
                        <h4><a href="http://<?php echo $site->site_domain.'.'.$domain_name; ?>"><?php echo $site->site_name; ?></a>  <span><?php if($site->site_active == 1) { ?>active<?php }else{?>inactive<?php } ?></span></h4>
                        <p class="d-tagline"><?php echo $site->site_tagline; ?></p>
                        
                    </div>
                    <div class="d-right">
                    	
                    </div>
                    <p class="d-actions"><a target="_blank" href="http://<?php echo $site->site_domain.'.'.$domain_name; ?>admin">Admin Dashboard</a> | Change admin password: <input type="text" class="text" /> <input type="button" value="go" /> | 
                    <?php if($site->site_active == 1) { ?> <a class="active-link" href="?deactivate=<?php echo $site->site_domain; ?>">Deactivate</a> <?php }else{ ?> <a class="active-link" href="?activate=<?php echo $site->site_domain; ?>">Activate</a> <?php } ?>
                	</p>
                </div>
               
                <!--CB: Hiding this... -->
                <div style="display:none">
				
							<?php if(in_array($site->site_domain,$sites_pw_changed)) { ?>
								<strong>* Password Changed</strong>
							<?php } ?>
							<?php print form::open(url::site().'mhi/manage', array('id' => 'frm-MHI-Admin-PW', 'name' => 'frm-MHI-Admin-PW')); ?>
								<input type="password" size="24" name="admin_password" maxlength="32" id="admin_password"/>
								Change password for 
								<select name="change_pw_for">
									<option value="one" selected="yes">just this deployment.</option>
									<option value="all">all deployments.</option>
								</select>
								<input type="hidden" name="site_domain" value="<?php echo $site->site_domain; ?>"/>
								<input class="button" type="submit" value="Change Password" />
							<?php print form::close(); ?>
				</div>
                    
				<?php } ?>
				
                <p class="no-results msg m-info">No results.</p>
                </div>
                <hr />
                <h3>Multi-Deployment Operations</h3>
                <p>Use these functions to perform changes across all your deployments.</p>
                
                <h4>Change Admin Password</h4>
                <form class="frm-content">
                <table><tbody>
                	<tr>
				      <td><label for="account_password">Current Password</label></td>
				      <td><input type="password" id="account_password" maxlength="32" name="account_password" size="24">
				     </td>
				    </tr>
                    <tr>
				      <td><label for="account_new_password">New Password</label></td>
				      <td><input type="password" id="account_confirm_new_password" maxlength="32" name="account_new_password" size="24"></td>
				    </tr>
                    <tr>
				      <td><label for="account_confirm_new_password">New Password Confirmation</label></td>
				      <td><input type="password" id="account_confirm_new_password" maxlength="32" name="account_confirm_new_password" size="24"></td>
				    </tr>
                    <tr>
				      <td></td>
				      <td><input class="button" type="submit" value="Save Password" /></td>
				    </tr>
                 </tbody></table>
                
                 </form>
                
            
            </div></div>
            <div class="twocol-right">
                <p class="side-bar-buttons"><a class="admin-button green" href="<?php echo url::site() ?>mhi/signup">New Deployment</a></p>
            </div>
            <div style="clear:both;"></div>
        </div>