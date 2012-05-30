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
					<?php admin::manage_subtabs("feeds"); ?>
				</h2>
			
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::site() . 'admin/manage/feeds' ?>"><?php echo Kohana::lang('ui_main.feeds');?></a></li>
						<li><a href="<?php echo url::site() . 'admin/manage/feeds_items' ?>" class="active"><?php echo Kohana::lang('ui_main.feed_items');?></a></li>
						
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
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_main.messages');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'feedListing', 'name' => 'feedListing')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="item_id"  id="item_id_action"  value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'item_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.item_details');?></th>
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
												<p><a href="javascript:preview('feed_preview_<?php echo $item_id?>')"><?php echo Kohana::lang('ui_main.preview_item');?></a></p>
												<div id="feed_preview_<?php echo $item_id?>" style="display:none;">
													<?php echo $item_description; ?>
												</div>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.feed');?>: <strong><a href="<?php echo $item_link; ?>"><?php echo $feed_name; ?></a></strong>
												<li><?php echo Kohana::lang('ui_main.geolocation_available');?>?: <strong><?php echo ($location_id) ? utf8::strtoupper(Kohana::lang('ui_main.yes')) : utf8::strtoupper(Kohana::lang('ui_main.no'));?></strong></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $item_date; ?></td>
										<td class="col-4">
											<ul>
												<?php
												if ($incident_id != 0) {
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>".Kohana::lang('ui_main.view_report')."</strong></a></li>";
												}
												else
												{
													echo "<li class=\"none-separator\"><a href=\"".url::base().'admin/reports/edit?fid='.$item_id."\">".Kohana::lang('ui_main.create_report')."?</a></li>";
												}
												?>
											<li><a href="javascript:feedAction('d','DELETE','<?php echo(rawurlencode($item_id)); ?>');"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
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
