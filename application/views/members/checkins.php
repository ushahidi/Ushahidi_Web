<?php 
/**
 * Checkins view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Checkins View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php echo Kohana::lang('ui_admin.my_checkins'); ?>
				</h2>

				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.show_all');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onClick="checkinAction('d', 'DELETE', '')"><?php echo strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
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
						<h3><?php echo Kohana::lang('ui_admin.checkins');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'checkinMain', 'name' => 'checkinMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="level"  id="level"  value="">
					<input type="hidden" name="checkin_id[]" id="checkin_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkall" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'checkin_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_admin.checkin_details');?></th>
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
								foreach ($checkins as $checkin)
								{
									$checkin_id = $checkin->id;
									$incident_id = $checkin->incident_id;
									$location = $checkin->location->location_name;
									$latitude = $checkin->location->latitude;
									$longitude = $checkin->location->longitude;
									$description = $checkin->checkin_description;
									$preview = text::limit_chars(strip_tags($description), 150, "...", true);
									
									$checkin_date = date('Y-m-d h:i', strtotime($checkin->checkin_date));
									$auto_checkin = ($checkin->checkin_auto) ? "YES" : "NO";
									?>
									<tr>
										<td class="col-1"><input name="checkin_id[]" id="checkin" value="<?php echo $checkin_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<p><?php echo $preview; ?></p>
												<p><a href="javascript:showCheckin('checkin_preview_<?php echo $checkin_id?>', '<?php echo $longitude?>', '<?php echo $latitude?>')"><?php echo Kohana::lang('ui_admin.preview');?></a></p>
												<div id="checkin_preview_<?php echo $checkin_id?>" class="preview_div">
													<?php echo $description; ?>
													<div id="checkin_preview_<?php echo $checkin_id?>_map" class="checkin_map"></div>
													<?php
													// Retrieve Attachments if any
													foreach($checkin->media as $photo) 
													{
														if ($photo->media_type == 1)
														{
															print "<div class=\"attachment_thumbs\" style=\"margin-top:15px\" id=\"photo_". $photo->id ."\">";

															$thumb = $photo->media_thumb;
															$photo_link = $photo->media_link;
															$prefix = url::base().Kohana::config('upload.relative_directory');
															print "<a class='photothumb' rel='lightbox-group".$checkin_id."' href='$prefix/$photo_link'>";
															print "<img src=\"$prefix/$thumb\" border=\"0\" >";
															print "</a>";
															print "</div>";
														}
													}
													?>
													<div style="clear:both;"></div>
												</div>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.location');?>: <strong><?php echo $latitude.", ".$longitude; ?></strong></li>
												<li><?php echo Kohana::lang('ui_main.auto_checkin');?>: <strong><?php echo $auto_checkin; ?></strong></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $checkin_date; ?></td>
										<td class="col-4">
											<ul>
												<?php
												if ((int) $incident_id)
												{
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'members/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>".Kohana::lang('ui_admin.view_report')."</strong></a></li>";
												}
												else
												{
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'members/reports/edit?cid=' . $checkin_id ."\">".Kohana::lang('ui_admin.create_report')."?</a></li>";
												}
												?>
												<li class="none-separator"><a href="javascript:checkinAction('d','DELETE','<?php echo(rawurlencode($checkin_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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