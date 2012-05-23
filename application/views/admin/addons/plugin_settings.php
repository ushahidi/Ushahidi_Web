<?php 
/**
 * Plugin Settings View
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
				<h2><?php echo Kohana::lang('ui_admin.addons'); ?> 
					<a href="<?php echo url::base() . 'admin/addons/plugins' . '" class="active">' . Kohana::lang('ui_main.plugins') . '</a>' ?>
					<a href="<?php echo url::base() . 'admin/addons/themes' . '">' . Kohana::lang('ui_main.themes') . '</a>' ?>
				</h2>
				
				<?php print form::open(NULL, array('name'=>'plugin_settings')); ?>
				<div class="report-form">
					<?php
					if (isset($form_error) AND $form_error)
					{
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

					if (isset($form_saved) AND $form_saved) {
					?>
						<!-- green-box -->
						<div class="green-box">
							<h3><?php echo Kohana::lang('ui_main.configuration_saved');?></h3>
						</div>
					<?php
					}
					?>			
				
					<div class="head">
						<h3><?php echo (isset($title)) ? $title : Kohana::lang('ui_admin.settings');?></h3>
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-cancel.gif" class="cancel-btn" onclick="document.location.href='<?php echo url::site() . 'admin/addons/plugins/'; ?>'; return false;" />
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />	
					</div>				
					
					<div class="settings_holder">
						<?php
						
						echo $settings_form;
						
						?>
					</div>
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-cancel.gif" class="cancel-btn" onclick="document.location.href='<?php echo url::site() . 'admin/addons/plugins/'; ?>'; return false;" />
				</div>
				<?php print form::close(); ?>
			</div>