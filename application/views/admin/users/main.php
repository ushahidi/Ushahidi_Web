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
				<h2>
					<?php admin::user_subtabs("users", $display_roles); ?>
				</h2>
				<!-- report-table -->
				<div class="report-form">
					<!-- report-table -->
					<?php print form::open(NULL,array('id' => 'userMain', 'name' => 'userMain')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="user_id_action" id="user_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">
											&nbsp;
										</th>
										<th class="col-2">
											<?php echo Kohana::lang('ui_admin.header_user'); ?>
										</th>

										<?php
											//Add header item to users listing page
											Event::run('ushahidi_action.users_listing_header');
										?>

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

										// Show the highest role, defaulting to "none"
										$role = Kohana::lang('ui_main.none');
										foreach ($user->roles as $user_role) {
											$role = $user_role->name;
										}
										?>
										<tr>

											<td class="col-1">
												&nbsp;
											</td>
											<td class="col-2">
												<div class="post">
													<h4>
														<a href="<?php echo url::site() . 'admin/users/edit/' . $user_id; ?>">
															<?php echo html::specialchars($name); ?>
														</a>
													</h4>
												</div>
												<ul class="info">
													<li class="none-separator">
														<?php echo Kohana::lang('ui_main.email');?>: <strong><?php echo html::specialchars($email); ?></strong>
													</li>
												</ul>
											</td>
																						<?php
												//Add item to users listing page
												Event::run('ushahidi_action.users_listing_item',$user_id);
											?>

											<td class="col-3">
												<?php echo utf8::strtoupper($role); ?>
											</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="<?php echo url::site() . 'admin/users/edit/' . $user_id; ?>">
														<?php echo Kohana::lang('ui_admin.edit_action');?>
														</a></li>
		<?php if($user_id != 1) { ?>
		<li><a href="javascript:userAction('d','DELETE','<?php echo(rawurlencode($user_id)); ?>')" class="del">
														<?php echo Kohana::lang('ui_admin.delete_action');?>
														</a></li>
		<?php } ?>
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
