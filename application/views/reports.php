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
							
              <div class="r_cat_tooltip">
                <a href="#" class="r-3">2a. Structures a risque | Structures at risk</a>
              </div>
              
						  <div class="reports-box">
							<?php
							foreach ($incidents as $incident)
							{
								$incident_id = $incident->id;
								$incident_title = $incident->incident_title;
								$incident_description = $incident->incident_description;
								//$incident_category = $incident->incident_category;
								// Trim to 150 characters without cutting words
								// XXX: Perhaps delcare 150 as constant

								$incident_description = text::limit_chars(strip_tags($incident_description), 150, "...", true);
								$incident_date = date('H:i M d, Y', strtotime($incident->incident_date));
								//$incident_time = date('H:i', strtotime($incident->incident_date));
							  $incident_location = $incident->location_name;
								$incident_verified = $incident->incident_verified;
								
								if ($incident_verified)
								{
									$incident_verified = '<span class="r_verified">'.Kohana::lang('ui_main.verified').'</span>';
								}else{
									$incident_verified = '<span class="r_unverified">'.Kohana::lang('ui_main.unverified').'</span>';
								}
							?>
							  
  							<div class="rb_report">
  							  <div class="r_media">
  							    <p class="r_photo">
  							      <a href="#"><!-- should link directly to report -->
  							        <!-- 
  							          If no images, show the default image placeholder (report-thumb-default.jpg)
  							          If there are images, show the thumbnail for the most recent one.
  							        -->
  							        <img src="<?php echo url::site(); ?>media/img/report-thumb-default.jpg" height="59" width="89" />
  							      </a>
  							    </p>
  							    <!-- Only show this if the report has a video -->
  							    <p class="r_video" style="display:none;"><a href="#">Video</a></p>
  							    
  							    <div class="r_categories" style="display:none;">
  							      <h4><?php echo Kohana::lang('ui_main.categories'); ?></h4>
  							      <!-- a default category -->
  							      <a class="r_category" href="#"><span class="r_cat-box" style="background-color:#368C00;"></span> <span class="r_cat-desc">2a. Structures a risque | Structures at risk</span></a>  
  							      <!-- a category with an icon-->
  							      <a class="r_category" href="#"><span class="r_cat-box"><img src="/media/uploads/_icon-1.jpg" height="16" width="16" /></span> <span class="r_cat-desc">1b. Incendie | Fire</span></a>
                    </div>
  							  </div>
  							  <div class="r_details">
                    <h3><a class="r_title" href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>"><?php echo $incident_title; ?></a> <a href="<?php echo url::site(); ?>reports/view/<?php echo $incident_id; ?>#comments" class="r_comments" style="display:none">3</a> <?php echo $incident_verified; ?></h3>
                    <p class="r_date r-3 bottom-cap"><?php echo $incident_date; ?></p>
                    <div class="r_description">
                        <?php echo $incident_description; ?>
                    </div>
                    <p class="r_location"><?php echo $incident_location; ?></p>
  							  </div>
  							</div>
  							
							<?php } ?>
              </div>
							<?php echo $pagination; ?>

						</div>
						<!-- end reports block -->
					</div>
				</div>
			</div>
		</div>
	</div>
