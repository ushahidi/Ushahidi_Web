<?php 
/**
 *  Feeds view page.
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
							<h1>Feeds <?php echo $pagination_stats; ?></h1>
							<div class="report_rowtitle">
								<div class="report_col2">
									<strong><?php echo Kohana::lang('feeds.title');?></strong>
								</div>
								<div class="report_col3">
									<strong><?php echo Kohana::lang('feeds.date');?></strong>
								</div>
								<div class="report_col4">
									<strong><?php echo Kohana::lang('feeds.source');?></strong>
								</div>
							</div>
							<?php
								foreach ($feeds as $feed)
								{
									$feed_id = $feed->id;
									$feed_title = text::limit_chars($feed->item_title, 40, '...', True);
									$feed_link = $feed->item_link;
									$feed_date = date('M j Y', strtotime($feed->item_date));
									$feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
												
									print "<div class=\"report_row1\">";
									print "		<div class=\"report_details report_col2\">";
									print "			<h3><a href=\"$feed_link\">" . $feed_title . "</a></h3>";
									print "		</div>";
									print "		<div class=\"report_date report_col3\">";
									print $feed_date;
									print "		</div>";
									print "		<div class=\"report_location report_col4\">";
									print $feed_source;
									print "		</div>";
									print "</div>";
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
