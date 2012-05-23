<?php 
/**
 * SSL view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Settings Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php admin::settings_subtabs("https"); ?>
				</h2>
				<?php print form::open(NULL, array('id' => 'httpsForm', 'name' => 'httpsForm','action'=> url::site().'admin/settings/https')); ?>
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
						<h3><?php echo Kohana::lang('settings.https.title');?></h3>
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->		
					<div class="sms_holder">
						<div class="row">
							<h4><?php echo Kohana::lang('settings.https.enable_https');?>?</h4>
								<?php if ( ! $is_https_capable): ?>
								<?php print form::dropdown(array('name'=>'enable_https','disabled' =>'true'), $yesno_array, '0'); ?>
								<p>
								<?php echo Kohana::lang('settings.https.https_disabled');?>
								</p>
								<?php else: ?>
								<?php print form::dropdown('enable_https', $yesno_array, $form['enable_https']); ?>
								<p>
								<?php echo Kohana::lang('settings.https.https_enabled');?>
								</p>
								<?php endif; ?>
						</div>						
						
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>
