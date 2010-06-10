<?php 
/**
 * Help view page.
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
							<h1>How To Help <?php echo $pagination_stats; ?></h1>
							<div class="org_rowtitle">
								<div class="org_col1">
									<strong>Organization</strong>
								</div>
							</div>
												<?php
											 	foreach ($organizations as $organization)
												{
														$organization_id = $organization->id;
														$organization_name = $organization->organization_name;
														$organization_description = $organization->organization_description;
		
														// Trim to 150 characters without cutting words (Text Helper)
														//XXX: Perhaps delcare 150 as constant
								$organization_description = text::limit_chars($organization_description, 150, "...", true);
												
														echo "<div class=\"org_row1\">";
														echo "	<h3><a href=\"" . url::site() . "help/view/" . $organization_id . "\">" . $organization_name . "</a></h3>";
														echo $organization_description;
														echo "</div>";
												}
										?>
							<?php echo $pagination; ?>
						</div>
						<!-- end reports block -->
					</div>
				</div>
			</div>
		</div>
	</div>
				
