<?php 
/**
 * Facebook Settings view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Facebook Settings View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">

				<h2>
					<?php admin::settings_subtabs("facebook"); ?>
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
							<h3><?php echo Kohana::lang('ui_main.configuration_saved');?></h3>
						</div>
					<?php
					}
					?>				
					<div class="head">
						<h3><?php echo Kohana::lang('settings.facebook.title');?></h3>
						<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
					</div>
					<!-- column -->
		
					<div class="sms_holder">
						<div class="row" style="margin-top:20px;">
							<h4><?php echo Kohana::lang('settings.facebook.description');?>:<BR /><a href="http://www.facebook.com/developers/" target="_blank">http://www.facebook.com/developers/</a></h4>
						</div>
						<div class="row">
							<h4><?php echo Kohana::lang('settings.facebook.app_id');?>:</h4>
							<?php print form::input('facebook_appid', $form['facebook_appid'], ' class="text title_2"'); ?>
						</div>
						<div class="row">
							<h4><?php echo Kohana::lang('settings.facebook.app_secret');?>:</h4>
							<?php print form::input('facebook_appsecret', $form['facebook_appsecret'], ' class="text title_2"'); ?>
						</div>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
				</div>
				<?php print form::close(); ?>
			</div>
