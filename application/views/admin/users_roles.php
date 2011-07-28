<?php 
/**
 * Roles view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Roles View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
		<div class="bg">
			<h2>
				<?php admin::user_subtabs("roles", $display_roles); ?>
			</h2>

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
						<?php echo $form_action; ?>!
					</h3>
				</div>
			<?php
			}
			?>
				
			<!-- tabs -->
			<div class="tabs">
				<a name="add"></a>
				<!-- tabset -->
				<ul class="tabset">
					<li>
						<a href="#" class="active" onclick="show_addedit(true)">
							<?php echo Kohana::lang('ui_admin.header_add_edit'); ?>
						</a>
					</li>
				</ul>
				<!-- tab -->
				<div class="tab" id="addedit" style="display:none">
					<?php print form::open(NULL,array('id' => 'rolesMain',
					 	'name' => 'rolesMain')); ?>
					<input type="hidden" name="action" id="action" value="a"/>
					<input type="hidden" id="role_id" name="role_id" value="">
					<div class="tab_form_item">
						<strong><?php echo Kohana::lang('ui_main.name');?>:</strong><br />
						<?php print form::input('name', '', ' class="text"'); ?>
					</div>
					<div class="tab_form_item">
						<strong><?php echo Kohana::lang('ui_main.description');?>:</strong><br />
						<?php print form::input('description', '', ' class="text long"'); ?>
					</div>
					<div class="tab_form_item">
						<strong>
							<a href="#" class="tooltip" style="background-position-y:0px" title="<?php echo Kohana::lang("tooltips.settings_access_level"); ?>">
								<?php echo  Kohana::lang('ui_admin.access_level'); ?>: </a></h4>
						<?php print form::input('access_level','', ' class="text"'); ?>
					</div>
					<div style="clear:both;"></div>
					<div class="tab_form_item">
					<?php
					$i = 0;
					foreach ($permissions as $permission => $permission_desc)
					{
						echo "<div style=\"width:200px;float:left;margin-bottom:3px;\">";
						echo form::checkbox($permission, '1');
						echo form::label($permission, $permission_desc);
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "</div>";
						$i++;
					}
					?>
					</div>
					<div style="clear:both;"></div>
					<div class="tab_form_item">
						&nbsp;<br />
						<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<?php print form::close(); ?>			
				</div>
			</div>			
			
			
			<!-- report-table -->
			<div class="report-form">
				<?php print form::open(NULL,array('id' => 'roleListing',
				 	'name' => 'roleListing')); ?>
					<input type="hidden" name="action" id="role_action_main" value="">
					<input type="hidden" name="role_id" id="role_id_main" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1">
										&nbsp;
									</th>
									<th class="col-2">
										<?php echo Kohana::lang('ui_admin.header_role'); ?>
									</th>
									<th class="col-2">
										&nbsp;
									</th>
									<th class="col-4">
										<?php echo Kohana::lang('ui_admin.header_actions')?>
									</th>
								</tr>
							</thead>
							<tfoot>
								<tr class="foot">
									<td colspan="4">
										&nbsp;
									</td>
								</tr>
							</tfoot>
							<tbody>
								<?php
								foreach ($roles as $role)
								{
									$role_id = $role->id;
									$name = $role->name;
									$description = $role->description;
									
									$reports_view = $role->reports_view;
									$reports_edit = $role->reports_edit;
									$reports_evaluation = $role->reports_evaluation;
									$reports_comments = $role->reports_comments;
									$reports_download = $role->reports_download;
									$reports_upload = $role->reports_upload;
									$messages = $role->messages;
									$messages_reporters = $role->messages_reporters;
									$stats = $role->stats;
									$settings = $role->settings;
									$manage = $role->manage;
									$users = $role->users;
									$access_level = $role->access_level;
									?>
									<tr>
										
										<td class="col-1">
											&nbsp;
										</td>
										<td class="col-2">
											<div class="post">
												<h4><?php echo strtoupper($name); ?></h4>
												<p><?php echo $description; ?></p>
											</div>
										</td>
										<td class="col-3">&nbsp;</td>
										<td class="col-4">
											<?php if($role_id == 1 OR $role_id == 3 OR $role->name == "member") { echo "&nbsp;";
											
											} else {?>
											<ul>
												<li class="none-separator"><a href="#" 
													onClick="fillFields(
													'<?php echo(rawurlencode($role_id)); ?>',
													'<?php echo(rawurlencode($name)); ?>',
													'<?php echo(rawurlencode($description)); ?>',
													'<?php echo(rawurlencode($access_level)); ?>',
													<?php echo(rawurlencode($reports_view)); ?>,
													<?php echo(rawurlencode($reports_edit)); ?>,
													<?php echo(rawurlencode($reports_evaluation)); ?>,
													<?php echo(rawurlencode($reports_comments)); ?>,
													<?php echo(rawurlencode($reports_download)); ?>,
													<?php echo(rawurlencode($reports_upload)); ?>,
													<?php echo(rawurlencode($messages)); ?>,
													<?php echo(rawurlencode($messages_reporters)); ?>,
													<?php echo(rawurlencode($stats)); ?>,
													<?php echo(rawurlencode($settings)); ?>,
													<?php echo(rawurlencode($manage)); ?>,
													<?php echo(rawurlencode($users)); ?>
													)">
													<?php echo Kohana::lang('ui_admin.edit_action');?>
													</a></li>
	<li><a href="javascript:rolesAction('d','DELETE','<?php echo(rawurlencode($role_id)); ?>')" class="del">
													<?php echo Kohana::lang('ui_admin.delete_action');?>
													</a></li>
											</ul>
											<?php } ?>
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
