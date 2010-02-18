<?php 
/**
 * Email view page.
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
					<a href="<?php echo url::base() . 'admin/settings/site' ?>">Site</a>
					<a href="<?php echo url::base() . 'admin/settings' ?>">Map</a>
					<a href="<?php echo url::base() . 'admin/settings/sms' ?>">SMS</a>
					<a href="<?php echo url::base() . 'admin/settings/sharing' ?>">Sharing</a>
					<a href="<?php echo url::base() . 'admin/settings/email' ?>" class="active">Email</a>
					<a href="<?php echo url::base() . 'admin/settings/themes' ?>">Themes</a>
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
						<h3>Mail Server Settings</h3>
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->		
					<div class="sms_holder">
						In order to receive reports by email, please input your email account settings below. 
						Please note that emails will be received at your <a href="<?php echo url::base()."admin/settings/site";?>">site email address</a> 
						<strong><?php echo Kohana::config('settings.site_email');?></strong>, so your settings have to be associated with 
						this email address.
						<div class="row">
							<h4>Mail Server Username</h4>
							<?php print form::input('email_username', $form['email_username'], ' class="text long2"'); ?>
						</div>
						<span>
							Some providers require a full email address as username
						</span>
						<div class="row">
							<h4>Mail Server Password</h4>
							<?php print form::password('email_password', $form['email_password'], ' class="text long2"'); ?>							
						</div>
						<span>
							Mail server password
						</span>
						<div class="row">
							<h4>Mail Server Port</h4>
							<?php print form::input('email_port', $form['email_port'], ' class="text long2"'); ?>
						</div>
						<span>
							Common Ports: 25, 110, 995 (Gmail POP3 SSL), 993 (Gmail IMAP SSL)
						</span>
						<div class="row">
							<h4>Mail Server Host</h4>
							<?php print form::input('email_host', $form['email_host'], ' class="text long2"'); ?>
						</div>
						<span>
							Config Mail Server Examples: mail.yourwebsite.com, imap.gmail.com, pop.gmail.com
						</span>
						<div class="row">
							<h4>Mail Server Type</h4>
							<?php print form::input('email_servertype', $form['email_servertype'], ' class="text long2"'); ?>								 
						</div>
						<span>
							Config Mail Server Type Examples: pop3, imap
						</span>
						<div class="row">
							<h4>Mail Server SSL support</h4>
								<?php print form::dropdown('email_ssl', $email_ssl_array, $form['email_ssl']); ?>
						</div>
						<span>
							Enable or disable SSL
						</span>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>
