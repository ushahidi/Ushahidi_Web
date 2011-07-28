<?php 
/**
 * Clean URLs view page.
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
				<h2>
					<?php admin::settings_subtabs("cleanurl"); ?>
				</h2>
				<?php print form::open(NULL, array('id' => 'cleanurlForm', 'name' => 'cleanurlForm','action'=> url::site().'admin/settings/cleanurl')); ?>
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
						<h3><?php echo Kohana::lang('settings.cleanurl.title');?></h3>
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->		
					<div class="sms_holder">
						<div class="row">
							<h4><?php echo Kohana::lang('settings.cleanurl.enable_clean_url');?>?</h4>
								<?php if(!$is_clean_url_enabled) { ?>
								<?php print form::dropdown(array('name'=>'enable_clean_url','disabled' =>'true'), $yesno_array, '0'); ?>
								<p>
								<?php echo Kohana::lang('settings.cleanurl.clean_url_disabled');?>
								</p>
								<?php } else {?>
								<?php print form::dropdown('enable_clean_url', $yesno_array, $form['enable_clean_url']); ?>
								<p>
								<?php echo Kohana::lang('settings.cleanurl.clean_url_enabled');?>
								</p>
								<?php } ?>
						</div>						
						
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>
