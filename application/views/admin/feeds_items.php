<?php 
/**
 * Messages view page.
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
					<a href="<?php echo url::base() . 'admin/manage' ?>">Categories</a>
					<a href="<?php echo url::base() . 'admin/manage/forms' ?>">Forms</a>
					<a href="<?php echo url::base() . 'admin/manage/organizations' ?>">Organizations</a>
					<a href="<?php echo url::base() . 'admin/manage/pages' ?>">Pages</a>
					<a href="<?php echo url::base() . 'admin/manage/feeds' ?>" class="active">News Feeds</a>
					<span>(<a href="#add">Add New</a>)</span>
					<a href="<?php echo url::base() . 'admin/manage/reporters' ?>">Reporters</a>
				</h2>
			
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::base() . 'admin/manage/feeds' ?>">Feeds</a></li>
						<li><a href="<?php echo url::base() . 'admin/manage/feeds_items' ?>" class="active">Feed Items</a></li>
					</ul>
				
					<!-- tab -->
					<div class="tab">
						&nbsp;
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
						<h3>Messages <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'feedListing', 'name' => 'feedListing')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="level"  id="level"  value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'item_id[]' )" /></th>
									<th class="col-2">Item Details</th>
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
								foreach ($feed_items as $item)
								{
									$item_id = $item->id;
									$item_title = $item->item_title;
									$item_description = $item->item_description;
									$item_link = $item->item_link;
									$item_date = date('Y-m-d', strtotime($item->item_date));
									
									$feed_name = $item->feed->feed_name;
									
									$location_id = $item->location_id;
									$incident_id = $item->incident_id;
									?>
									<tr>
										<td class="col-1"><input name="item_id[]" value="<?php echo $item_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<h4><?php echo $item_title; ?></h4>
												<p><a href="javascript:preview('feed_preview_<?php echo $item_id?>')">Preview Item</a></p>
												<div id="feed_preview_<?php echo $item_id?>" style="display:none;">
													<?php echo $item_description; ?>
												</div>
											</div>
											<ul class="info">
												<li class="none-separator">Feed: <strong><a href="<?php echo $item_link; ?>"><?php echo $feed_name; ?></a></strong>
												<li>GeoLocation Available?: <strong><?php echo ($location_id) ? "YES" : "NO"; ?></strong></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $item_date; ?></td>
										<td class="col-4">
											<ul>
												<?php
												if ($incident_id != 0) {
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>View Report</strong></a></li>";
												}
												else
												{
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit?fid=' . $item_id ."\">Create Report?</a></li>";
												}
												?>
												<li>
                                                <a href="<?php echo url::base().'admin/manage/feeds_delete/'.$item_id ?>" onclick="return confirm('Delete cannot be undone. Are you sure you want to continue?')" class="del">Delete</a></li>
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
