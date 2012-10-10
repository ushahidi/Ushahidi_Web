<?php
/**
 * Reports view page.
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
					<?php admin::reports_subtabs("view"); ?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li>
							<a href="?status=0" <?php if ($status != 'a' AND $status !='v' AND $status != 'o') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a>
						</li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_approval');?></a></li>
						<li><a href="?status=v" <?php if ($status == 'v') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_verification');?></a></li>
						<li>
							<a href="?status=o" <?php if ($status == 'o') echo "class=\"active\""; ?>>
								<?php echo Kohana::lang('ui_main.uncategorized_reports'); ?>
							</a>
						</li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<?php if (Auth::instance()->has_permission('reports_approve')): ?>
							<li><a href="#" onclick="reportAction('a','<?php echo utf8::strtoupper(Kohana::lang('ui_main.approve')); ?>', '');">
								<?php echo Kohana::lang('ui_main.approve');?></a>
							</li>
							<li><a href="#" onclick="reportAction('u','<?php echo utf8::strtoupper(Kohana::lang('ui_main.disapprove')); ?>', '');">
								<?php echo Kohana::lang('ui_main.disapprove');?></a>
							</li>
							<?php endif; ?>
							<?php if (Auth::instance()->has_permission('reports_verify')): ?>
							<li><a href="#" onclick="reportAction('v','<?php echo utf8::strtoupper(Kohana::lang('ui_admin.verify_unverify')); ?>', '');">
								<?php echo Kohana::lang('ui_admin.verify_unverify');?></a>
							</li>
							<?php endif; ?>
							<?php if (Auth::instance()->has_permission('reports_edit')): ?>
							<li><a href="#" onclick="reportAction('d','<?php echo utf8::strtoupper(Kohana::lang('ui_main.delete')); ?>', '');">
								<?php echo Kohana::lang('ui_main.delete');?></a>
							</li>
							<?php endif; ?>
						</ul>
						
						<div class="sort_by">
							<?php print form::open(NULL, array('method' => 'get', 'class' => 'sort-form')); ?>
							<?php echo Kohana::lang('ui_main.sort_by'); ?>
							<?php echo form::dropdown('order', array(
								'date' => Kohana::lang('ui_admin.report_date'),
								'id' => Kohana::lang('ui_main.id'),
								'datemodify' => Kohana::lang('ui_admin.date_modified'),
								'dateadd' => Kohana::lang('ui_admin.date_added'),
								'title' => Kohana::lang('ui_admin.report_title'),
							), $order_field); 
							echo form::input(array(
									'type'  => 'hidden',
									'name'  => 'sort',
									'value' => $sort,
									'class' => 'sort-field'
								));
							echo form::hidden('status', $status);
							echo form::close(); ?>
						</div>
					</div>
				</div>
				<?php if ($form_error): ?>
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
				<?php endif; ?>
				
				<?php if ($form_saved): ?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_main.reports');?> 
							<?php echo $form_action; ?> 
							<a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message'); ?></a>
						</h3>
					</div>
				<?php endif; ?>
				
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'reportMain', 'name' => 'reportMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="incident_id[]" id="incident_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1">
										<input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'incident_id[]' )" />
									</th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.report_details');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.date');?></th>
									<th class="col-4">
										<a class="sort sort-<?php echo $sort; ?>" title="<?php echo ($sort == 'ASC') ? Kohana::lang('ui_main.ascending') : Kohana::lang('ui_main.descending'); ?>" href="#"></a>
										<?php echo Kohana::lang('ui_main.actions');?>
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
							<?php if ($total_items == 0): ?>
								<tr>
									<td colspan="4" class="col">
										<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
									</td>
								</tr>
							<?php endif; ?>
							<?php
								foreach ($incidents as $incident)
								{
									$incident_id = $incident->incident_id;
									$incident_title = strip_tags($incident->incident_title);
									$incident_description = text::limit_chars(strip_tags($incident->incident_description), 150, "...", true);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y-m-d', strtotime($incident->incident_date));
									
									// Mode of submission... WEB/SMS/EMAIL?
									$incident_mode = $incident->incident_mode;
									
									// Get the incident ORM
									$incident_orm = ORM::factory('incident', $incident_id);
									
									// Get the person submitting the report
									$incident_person = $incident_orm->incident_person;
									$submit_by = NULL;
									
									//XXX incident_Mode will be discontinued in favour of $service_id
									if ($incident_mode == 1)	// Submitted via WEB
									{
										$submit_mode = "WEB";
										// Who submitted the report?
										if ($incident_person->loaded)
										{
											// Report was submitted by a visitor
											$submit_by = $incident_person->person_first . " " . $incident_person->person_last;
										}
										
										// If $submit_by is empty, check for user id
										if (trim($submit_by) == "" AND $incident_orm->user_id)	// Report Was Submitted By authenticated user
										{
											$submit_by = $incident_orm->user->name;
										}
									}
									elseif ($incident_mode == 2) 	// Submitted via SMS
									{
										$submit_mode = "SMS";
										$submit_by = $incident_orm->message->message_from;
									}
									elseif ($incident_mode == 3) 	// Submitted via Email
									{
										$submit_mode = "EMAIL";
										$submit_by = $incident_orm->message->message_from;
									}
									elseif ($incident_mode == 4) 	// Submitted via Twitter
									{
										$submit_mode = "TWITTER";
										$submit_by = $incident_orm->message->message_from;
									}
									
									// If $submit_by is still empty, set it to Unknown
									if (trim($submit_by) == "")
									{
										$submit_by = Kohana::lang('ui_admin.unknown');
									}
									
									// Incident location
									$incident_location = $incident->location_id ? $incident->location_name : Kohana::lang('ui_main.none');
									
									// Retrieve Incident Categories
									$incident_category = "";
									if ($incident_orm->incident_category->count() > 0)
									{
										foreach ($incident_orm->incident_category as $category)
										{
											$incident_category .= Category_Lang_Model::category_title($category->category_id) ."&nbsp;&nbsp;";
										}
									}
									else
									{
										$incident_category .= Kohana::lang('ui_main.none');
									}
									
									// Incident Status
									$incident_approved = $incident->incident_active;
									$incident_verified = $incident->incident_verified;
									
									// Get Edit Log
									$edit_count = $incident_orm->verify->count();
									$edit_css = ($edit_count == 0) ? "post-edit-log-gray" : "post-edit-log-blue";
									
									$edit_log  = "<div class=\"".$edit_css."\">"
										. "<a href=\"javascript:showLog('edit_log_".$incident_id."')\">".Kohana::lang('ui_admin.edit_log').":</a> (".$edit_count.")</div>"
										. "<div id=\"edit_log_".$incident_id."\" class=\"post-edit-log\"><ul>";
									
									foreach ($incident_orm->verify as $verify)
									{
										$edit_log .= "<li>".Kohana::lang('ui_admin.edited_by')." ".html::specialchars($verify->user->name)." : ".$verify->verified_date."</li>";
									}
									$edit_log .= "</ul></div>";

									// Get Any Translations
									$i = 1;
									$incident_translation  = "<div class=\"post-trans-new\">"
											. "<a href=\"" . url::base() . 'admin/reports/translate/?iid='.$incident_id."\">"
											. utf8::strtoupper(Kohana::lang('ui_main.add_translation')).":</a></div>";
											
									foreach ($incident_orm->incident_lang as $translation)
									{
										$incident_translation .= "<div class=\"post-trans\">"
											. Kohana::lang('ui_main.translation'). $i . ": "
											. "<a href=\"" . url::base() . 'admin/reports/translate/'. $translation->id .'/?iid=' . $incident_id . "\">"
											. text::limit_chars($translation->incident_title, 150, "...", TRUE)
											. "</a>"
											. "</div>";
									}
									?>
									<tr>
										<td class="col-1">
											<input name="incident_id[]" id="incident" value="<?php echo $incident_id; ?>" type="checkbox" class="check-box"/>
										</td>
										<td class="col-2">
											<div class="post">
												<div class="incident-id"><a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>" class="more">#<?php echo $incident_id; ?></a></div>
												<h4>
													<a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>" class="more">
														<?php echo $incident_title; ?>
													</a>
												</h4>
												<p><?php echo $incident_description; ?>... 
													<a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>" class="more">
														<?php echo Kohana::lang('ui_main.more');?>
													</a>
												</p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.location');?>: 
													<strong><?php echo html::specialchars($incident_location); ?></strong>
												</li>
												<li><?php echo Kohana::lang('ui_main.submitted_by', array($submit_by, $submit_mode));?>
												</li>
											</ul>
											<ul class="links">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.categories');?>:
													<strong><?php echo $incident_category;?></strong>
												</li>
											</ul>
											<?php
											echo $edit_log;
											
											// Action::report_extra_admin - Add items to the report list in admin
											Event::run('ushahidi_action.report_extra_admin', $incident);
											?>
										</td>
										<td class="col-3"><?php echo $incident_date; ?></td>
										<td class="col-4">
											<ul>
												<?php if (Auth::instance()->has_permission('reports_approve')): ?>
												<li class="none-separator">
													<?php if ($incident_approved): ?>
													<a href="#" class="status_yes" onclick="reportAction('u','UNAPPROVE', '<?php echo $incident_id; ?>');">
														<?php echo Kohana::lang('ui_main.approve');?>
													</a>
													<?php  else: ?>
													<a href="#" onclick="reportAction('a','APPROVE', '<?php echo $incident_id; ?>');">
														<?php echo Kohana::lang('ui_main.approve');?>
													</a>
													<?php endif; ?>	
												</li>
												<?php endif; ?>
												<?php if (Auth::instance()->has_permission('reports_verify')): ?>
												<li>
													<?php if ($incident_verified): ?>
													<a href="#" class="status_yes" 
													    onclick="reportAction('v','VERIFY', '<?php echo $incident_id; ?>');"><?php echo Kohana::lang('ui_main.verify');?>
													</a>
													<?php else: ?>
													<a href="#" onclick="reportAction('v','VERIFY', '<?php echo $incident_id; ?>');">
														<?php echo Kohana::lang('ui_main.verify');?>
													</a>
													<?php endif; ?>
												</li>
												<?php endif; ?>
												<?php if (Auth::instance()->has_permission('reports_edit')): ?>
												<li>
													<a href="#" class="del" onclick="reportAction('d','DELETE', '<?php echo $incident_id; ?>');">
														<?php echo Kohana::lang('ui_main.delete');?>
													</a>
												</li>
												<?php endif; ?>
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
				<div class="tabs">
					<div class="tab">
						<ul>
						<li><a href="#" onclick="reportAction('a','<?php echo utf8::strtoupper(Kohana::lang('ui_main.approve')); ?>', '');">
							<?php echo Kohana::lang('ui_main.approve');?></a>
						</li>
						<li><a href="#" onclick="reportAction('u','<?php echo utf8::strtoupper(Kohana::lang('ui_main.disapprove')); ?>', '');">
							<?php echo Kohana::lang('ui_main.disapprove');?></a>
						</li>
						<li><a href="#" onclick="reportAction('v','<?php echo utf8::strtoupper(Kohana::lang('ui_admin.verify_unverify')); ?>', '');">
							<?php echo Kohana::lang('ui_admin.verify_unverify');?></a>
						</li>
						<li><a href="#" onclick="reportAction('d','<?php echo utf8::strtoupper(Kohana::lang('ui_main.delete')); ?>', '');">
							<?php echo Kohana::lang('ui_main.delete');?></a>
						</li>
						</ul>
					</div>
				</div>
			</div>
