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
				<div id="main" class="clearingfix">
					<div id="mainmiddle" class="floatbox withright">
						<!-- start incident block -->
						<div class="reports">
							<div class="report-details">
								<div class="verified <?php
								if ($incident_verified == 1)
								{
									echo " verified_yes";
								}
								?>"><?php
									echo ($incident_verified == 1) ?
										"<span>Verified</span>" :
										"<span>Unverified</span>";
									?>
								</div>
								<h1><?php
								echo $incident_title;
								
								// If Admin is Logged In - Allow For Edit Link
								if ($logged_in)
								{
									echo " [&nbsp;<a href=\"".url::site()."admin/reports/edit/".$incident_id."\">Edit</a>&nbsp;]";
								}
								?></h1>
								<ul class="details">
									<li>
										<small>Location</small>
										<?php echo $incident_location; ?>
									</li>
									<li>
										<small>Date</small>
										<?php echo $incident_date; ?>
									</li>
									<li>
										<small>Time</small>
										<?php echo $incident_time; ?>
									</li>
									<li>
										<small>Category</small>
										<?php
											foreach($incident_category as $category) 
											{ 
												echo "<a href=\"".url::site()."reports/?c=".$category->category->id."\">" .
												$category->category->category_title . "</a>&nbsp;&nbsp;&nbsp;";
											}
										?>
									</li>
								</ul>
							</div>
							<div class="location">
								<div class="incident-notation clearingfix">
									<ul>
										<li><img align="absmiddle" alt="Incident" src="<?php echo url::base(); ?>media/img/incident-pointer.jpg"/> Incident</li>
										<li><img align="absmiddle" alt="Nearby Incident" src="<?php echo url::base(); ?>media/img/nearby-incident-pointer.jpg"/> Nearby Incident</li>
									</ul>
								</div>
								<div class="report-map">
									<div class="map-holder" id="map"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
		
				<div class="report-description">
					<h3>Incident Report Description</h3>
						<div class="content">
							<?php echo $incident_description; ?>
							<div class="credibility">
								Credibility:
								<a href="javascript:rating('<?php echo $incident_id; ?>','add','original','oloader_<?php echo $incident_id; ?>')"><img id="oup_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>thumb-up.jpg" alt="UP" title="UP" border="0" /></a>&nbsp;
								<a href="javascript:rating('<?php echo $incident_id; ?>','subtract','original')"><img id="odown_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>thumb-down.jpg" alt="DOWN" title="DOWN" border="0" /></a>&nbsp;
								<a href="" class="rating_value" id="orating_<?php echo $incident_id; ?>"><?php echo $incident_rating; ?></a>
								<a href="" id="oloader_<?php echo $incident_id; ?>" class="rating_loading" ></a>
							</div>
						</div>
						<?php echo $comments; ?>		
					</div>
		
					<?php
					if( count($incident_photos) > 0 ) 
					{
					?>
					<!-- start images -->
					<div class="report-description">
						<h3>Images</h3>
						<div class="photos">
							<?php
							foreach ($incident_photos as $photo)
							{
								$thumb = str_replace(".","_t.",$photo);
				      	$prefix = url::base()."media/uploads";
								echo("<a class='photothumb' rel='lightbox-group1' href='$prefix/$photo'><img src='$prefix/$thumb'/></a> ");
							}
							?>
						</div>
					</div>

					<!-- end images <> start side block -->
					<?php 
					} else {
					?> 

					<div class="report-description">
						<h3>Related Mainstream News of Incident</h3>
						<table cellpadding="0" cellspacing="0">
							<tr class="title">
								<th class="w-01">TITLE</th>
								<th class="w-02">SOURCE</th>
								<th class="w-03">DATE</th>
							</tr>
							<?php
								foreach ($feeds as $feed)
								{
									$feed_id = $feed->id;
									$feed_title = text::limit_chars($feed->item_title, 40, '...', True);
									$feed_link = $feed->item_link;
									$feed_date = date('M j Y', strtotime($feed->item_date));
									$feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
							?>
							<tr>
								<td class="w-01">
									<a href="<?php echo $feed_link; ?>" target="_blank">
									<?php echo $feed_title ?></a>
								</td>
								<td class="w-02"><?php echo $feed_source; ?></td>
								<td class="w-03"><?php echo $feed_date; ?></td>
							</tr>
							<?php
							}
							?>
						</table>
						<!-- end mainstream news of incident -->
					</div>
					<?php
					}?>


					<div class="report-description">
						<h3>Incident Report(s)</h3>
						<table cellpadding="0" cellspacing="0">
							<tr class="title">
								<th class="w-01">TITLE</th>
								<th class="w-02">LOCATION</th>
								<th class="w-03">DATE</th>
							</tr>
							<?php
								foreach($incident_neighbors as $neighbor)
								{
									echo "<tr>";
									echo "<td class=\"w-01\"><a href=\"" . url::site(); 
									echo "reports/view/" . $neighbor->id . "\">" . $neighbor->incident_title . "</a></td>";
									echo "<td class=\"w-02\">" . $neighbor->location->location_name . "</td>";
									echo "<td class=\"w-03\">" . date('M j Y', strtotime($neighbor->incident_date)) . "</td>";
									echo "</tr>";
								}
								?>
						</table>
					</div>

					<?php 
					if( $incident_photos <= 0) 
					{
					?> 
					<div class="small-block">
						<h3>Related Mainstream News of Incident</h3>
						<div class="block-bg">
							<table>
								<tr class="title">
									<th class="w-01">TITLE</th>
									<th class="w-02">SOURCE</th>
									<th class="w-03">DATE</th>
								</tr>
								<?php
									foreach ($feeds as $feed)
									{
										$feed_id = $feed->id;
										$feed_title = text::limit_chars($feed->item_title, 40, '...', True);
										$feed_link = $feed->item_link;
										$feed_date = date('M j Y', strtotime($feed->item_date));
										$feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
								?>
								<tr>
									<td class="w-01">
									<a href="<?php echo $feed_tdnk; ?>" target="_blank">
									<?php echo $feed_title ?></a></td>
									<td class="w-02"><?php echo $feed_source; ?></td>
									<td class="w-03"><?php echo $feed_date; ?></td>
								</tr>
								<?php
									}
								?>
							</table>
						</div>
					</div>
					<?php }	?>
					<!-- end side block -->
					
					
					<!-- start videos -->
					<?php
						if( count($incident_videos) > 0 ) 
						{
					?>
					<div class="report-description">
						<h3>Videos</h3>
						<div class="block-bg">
							<div style="overflow:auto; white-space: nowrap; padding: 10px">
								<?php
									// embed the video codes
									foreach( $incident_videos as $incident_video) {
										$videos_embed->embed($incident_video,'');
									}
								?>
							</div>
						</div>
						<?php } ?>
					</div>
					<!-- end incident block <> start other report -->
					
					<?php echo $comments_form; ?>
					
				</div>
			</div>
		</div>
	</div>

