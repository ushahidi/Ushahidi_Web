<?php
/**
 *  Reports view page.
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

				<div id="content">
					<div class="content-bg">
						<!-- start reports block -->
						<div class="big-block">

							<div id="report_stats">
								<table>
									<tr>
										<th>Total Reports</th>
										<th>Avg Reports Per Day</th>
										<th>% Verified</th>
									</tr>
									<tr>
										<td><?php echo $total_reports; ?></td>
										<td><?php echo $avg_reports_per_day; ?></td>
										<td><?php echo $percent_verified; ?></td>
									</tr>
								</table>
							</div>

							<h1><?php echo Kohana::lang('ui_main.reports').": ";?> <?php echo ($category_title) ? " in $category_title" : ""?>
								<?php echo $pagination_stats; ?></h1>

							<div style="clear:both;"></div>

							<div class="report_rowtitle">
								<div class="report_col1">
									<strong><?php echo strtoupper(Kohana::lang('ui_main.media'));?></strong>
								</div>
								<div class="report_col2">
									<strong><?php echo strtoupper(Kohana::lang('ui_main.report_title'));?></strong>
								</div>
								<div class="report_col3">
									<strong><?php echo strtoupper(Kohana::lang('ui_main.date'));?></strong>
								</div>
								<div class="report_col4">
									<strong><?php echo strtoupper(Kohana::lang('ui_main.location'));?></strong>
								</div>
								<div class="report_col5">
									<strong><?php echo strtoupper(Kohana::lang('ui_main.verified'));?>?</strong>
								</div>
							</div>
							<?php
							foreach ($incidents as $incident)
							{
								$incident_id = $incident->id;
								$incident_title = $incident->incident_title;
								$incident_description = $incident->incident_description;

								// Trim to 150 characters without cutting words
								// XXX: Perhaps delcare 150 as constant

								$incident_description = text::limit_chars($incident_description, 150, "...", true);
								$incident_date = date('Y-m-d', strtotime($incident->incident_date));
								$incident_location = $incident->location_name;
								$incident_verified = $incident->incident_verified;
								if ($incident_verified)
								{
									$incident_verified = '<span class="report_yes">YES</span>';
								}else{
									$incident_verified = '<span class="report_no">NO</span>';
								}
							?>

							<div class="report_row1">

								<div class="report_thumb report_col1">
									&nbsp<?php if(isset($media_icons[$incident_id])) echo $media_icons[$incident_id]; ?>
								</div>

								<div class="report_details report_col2">
									<h3><a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>"><?php echo $incident_title; ?></a></h3>
									<?php echo $incident_description; ?>
								</div>

								<div class="report_date report_col3">
									<?php echo $incident_date; ?>
								</div>

								<div class="report_location report_col4">
									<?php echo $incident_location; ?>
								</div>

								<div class="report_status report_col5">
									<?php echo $incident_verified; ?>
								</div>

							</div>
							<?php } ?>

							<?php echo $pagination; ?>

						</div>
						<!-- end reports block -->
					</div>
				</div>
			</div>
		</div>
	</div>
