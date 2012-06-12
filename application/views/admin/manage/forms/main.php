<?php 
/**
 * Forms view page.
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
				<?php admin::manage_subtabs("forms"); ?>
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
					<h3><?php echo Kohana::lang('ui_main.form_has_been');?> <?php echo $form_action; ?>!</h3>
				</div>
			<?php
			}
			?>
			
			<!-- tabs -->
			<div class="tabs">
				<!-- tabset -->
				<a name="add"></a>
				<ul class="tabset">
					<li><a href="#" class="active" onclick="show_addedit(true)"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
				</ul>
				<!-- tab -->
				<div class="tab" id="addedit" style="display:none">
					<?php print form::open(NULL,array('id' => 'formMain',
					 	'name' => 'formMain')); ?>
					<input type="hidden" id="form_id" 
						name="form_id" value="<?php echo $form['form_id']; ?>" />
					<input type="hidden" id="form_active" 
						name="form_active" value="" />
					<input type="hidden" name="action" 
						id="action" value="a"/>
					<div class="tab_form_item">
						<strong><?php echo Kohana::lang('ui_main.form_title');?>:</strong><br />
						<?php print form::input('form_title', $form['form_title'], 
						' class="text"'); ?>
					</div>
					<div class="tab_form_item">
						<strong><?php echo Kohana::lang('ui_main.form_description');?>:</strong><br />
						<?php print form::input('form_description', $form['form_description'], ' class="text long"'); ?>
					</div>						
					<div class="tab_form_item">
						<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_main.save');?>" />
					</div>
					<?php print form::close(); ?>			
				</div>
			</div>
							
			<!-- report-table -->
			<div class="report-form">				
				<div class="table-holder">
					<table class="table">
						<thead>
							<tr>
								<th class="col-1">&nbsp;</th>
								<th class="col-2"><?php echo Kohana::lang('ui_main.form');?></th>
								<th class="col-3">&nbsp;</th>
								<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
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
										<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
									</td>
								</tr>
							<?php	
							}
							foreach ($forms as $form)
							{
								$form_id = $form->id;
								$form_title = $form->form_title;
								$form_description = $form->form_description;
								$form_active = $form->form_active;
								
								$fields = ORM::factory('form_field')
									->where('form_id', $form_id)
									->orderby('field_position', 'asc')
									->orderby('id', 'asc')
									->find_all();

								$form_fields = form::open(NULL, array('method' => 'get'));
								foreach ($fields as $field)
								{
									$field_id = $field->id;
									$field_name = $field->field_name;
									$field_default = $field->field_default;
									$field_required = $field->field_required;
									$field_width = $field->field_width;
									$field_height = $field->field_height;
									$field_maxlength = $field->field_maxlength;
									$field_position = $field->field_position;
									$field_type = $field->field_type;
									$field_isdate = $field->field_isdate;

									$form_fields .= "<div class=\"forms_fields_item\">";
									$form_fields .= "	<strong>".$field_name.":</strong><br />";
									if ($field_type == 1)
									{
										$form_fields .= form::input("custom_".$field_id, '', '');
									}
									elseif ($field_type == 2)
									{
										$form_fields .= form::textarea("custom_".$field_id, '');
									}
									if ($field_isdate == 1) 
									{
										$form_fields .= "&nbsp;<a href=\"#\"><img src = \"".url::file_loc('img')."media/img/icon-calendar.gif\"  align=\"middle\" border=\"0\"></a>";
									}
									$form_fields .= "	<div class=\"forms_fields_edit\">
									<a href=\"javascript:fieldAction('e','EDIT',".$field_id.",".$form_id.",".$field_type.");\">EDIT</a>&nbsp;|&nbsp;
									<a href=\"javascript:fieldAction('d','DELETE',".$field_id.",".$form_id.",".$field_type.");\">DELETE</a>&nbsp;|&nbsp;
									<a href=\"javascript:fieldAction('mu','MOVE',".$field_id.",".$form_id.",".$field_type.");\">MOVE UP</a>&nbsp;|&nbsp;
									<a href=\"javascript:fieldAction('md','MOVE',".$field_id.",".$form_id.",".$field_type.");\">MOVE DOWN</a></div>";
									$form_fields .= "</div>";
								}
								$form_fields .= form::close();
								?>
								<?php print form::open(NULL,array('id' => 'form_action_' . $form_id,
								 	'name' => 'form_action_' . $form_id )); ?>
									<input type="hidden" name="action" id="action_<?php echo $form_id;?>" value="">
									<input type="hidden" name="form_id" value="<?php echo $form_id;?>">
									<tr id="tr_<?php echo $form_id; ?>">
										<td class="col-1">&nbsp;</td>
										<td class="col-2">
											<div class="post">
												<h4><?php echo $form_title; ?>&nbsp;&nbsp;&nbsp;[<a href="javascript:showForm('formDiv_<?php echo $form_id; ?>')"><?php echo Kohana::lang('ui_main.edit_form_fields');?></a>]</h4>
												<p><?php echo $form_description; ?></p>
											</div>
										</td>
										<td>&nbsp;</td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($form_id)); ?>','<?php echo(rawurlencode($form_title)); ?>','<?php echo(rawurlencode($form_description)); ?>')">Edit</a></li>
												<li class="none-separator"><a href="javascript:formAction('h','SHOW/HIDE','<?php echo(rawurlencode($form_id)); ?>')"<?php if ($form_active) echo " class=\"status_yes\"" ?>><?php echo Kohana::lang('ui_main.active');?></a></li>
												<li><a href="javascript:formAction('d','DELETE','<?php echo(rawurlencode($form_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
											</ul>
										</td>
									</tr>
								<?php print form::close(); ?>
								<tr style="margin:0;padding:0;border-width:0;">
									<td colspan="4" style="margin:0;padding:0;border-width:0;">
										<div id="formDiv_<?php echo $form_id; ?>" class="forms_fields">
											<a href="javascript:addNewForm('<?php echo $form_id; ?>')" class="new-form_field">Add New Field</a>
											<div id="formadd_<?php echo $form_id; ?>" class="forms_fields_add">
												<div class="tab">
													<div>
														<?php echo form::open(url::site() . 'admin/manage/forms/field_add', array('id' => 'form_field_'.$form_id,
																'name' => 'form_field_'.$form_id)); ?>
															<strong><?php echo Kohana::lang('ui_main.select_field_type');?>:</strong>
															<?php print form::dropdown('field_type',$form_field_types, '', ' onchange="showFormSelected(this.options[this.selectedIndex].value, \''.$form_id.'\', \'\', \'\')"'); ?>
															<div id="form_fields_<?php echo $form_id; ?>" class="forms_fields_new"></div>
														<?php echo form::close(); ?>
													</div>		
												</div>
											</div>
											<div id="form_fields_current_<?php echo $form_id; ?>" class="forms_fields_current">
												<?php echo $form_fields; ?>
											</div>
										</div>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>

		</div>
