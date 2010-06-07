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
					<div class="head">
						<h3><?php echo (isset($title)) ? $title : Kohana::lang('ui_admin.settings');?></h3>
					</div>
					
					<!-- tabs -->
					<div class="tabs">
						<!-- tab -->
						<div class="tab">
							<ul>
								<li><a href="#" onClick="document.plugin_settings.submit();"><?php echo strtoupper(Kohana::lang('ui_admin.save'));?></a></li>
								<li><a href="#" onClick="document.plugin_settings.reset();"><?php echo strtoupper(Kohana::lang('ui_admin.reset'));?></a></li>
								<li><a href="<?php echo url::base() . 'admin/addons/plugins' ?>"><?php echo strtoupper(Kohana::lang('ui_admin.back'));?></a></li>
							</ul>
						</div>
					</div>				
					
					<div class="settings_holder">
						<?php
						
						echo $form;
						
						?>
					</div>
					
					<!-- tabs -->
					<div class="tabs">
						<!-- tab -->
						<div class="tab">
							<ul>
								<li><a href="#"><?php echo strtoupper(Kohana::lang('ui_admin.save'));?></a></li>
								<li><a href="#"><?php echo strtoupper(Kohana::lang('ui_admin.reset'));?></a></li>
								<li><a href="#"><?php echo strtoupper(Kohana::lang('ui_admin.back'));?></a></li>
							</ul>
						</div>
					</div>
					
				</div>
				<?php print form::close(); ?>
			</div>