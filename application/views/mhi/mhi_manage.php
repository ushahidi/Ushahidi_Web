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
		
				<a href="<?php echo url::site() ?>mhi/signup">Create New Deployment</a>
				
				<a href="<?php echo url::site() ?>mhi/account">Account Settings</a>
				
				<a href="<?php echo url::site() ?>mhi/manage">Your Sites</a>
				
				<br/>
				<h3>Your Sites</h3>
				
				<table><tbody>
					<tr>
						<th style="padding:2px;margin:0px;text-align:center;">Site</th>
						<th style="padding:2px;margin:0px;text-align:center;">Go To</th>
						<th style="padding:2px;margin:0px;text-align:center;">Status</th>
						<th style="padding:2px;margin:0px;text-align:center;">Change Admin Password</th>
					</tr>
				<?php foreach($sites as $site) { ?>
					<tr>
						<td><a href="http://<?php echo $site->site_domain.'.'.$domain_name; ?>" target="_blank"><?php echo $site->site_domain.'.'.$domain_name; ?></a></td>
						<td><a href="http://<?php echo $site->site_domain.'.'.$domain_name; ?>" target="_blank">Homepage</a> <a href="http://<?php echo $site->site_domain.'.'.$domain_name; ?>admin" target="_blank">Admin</a></td>
						<td>
							<strong><?php if($site->site_active == 1) { ?> Active <?php }else{ ?> Deactivated <?php } ?></strong>
							<div style="font-size:xx-small;"><?php if($site->site_active == 1) { ?> <a href="?deactivate=<?php echo $site->site_domain; ?>">Deactivate</a> <?php }else{ ?> <a href="?activate=<?php echo $site->site_domain; ?>">Activate</a> <?php } ?></div>
						</td>
						<td>
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
						</td>
					</tr>
				<?php } ?>
				</tbody></table>
                
                   
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