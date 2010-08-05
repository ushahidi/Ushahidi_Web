<?php 
/**
 * MHI Settings
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
					<?php /*admin::settings_subtabs("mhi_settings");*/ ?>
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
							<h3>MHI Settings</h3>
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
						</div>
						
						<!-- column -->
						<div class="l-column">
							<div class="row">
								<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.settings_google_analytics"); ?>"><?php echo Kohana::lang('settings.site.google_analytics');?></a></h4>
								<?php echo Kohana::lang('settings.site.google_analytics_example');?> &nbsp;&nbsp;
								<?php print form::input('google_analytics', $form['google_analytics'], ' class="text"'); ?>
							</div>
						</div>
					</div>
				<?php print form::close(); ?>
			</div>
