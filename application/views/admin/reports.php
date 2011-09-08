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
						<li><a href="?status=0" <?php if ($status != 'a' AND $status !='v') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_approval');?></a></li>
						<li><a href="?status=v" <?php if ($status == 'v') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_verification');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onclick="reportAction('a','<?php echo strtoupper(Kohana::lang('ui_main.approve')); ?>', '');">
								<?php echo Kohana::lang('ui_main.approve');?></a>
							</li>
							<li><a href="#" onclick="reportAction('u','<?php echo strtoupper(Kohana::lang('ui_main.disapprove')); ?>', '');">
								<?php echo Kohana::lang('ui_main.disapprove');?></a>
							</li>
							<li><a href="#" onclick="reportAction('v','<?php echo strtoupper(Kohana::lang('ui_admin.verify_unverify')); ?>', '');">
								<?php echo Kohana::lang('ui_admin.verify_unverify');?></a>
							</li>
							<li><a href="#" onclick="reportAction('d','<?php echo strtoupper(Kohana::lang('ui_main.delete')); ?>', '');">
								<?php echo Kohana::lang('ui_main.delete');?></a>
							</li>
						</ul>
					</div>
				</div>
				<?php
				if ($form_error) {
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_main.reports');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'reportMain', 'name' => 'reportMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="incident_id[]" id="incident_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'incident_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.report_details');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.date');?></th>
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
								foreach ($incidents as $incident)
								{
									$incident_id = $incident->id;
									$incident_title = strip_tags($incident->incident_title);
									$incident_description = text::limit_chars(strip_tags($incident->incident_description), 150, "...", true);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y-m-d', strtotime($incident->incident_date));
									
									// Mode of submission... WEB/SMS/EMAIL?
									$incident_mode = $incident->incident_mode;								

									//XXX incident_Mode will be discontinued in favour of $service_id
									if ($incident_mode == 1)	// Submitted via WEB
									{
										$submit_mode = "WEB";
										// Who submitted the report?
										if ($incident->incident_person->id)
										{
											// Report was submitted by a visitor
											$submit_by = $incident->incident_person->person_first . " " . $incident->incident_person->person_last;
										}
										else
										{
											if ($incident->user_id)					// Report Was Submitted By Administrator
											{
												$submit_by = $incident->user->name;
											}
											else
											{
												$submit_by = 'Unknown';
											}
										}
									}
									elseif ($incident_mode == 2) 	// Submitted via SMS
									{
										$submit_mode = "SMS";
										$submit_by = $incident->message->message_from;
									}
									elseif ($incident_mode == 3) 	// Submitted via Email
									{
										$submit_mode = "EMAIL";
										$submit_by = $incident->message->message_from;
									}
									elseif ($incident_mode == 4) 	// Submitted via Twitter
									{
										$submit_mode = "TWITTER";
										$submit_by = $incident->message->message_from;
									}

									$incident_location = $locations[$incident->location_id];
									$country_id = $country_ids[$incident->location_id]['country_id'];
							
									// Retrieve Incident Categories
									$incident_category = "";
									foreach($incident->incident_category as $category)
									{
										$incident_category .= "<a href=\"#\">" . $category->category->category_title . "</a>&nbsp;&nbsp;";
									}

									// Incident Status
									$incident_approved = $incident->incident_active;
									$incident_verified = $incident->incident_verified;
									
									// Get Edit Log
									$edit_count = $incident->verify->count();
									$edit_css = ($edit_count == 0) ? "post-edit-log-gray" : "post-edit-log-blue";
									$edit_log  = "<div class=\"".$edit_css."\">";
									$edit_log .= "<a href=\"javascript:showLog('edit_log_".$incident_id."')\">".Kohana::lang('ui_admin.edit_log').":</a> (".$edit_count.")</div>";
									$edit_log .= "<div id=\"edit_log_".$incident_id."\" class=\"post-edit-log\"><ul>";
									foreach ($incident->verify as $verify)
									{
										$edit_log .= "<li>".Kohana::lang('ui_admin.edited_by')." ".$verify->user->name." : ".$verify->verified_date."</li>";
									}
									$edit_log .= "</ul></div>";

									// Get Any Translations
									$i = 1;
									$incident_translation  = "<div class=\"post-trans-new\">";
									$incident_translation .= "<a href=\"" . url::base() . 'admin/reports/translate/?iid=' . $incident_id . "\">".strtoupper(Kohana::lang('ui_main.add_translation')).":</a></div>";
									foreach ($incident->incident_lang as $translation) {
										$incident_translation .= "<div class=\"post-trans\">";
										$incident_translation .= Kohana::lang('ui_main.translation'). $i . ": ";
										$incident_translation .= "<a href=\"" . url::base() . 'admin/reports/translate/'. $translation->id .'/?iid=' . $incident_id . "\">"
											. text::limit_chars($translation->incident_title, 150, "...", true)
											. "</a>";
										$incident_translation .= "</div>";
									}
									?>
									<tr>
										<td class="col-1"><input name="incident_id[]" id="incident" value="<?php echo $incident_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<h4><a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>" class="more"><?php echo $incident_title; ?></a></h4>
												<p><?php echo $incident_description; ?>... <a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>" class="more"><?php echo Kohana::lang('ui_main.more');?></a></p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.location');?>: <strong><?php echo $incident_location; ?></strong>,<strong><?php if ($country_id !=0) { echo $countries[$country_id];} else { echo $countries[Kohana::config('settings.default_country')];}?></strong></li>
												<li><?php echo Kohana::lang('ui_main.submitted_by');?> <strong><?php echo $submit_by; ?></strong> via <strong><?php echo $submit_mode; ?></strong></li>
											</ul>
											<ul class="links">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.categories');?>:<?php echo $incident_category; ?></li>
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
												<li class="none-separator"><a href="#"<?php if ($incident_approved) echo " class=\"status_yes\"" ?> onclick="reportAction('a','APPROVE', '<?php echo $incident_id; ?>');"><?php echo Kohana::lang('ui_main.approve');?></a></li>
												<li><a href="#"<?php if ($incident_verified) echo " class=\"status_yes\"" ?> onclick="reportAction('v','VERIFY', '<?php echo $incident_id; ?>');"><?php echo Kohana::lang('ui_main.verify');?></a></li>
												<li><a href="#" class="del" onclick="reportAction('d','DELETE', '<?php echo $incident_id; ?>');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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
