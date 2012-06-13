<?php
/**
 * Edit User
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Edit User View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
			<div class="bg">
				<h2>
					<?php admin::user_subtabs("users_edit", $display_roles); ?>
				</h2>
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
						<h3><?php echo Kohana::lang('ui_main.profile_saved');?></h3>
					</div>
				<?php
				}
				?>
				<?php print form::open(); ?>
				<div class="report-form">
					<div class="head">
						<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
					</div>
					<!-- column -->
					<div class="sms_holder">

						<div class="row">
							<h4><?php echo Kohana::lang('ui_main.full_name');?> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>
							<?php print form::input('name', $form['name'], ' class="text long2"'); ?>
						</div>

						<div class="row">
							<h4><?php echo Kohana::lang('ui_main.email');?> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>
							<?php print form::input('email', $form['email'], ' class="text long2"'); ?>
						</div>

						<div class="row">
							<h4><?php echo Kohana::lang('ui_main.role');?></h4>
							<?php
							if ($user AND $user->loaded AND $user->id == 1)
							{
								print form::dropdown('role', $role_array, $form['role'], ' readonly="readonly"');
							}
							else
							{
								print form::dropdown('role', $role_array, $form['role']);
							}
							?>
						</div>

						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.profile_public_url"); ?>"><?php echo Kohana::lang('ui_main.public_profile_url');?></a> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>
							<span style="float:left;"><?php echo url::base().'profile/user/'; ?></span>
							<?php print form::input('username', $form['username'], ' class="text short3"'); ?>
						</div>

						<div class="row">
							<h4><?php echo Kohana::lang('ui_main.receive_notifications');?>?</h4>
							<?php print form::dropdown('notify', $yesno_array, $form['notify']); ?>
						</div>

						<?php if ($user_id == FALSE) { ?>

						<div class="row">
							<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.profile_new_users_password"); ?>"><?php echo Kohana::lang('ui_main.password'); ?></a> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>
							<?php print form::password('password', '', ' class="text"'); ?>
						</div>

						<div class="row">
							<h4><?php echo Kohana::lang('ui_main.password_again');?></h4>
							<?php print form::password('password_again', $form['password_again'], ' class="text"'); ?>
						</div>

						<?php }elseif(kohana::config('riverid.enable') == FALSE){ ?>

						<div class="row">
							<h4><?php echo Kohana::lang('ui_admin.new_password');?></h4>
							<?php print form::password('new_password', '', ' class="text long2"'); ?>
						</div>

						<?php } ?>

                        <?php
                        // users_form_admin - add content to users from
                        Event::run('ushahidi_action.users_form_admin', $id);
                        ?>
					</div>

					<div class="simple_border"></div>

					<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_admin.save_settings');?>" />
				</div>
				<?php print form::close(); ?>
			</div>
