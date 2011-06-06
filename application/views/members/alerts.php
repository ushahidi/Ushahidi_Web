<?php 
/**
 * Alerts view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Private Messages View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php members::alerts_subtabs("view"); ?>
				</h2>

				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::site()."members/alerts/"; ?>" <?php if ($type == '0' OR empty($type) ) echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="<?php echo url::site()."members/alerts/index/"; ?>?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.sms');?></a></li>
						<li><a href="<?php echo url::site()."members/alerts/index/"; ?>?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.email');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onClick="alertsAction('d', 'DELETE', '')"><?php echo strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
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
						<h3><?php echo Kohana::lang('ui_main.messages');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'alertsMain', 'name' => 'alertsMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="level"  id="level"  value="">
					<input type="hidden" name="alert_id[]" id="alert_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkall" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'alert_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_admin.alerts');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_admin.alerts_received');?></th>
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
								foreach ($alerts as $alert)
								{
									$alert_id = $alert->id;
									$alert_type = $alert->alert_type;
									$alert_lat = $alert->alert_lat;
									$alert_lon = $alert->alert_lon;
									$alert_radius = $alert->alert_radius;
									
									$alert_count = $alert->alert_sent->count();
									$categories = $alert->alert_category;
									?>
									<tr>
										<td class="col-1"><input name="alert_id[]" id="alert" value="<?php echo $alert_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<p><?php echo $alert_lat.", ".$alert_lon; ?></p>
												<p><a href="javascript:showAlert('alert_preview_<?php echo $alert_id?>', '<?php echo $alert_lon?>', '<?php echo $alert_lat?>', '<?php echo $alert_radius?>')"><?php echo Kohana::lang('ui_admin.preview');?></a></p>
												<div id="alert_preview_<?php echo $alert_id?>" class="preview_div">
													<div id="alert_preview_<?php echo $alert_id?>_map" class="checkin_map"></div>
												</div>
											</div>
											<ul class="info">
												<li class="none-separator">Radius: <strong><?php echo $alert_radius; ?></strong></li>
												
												<li>Categories: <strong><?php 
												foreach ($categories as $alert_category)
												{
													echo $alert_category->category->category_title.", ";
												}
												?></strong></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $alert_count; ?></td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><a href="javascript:alertsAction('d','DELETE','<?php echo(rawurlencode($alert_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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