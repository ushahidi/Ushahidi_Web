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
				<h2><?php echo $title; ?> <span>(<?php echo $total_items; ?>)</span><a href="<?php print url::base() ?>admin/reports/edit">Create New Report</a><a href="<?php print url::base() ?>admin/reports/download">Download Reports</a><a href="<?php print url::base() ?>admin/reports/upload">Upload Reports</a></h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?status=0" <?php if ($status != 'a' && $status !='v') echo "class=\"active\""; ?>>Show All</a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>>Awaiting Approval</a></li>
						<li><a href="?status=v" <?php if ($status == 'v') echo "class=\"active\""; ?>>Awaiting Verification</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onclick="reportAction('a','APPROVE', '');">APPROVE</a></li>
							<li><a href="#" onclick="reportAction('u','UNAPPROVE', '');">UNAPPROVED</a></li>
							<li><a href="#" onclick="reportAction('v','VERIFY', '');">VERIFY</a></li>
							<li><a href="#" onclick="reportAction('d','DELETE', '');">DELETE</a></li>
						</ul>
					</div>
				</div>
				<?php
				if ($form_error) {
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3>Error!</h3>
						<ul>Please verify that you have checked an item</ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3>Reports <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
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
									<th class="col-2">Report Details</th>
									<th class="col-3">Date</th>
									<th class="col-4">Actions</th>
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
											<h3>No Results To Display!</h3>
										</td>
									</tr>
								<?php	
								}
								foreach ($incidents as $incident)
								{
									$incident_id = $incident->id;
									$incident_title = $incident->incident_title;
									$incident_description = text::limit_chars($incident->incident_description, 150, "...", true);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y-m-d', strtotime($incident->incident_date));
									$incident_mode = $incident->incident_mode;	// Mode of submission... WEB/SMS/EMAIL?
									
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
									elseif ($incident_mode == 5) 	// Submitted via Laconica
									{
										$submit_mode = "LACONICA";
										$submit_by = $incident->message->message_from;
									}
									
									$incident_location = $incident->location->location_name;

									// Retrieve Incident Categories
									$incident_category = "";
									foreach($incident->incident_category as $category) 
									{ 
										$incident_category .= "<a href=\"#\">" . $category->category->category_title . "</a>&nbsp;&nbsp;";
									}
									
									// Incident Status
									$incident_approved = $incident->incident_active;
									$incident_verified = $incident->incident_verified;
									
									// Get Any Translations
									$i = 1;
									$incident_translation  = "<div class=\"post-trans-new\">";
									$incident_translation .= "<a href=\"" . url::base() . 'admin/reports/translate/?iid=' . $incident_id . "\">+ADD TRANSLATION:</a></div>";
									foreach ($incident->incident_lang as $translation) {
										$incident_translation .= "<div class=\"post-trans\">";
										$incident_translation .= "Translation " . $i . ": ";
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
												<h4><a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>" class="more"><?php echo $incident_title; ?></a></h4>
												<p><?php echo $incident_description; ?>... <a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>" class="more">more</a></p>
											</div>
											<ul class="info">
												<li class="none-separator">Location: <strong><?php echo $incident_location; ?></strong>, <strong><?php echo $countries[Kohana::config('settings.default_country')]; ?></strong></li>
												<li>Submitted by <strong><?php echo $submit_by; ?></strong> via <strong><?php echo $submit_mode; ?></strong></li>
											</ul>
											<ul class="links">
												<li class="none-separator">Categories:<?php echo $incident_category; ?></li>
											</ul>
											<?php
											//XXX DISABLED Until Completed
											// echo $incident_translation;
											?>
										</td>
										<td class="col-3"><?php echo $incident_date; ?></td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><a href="#"<?php if ($incident_approved) echo " class=\"status_yes\"" ?> onclick="reportAction('a','APPROVE', '<?php echo $incident_id; ?>');">Approve</a></li>
												<li><a href="#"<?php if ($incident_verified) echo " class=\"status_yes\"" ?> onclick="reportAction('v','VERIFY', '<?php echo $incident_id; ?>');">Verify</a></li>
												<li><a href="#" class="del" onclick="reportAction('d','DELETE', '<?php echo $incident_id; ?>');">Delete</a></li>
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
