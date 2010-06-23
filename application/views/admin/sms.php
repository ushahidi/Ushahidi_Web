<?php 
/**
 * Sms view page.
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
					<?php admin::settings_subtabs("sms"); ?>
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
						<h3><?php echo Kohana::lang('settings.sms.title');?></h3>
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->
		
					<div class="sms_nav_holder">
						<a href="<?php echo url::site() . 'admin/settings/sms' ?>" class="active"><?php echo Kohana::lang('settings.sms.option_1');?></a>
						<a href="<?php echo url::site() . 'admin/settings/smsglobal' ?>"><?php echo Kohana::lang('settings.sms.option_2');?></a>
					</div>
		
					<div class="sms_holder">
						<table style="width: 630px;" class="my_table">
							<tr>
								<td style="width:60px;">
									<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 1:</span>
								</td>
								<td>
									<h4 class="fix"><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_flsms_download"); ?>"><?php echo Kohana::lang('settings.sms.flsms_download');?></a></h4>
									<p>
										<?php echo Kohana::lang('settings.sms.flsms_description');?>.
									</p>
									<a href="http://www.frontlinesms.com/download/" class="no_border">
										<img src="<?php echo url::base() ?>media/img/admin/download_frontline_engine.gif" />
									</a>
								</td>
							</tr>
							<tr>
								<td>
									<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 2:</span>
								</td>
								<td>
									<h4 class="fix"><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_flsms_synchronize"); ?>"><?php echo Kohana::lang('settings.sms.flsms_synchronize');?></a></h4>
									<p>
										<?php echo Kohana::lang('settings.sms.flsms_instructions');?>.
									</p>
									<p class="sync_key">
										<?php echo Kohana::lang('settings.sms.flsms_key');?>: <span><?php echo $frontlinesms_key; ?></span><br /><br />
										<?php echo Kohana::lang('settings.sms.flsms_link');?>:<br /><span><?php echo $frontlinesms_link; ?></span>
									</p>
								</td>
							</tr>
							<tr>
								<td>
									<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 3:</span>
								</td>
								<td>
									<h4 class="fix"><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_flsms_text_1"); ?>"><?php echo Kohana::lang('settings.sms.flsms_text_1');?>.</a></h4>
									<p>
										<?php echo Kohana::lang('settings.sms.flsms_text_2');?>.
									</p>
									<div class="row">
										<h4><?php echo Kohana::lang('ui_main.phone');?> 1:</h4>
										<?php print form::input('sms_no1', $form['sms_no1'], ' class="text title_2"'); ?>
									</div>
									<div class="row">
										<h4><?php echo Kohana::lang('ui_main.phone');?> 2:</h4>
										<?php print form::input('sms_no2', $form['sms_no2'], ' class="text title_2"'); ?>
									</div>
									<div class="row">
										<h4><?php echo Kohana::lang('ui_main.phone');?> 3:</h4>
										<?php print form::input('sms_no3', $form['sms_no3'], ' class="text title_2"'); ?>
									</div>
								</td>
							</tr>
						</table>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
				</div>
				<?php print form::close(); ?>
			</div>
