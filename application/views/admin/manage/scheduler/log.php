<?php 
/**
 * Scheduler view page.
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
				<?php admin::manage_subtabs("scheduler"); ?>
				</h2>
				
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::site() . 'admin/manage/scheduler' ?>"><?php echo Kohana::lang('ui_main.scheduler');?></a></li>
						<li><a href="<?php echo url::site() . 'admin/manage/scheduler/log' ?>" class="active"><?php echo Kohana::lang('ui_main.scheduler_log');?></a></li>
					</ul>

					<!-- tab -->
					<div class="tab">
						&nbsp;
					</div>
				</div>
				

				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'schedulerListing',
					 	'name' => 'schedulerListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="scheduler_id" id="scheduler_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2"><?php echo Kohana::lang('ui_main.schedule');?></th>
										<th class="col-3"><?php echo Kohana::lang('ui_main.date');?></th>
										<th class="col-4">&nbsp;</th>
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
									foreach ($scheduler_logs as $log)
									{
										$scheduler_name = $log->scheduler->scheduler_name;
										$scheduler_date = date("F j, Y, g:i a", $log->scheduler_date);
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $scheduler_name; ?></h4>
												</div>
											</td>
											<td class="col-3" nowrap="nowrap"><?php echo $scheduler_date; ?></td>
											<td class="col-4">&nbsp;
												
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
			</div>
