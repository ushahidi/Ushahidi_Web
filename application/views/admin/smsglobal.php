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
					<a href="<?php echo url::site() . 'admin/settings/site' . '" class="active">' . Kohana::lang('ui_main.site') . '</a>' ?>
					<a href="<?php echo url::site() . 'admin/settings' . '">' . Kohana::lang('ui_main.map') . '</a>' ?>
					<a href="<?php echo url::site() . 'admin/settings/sms' . '">' . Kohana::lang('ui_main.sms') . '</a>' ?>
					<a href="<?php echo url::site() . 'admin/settings/sharing' . '">' . Kohana::lang('ui_main.sharing') . '</a>' ?>
					<a href="<?php echo url::site() . 'admin/settings/email' . '">' . Kohana::lang('ui_main.email') . '</a>' ?>
					<a href="<?php echo url::site() . 'admin/settings/themes' . '">' . Kohana::lang('ui_main.themes') . '</a>' ?>
					<a href="<?php echo url::site() . 'admin/settings/cleanurl'.'">' . Kohana::lang('ui_main.cleanurl').'</a>' ?>
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
						<a href="<?php echo url::base() . 'admin/settings/sms' ?>"><?php echo Kohana::lang('settings.sms.option_1');?></a>
						<a href="<?php echo url::base() . 'admin/settings/smsglobal' ?>" class="active"><?php echo Kohana::lang('settings.sms.option_2');?></a>
					</div>
		
					<div class="sms_holder">
						<table style="width: 630px;" class="my_table">
							<tr>
								<td style="width:60px;">
									<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 1:</span>
								</td>
								<td>
									<h4 class="fix"><?php echo Kohana::lang('settings.sms.clickatell_text_1');?>. <sup><a href="#">?</a></sup></h4>
								</td>
							</tr>
							<tr>
								<td>
									<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step');?> 2:</span>
								</td>
								<td>
									<h4 class="fix"><?php echo Kohana::lang('settings.sms.clickatell_text_2');?>. <sup><a href="#">?</a></sup></h4>
									<div class="row">
										<h4><?php echo Kohana::lang('settings.sms.clickatell_api');?>:</h4>
										<?php print form::input('clickatell_api', $form['clickatell_api'], ' class="text title_2"'); ?>
									</div>
									<div class="row">
										<h4><?php echo Kohana::lang('settings.sms.clickatell_username');?>:</h4>
										<?php print form::input('clickatell_username', $form['clickatell_username'], ' class="text title_2"'); ?>
									</div>
									<div class="row">
										<h4><?php echo Kohana::lang('settings.sms.clickatell_password');?>:</h4>
										<?php print form::password('clickatell_password', $form['clickatell_password'], ' class="text title_2"'); ?>
									</div>
								</td>
							</tr>
							<!--<tr>
								<td>
									<span class="big_blue_span">Step 3:</span>
								</td>
								<td>
									<h4 class="fix"><?php echo Kohana::lang('settings.sms.clickatell_check_balance');?>. <sup><a href="#">?</a></sup></h4>
									<div class="row">
										<h4><a href="javascript:clickatellBalance()"><?php echo Kohana::lang('settings.sms.clickatell_load_balance');?></a>&nbsp;<span id="balance_loading"></span></h4>
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