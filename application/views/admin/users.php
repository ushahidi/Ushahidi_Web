<?php 
/**
 * Users view page.
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
				<h2><?php echo Kohana::lang('ui_admin.title')?></h2>
				<!-- tabs -->
				<div class="tabs">
			
				
					<!-- tabset -->
					<ul class="tabset">
						<li>
							<a href="#" class="active">
								<?php echo Kohana::lang('ui_admin.header_add_edit'); ?>
							</a>
						</li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'userMain',
						 	'name' => 'userMain')); ?>
						<input type="hidden" id="user_id" name="user_id" value="<?php echo $form['user_id']; ?>">
						<input type="hidden" name="action" id="action" value="a">
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_admin.label_username');?></strong><br />
							<?php print form::input('username', $form['username'], 
								' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_admin.label_password');?></strong><br />
							<?php print form::password('password', $form['password'], 
								' class="text"'); ?>
						</div>
						
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_admin.label_full_name');?></strong><br />
							<?php print form::input('name', $form['name'], ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_admin.label_email');?></strong><br />
							<?php print form::input('email', $form['email'], ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_admin.label_role');?></strong><br />
							<span class="my-sel-holder">
								<?php print form::dropdown('role',
									$roles,$form['role']); ?>
							</span>
						</div>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>			
					</div>
				</div>
				<!-- report-table -->
				<div class="report-form">
					<?php
					if ($form_error) {
						
					?>
						<!-- red-box -->
						<div class="red-box">
							<h3><?php echo Kohana::lang('ui_admin.error_msg');?></h3>
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
							<h3>
								<?php echo Kohana::lang('ui_admin.confirm_msg'); ?> 
								<?php echo $form_action; ?>!
							</h3>
						</div>
					<?php
					}
					?>
					<!-- report-table -->
					<?php print form::open(); ?>
						<input type="hidden" name="action" id="action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">
											<?php echo Kohana::lang('ui_admin.header_user'); ?>
										</th>
										<th class="col-2">
											<?php echo Kohana::lang('ui_admin.header_email'); ?>
										</th>
										<th class="col-2">
											<?php echo Kohana::lang('ui_admin.header_role');?>
										</th>
										<th class="col-4">
											<?php echo Kohana::lang('ui_admin.header_actions')?>
										</th>
									</tr>
								</thead>
								<tfoot>
									<tr class="foot">
										<td colspan="4">
											<?php echo $pagination; ?>
										</td>
									</tr>
								</tfoot>
								<tbody>
									<?php
									if ($total_items == 0)
									{
									?>
										<tr>
											<td colspan="4" class="col">
												<h3>
													<?php echo Kohana::lang('ui_admin.no_result_display_msg');?>
												</h3>
											</td>
										</tr>
									<?php	
									}
									foreach ($users as $user)
									{
										$user_id = $user->id;
										$username = $user->username;
										$password = $user->password;
										$name = $user->name;
										$email = $user->email;
										
										foreach ($user->roles as $user_role) {
											$role = $user_role->name;
										}
										?>
										<tr>
											
											<td class="col-1">
												<div class="post">
													<h4><?php echo $name; ?> (<?php echo $username; ?>)</h4>
												</div>
											</td>
											<td class="col-2">
												<?php echo $email; ?>
											</td>
											<td class="col-3">
												<?php  
													if( $role == "admin") 
														echo Kohana::lang('ui_admin.admin_role');
													else if ($role == "login")
														echo Kohana::lang('ui_admin.login_role');
													else 
														echo Kohana::lang('ui_admin.superadmin_role');
												?>
											</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#" 
														onClick="fillFields(
														'<?php echo(rawurlencode($user_id)); ?>',
    													'<?php echo(rawurlencode($username)); ?>',
														'<?php echo(rawurlencode($name)); ?>',
														'<?php echo(rawurlencode($role));?>',
														'<?php echo(rawurlencode($email)); ?>')">
														<?php echo Kohana::lang('ui_admin.edit_action');?>
														</a></li>
		<li><a href="javascript:userAction('d','DELETE','<?php echo(rawurlencode($user_id)); ?>')" class="del">
														<?php echo Kohana::lang('ui_admin.delete_action');?>
														</a></li>
												</ul>
											</td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>
					<?php print form::close(); ?>
				</div>
			</div>