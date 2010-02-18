<?php 
/**
 * Sms global view page.
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
			<div class="bg">
				<h2><?php echo $title; ?> 
					<a href="<?php echo url::base() . 'admin/settings/site' ?>" class="active">Site</a>
					<a href="<?php echo url::base() . 'admin/settings' ?>">Map</a>
					<a href="<?php echo url::base() . 'admin/settings/sms' ?>">SMS</a>
					<a href="<?php echo url::base() . 'admin/settings/sharing' ?>">Sharing</a>
					<a href="<?php echo url::base() . 'admin/settings/email' ?>">Email</a>
				</h2>
				<?php print form::open(); ?>
				<div class="report-form">
					<?php
					if ($form_error) {
					?>
						<!-- red-box -->
						<div class="red-box">
							<h3>Error!</h3>
							<ul>
							<?php
							foreach ($errors as $error_item => $error_description)
							{
								// print "<li>" . $error_description . "</li>";
								print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
							}
							?>
							</ul>
						</div>
					<?php
					}

					if ($form_saved) {
					?>
						<!-- green-box -->
						<div class="green-box">
							<h3>Your Settings Have Been Saved!</h3>
						</div>
					<?php
					}
					?>				
					<div class="head">
						<h3>SMS Setup Options</h3>
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->
		
					<div class="sms_nav_holder">
						<a href="<?php echo url::base() . 'admin/settings/sms' ?>">Option 1: Use Frontline SMS</a>
						<a href="<?php echo url::base() . 'admin/settings/smsglobal' ?>" class="active">Option 2: Use a Global SMS Gateway</a>
					</div>
		
					<div class="sms_holder">
						<table style="width: 630px;" class="my_table">
							<tr>
								<td style="width:60px;">
									<span class="big_blue_span">Step 1:</span>
								</td>
								<td>
									<h4 class="fix">Sign up for Clickatells service by <a href="https://www.clickatell.com/central/user/client/step1.php?prod_id=2" target="_blank">clicking here</a>. <sup><a href="#">?</a></sup></h4>
								</td>
							</tr>
							<tr>
								<td>
									<span class="big_blue_span">Step 2:</span>
								</td>
								<td>
									<h4 class="fix">Enter your clickatell access information below. <sup><a href="#">?</a></sup></h4>
									<div class="row">
										<h4>Your Clickatell API Number:</h4>
										<?php print form::input('clickatell_api', $form['clickatell_api'], ' class="text title_2"'); ?>
									</div>
									<div class="row">
										<h4>Your Clickatell User Name:</h4>
										<?php print form::input('clickatell_username', $form['clickatell_username'], ' class="text title_2"'); ?>
									</div>
									<div class="row">
										<h4>Your Clickatell Password:</h4>
										<?php print form::password('clickatell_password', $form['clickatell_password'], ' class="text title_2"'); ?>
									</div>
								</td>
							</tr>
							<!--<tr>
								<td>
									<span class="big_blue_span">Step 3:</span>
								</td>
								<td>
									<h4 class="fix">Check Your Clickatell Credit Balance. <sup><a href="#">?</a></sup></h4>
									<div class="row">
										<h4><a href="javascript:clickatellBalance()">Load Credit Balance</a>&nbsp;<span id="balance_loading"></span></h4>
									</div>
								</td>
							</tr>-->							
						</table>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>