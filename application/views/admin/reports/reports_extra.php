<?php 
/**
 * Reports extra view page.
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
				<h2><?php echo $title; ?><span>(<?php echo $total_items; ?>)</span><a href="reports/edit"><?php echo Kohana::lang('ui_main.create_report');?></a><a href="reports/extra"><?php echo Kohana::lang('ui_main.additional_reports');?></a></h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?status=0" <?php if ($status != 'a' && $status !='v') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_approval');?></a></li>
						<li><a href="?status=v" <?php if ($status == 'v') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_verification');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onclick="reportAction('a','APPROVE');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.appove'));?></a></li>
							<li><a href="#" onclick="reportAction('u','UNAPPROVE');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.disapprove'));?></a></li>
							<li><a href="#" onclick="reportAction('v','VERIFY');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.verify'));?></a></li>
							<li><a href="#" onclick="reportAction('d','DELETE');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
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
						<h3>Reports <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'reportMain', 'name' => 'reportMain')); ?>
					<input type="hidden" name="action" id="action" value="">
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
									$incident_title = $incident->incident_title;
									$incident_description = substr($incident->incident_description, 0, 150);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y-m-d', strtotime($incident->incident_date));
									$incident_mode = $incident->incident_mode;	// Mode of submission... WEB/SMS/EMAIL?
									
									if ($incident_mode == 1)
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
												$submit_by = Kohana::lang('ui_admin.unknown');
											}
										}
									}
									
									$incident_location = $incident->location->location_name;
									$incident_country = $incident->location->country->country;

									// Retrieve Incident Categories
									$incident_category = "";
									foreach($incident->incident_category as $category) 
									{ 
										$incident_category .= "<a href=\"#\">" . $category->category->category_title . "</a>&nbsp;&nbsp;";
									}
									
									// Incident Status
									$incident_approved = $incident->incident_active;
									$incident_verified = $incident->incident_verified;
									?>
									<tr>
										<td class="col-1"><input name="incident_id[]" id="incident" value="<?php echo $incident_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<h4><a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>" class="more"><?php echo $incident_title; ?></a></h4>
												<p><?php echo $incident_description; ?>... <a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>" class="more"><?php echo Kohana::lang('ui_main.more');?></a></p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.location');?>: <strong><?php echo $incident_location; ?></strong>, <strong><?php echo Kohana::lang('ui_main.example_country');?></strong></li>
												<li><?php echo Kohana::lang('ui_main.submitted_by');?> <strong><?php echo $submit_by; ?></strong> via <strong><?php echo $submit_mode; ?></strong></li>
											</ul>
											<ul class="links">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.categories');?>:<?php echo $incident_category; ?></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $incident_date; ?></td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><a href="#"<?php if ($incident_approved) echo " class=\"status_yes\"" ?> onclick="reportAction('a','APPROVE');"><?php echo Kohana::lang('ui_main.approve');?></a></li>
												<li><a href="#"<?php if ($incident_verified) echo " class=\"status_yes\"" ?> onclick="reportAction('v','VERIFY');"><?php echo Kohana::lang('ui_main.verify');?></a></li>
												<li><a href="#" class="del" onclick="reportAction('d','DELETE');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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