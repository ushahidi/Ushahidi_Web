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
					<a href="<?php echo url::base() . 'admin/settings/site' ?>"><?php echo Kohana::lang('ui_main.site');?></a>
					<a href="<?php echo url::base() . 'admin/settings' ?>"><?php echo Kohana::lang('ui_main.map');?></a>
					<a href="<?php echo url::base() . 'admin/settings/sms' ?>"><?php echo Kohana::lang('ui_main.sms');?></a>
					<a href="<?php echo url::base() . 'admin/settings/sharing' ?>"><?php echo Kohana::lang('ui_main.sharing');?></a>
					<a href="<?php echo url::base() . 'admin/settings/email' ?>" class="active"><?php echo Kohana::lang('ui_main.email');?></a>
					<a href="<?php echo url::base() . 'admin/settings/themes' ?>"><?php echo Kohana::lang('ui_main.themes');?></a>
				</h2>
				<?php print form::open(); ?>
				<div class="report-form">
					<?php
					if ($form_error) {
					?>
						<!-- red-box -->
						<div class="red-box">
							<h3><?php echo Kohana::lang('ui_main.error');?></h3>
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
							<h3><?php echo Kohana::lang('ui_main.configuration_saved');?></h3>
						</div>
					<?php
					}
					?>				
					<div class="head">
						<h3><?php echo Kohana::lang('ui_main.email_configuration');?></h3>
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->		
					<div class="sms_holder">
						<?php echo Kohana::lang('ui_main.email_settings_comment_00');?> <a href="<?php echo url::base()."admin/settings/site";?>"><?php echo Kohana::lang('ui_main.site_email_address');?></a> 
						(<strong> <?php echo Kohana::config('settings.site_email');?></strong>), <?php echo Kohana::lang('ui_main.email_settings_comment_0');?>.
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_server_username"); ?>"><?php echo Kohana::lang('ui_main.email_server_username');?></a></h4>
							<?php print form::input('email_username', $form['email_username'], ' class="text long2"'); ?>
						</div>
						<span>
							<?php echo Kohana::lang('ui_main.email_settings_comment_1');?>
						</span>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_server_password"); ?>"><?php echo Kohana::lang('ui_main.email_server_password');?></a></h4>
							<?php print form::password('email_password', $form['email_password'], ' class="text long2"'); ?>							
						</div>
						<span>
							<?php echo Kohana::lang('ui_main.email_settings_comment_2');?>
						</span>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_server_port"); ?>"><?php echo Kohana::lang('ui_main.email_server_port');?></a></h4>
							<?php print form::input('email_port', $form['email_port'], ' class="text long2"'); ?>
						</div>
						<span>
							<?php echo Kohana::lang('ui_main.email_settings_comment_3');?>
						</span>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_server_host"); ?>"><?php echo Kohana::lang('ui_main.email_server_host');?></a></h4>
							<?php print form::input('email_host', $form['email_host'], ' class="text long2"'); ?>
						</div>
						<span>
							<?php echo Kohana::lang('ui_main.email_settings_comment_4');?>
						</span>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_server_type"); ?>"><?php echo Kohana::lang('ui_main.email_server_type');?></a></h4>
							<?php print form::input('email_servertype', $form['email_servertype'], ' class="text long2"'); ?>								 
						</div>
						<span>
							<?php echo Kohana::lang('ui_main.email_settings_comment_5');?>
						</span>
						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_server_ssl_support"); ?>"><?php echo Kohana::lang('ui_main.email_server_ssl_support');?></a></h4>
								<?php print form::dropdown('email_ssl', $email_ssl_array, $form['email_ssl']); ?>
						</div>
						<span>
							<?php echo Kohana::lang('ui_main.email_settings_comment_6');?>
						</span>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>
