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
						<li><a href="<?php echo url::site() . 'admin/manage/scheduler' ?>" class="active"><?php echo Kohana::lang('ui_main.scheduler');?></a></li>
						<li><a href="<?php echo url::site() . 'admin/manage/scheduler/log' ?>"><?php echo Kohana::lang('ui_main.scheduler_log');?></a></li>
					</ul>

					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="<?php echo url::site(); ?>admin/manage/scheduler/?run_scheduler=1"><?php echo strtoupper(Kohana::lang('ui_main.force_run_scheduler'));?></a></li>
						</ul>
					</div>
				</div>
				
				
				<?php
				if ($form_error) {
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul>
						<?php
						foreach ($errors as $error_item => $error_description)
						{
							// print "<li>" . $error_description . "</li>";
							print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
						}
						?>
						</ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box">
						<h3><?php echo Kohana::lang('ui_main.schedule');?> <?php echo $form_action; ?>!</h3>
					</div>
				<?php
				}
				?>
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
										<th class="col-3">&nbsp;</th>
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
									foreach ($schedules as $schedule)
									{
										$scheduler_id = $schedule->id;
										$scheduler_name = $schedule->scheduler_name;
										$scheduler_weekday = $scheduler_weekday_text = $schedule->scheduler_weekday;
										$scheduler_day = $scheduler_day_text = $schedule->scheduler_day;
										$scheduler_hour = $scheduler_hour_text = $schedule->scheduler_hour;
										$scheduler_minute = $scheduler_minute_text = $schedule->scheduler_minute;
										$scheduler_active = $schedule->scheduler_active;
										
										$days[0] = 'Sunday'; 
										$days[1] = 'Monday'; 
										$days[2] = 'Tuesday'; 
										$days[3] = 'Wednesday'; 
										$days[4] = 'Thursday'; 
										$days[5] = 'Friday'; 
										$days[6] = 'Saturday';
										if ($scheduler_weekday <= -1) 
										{ // Ran every day?
											$scheduler_weekday_text = "ALL";
										}
										else
										{
											$scheduler_weekday_text = $days[$scheduler_weekday];
										}
										
										if ($scheduler_day <= -1) 
										{ // Ran every day?
											$scheduler_day_text = "ALL";
										}

										if ($scheduler_hour <= -1) 
										{ // Ran every hour?
											$scheduler_hour_text = "ALL";
										}

										if ($scheduler_minute <= -1) 
										{ // Ran every minute?
											$scheduler_minute_text = "ALL";
										}
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $scheduler_name; ?></h4>
													<p>Schedule:</p>
												</div>
												<ul class="info">
													<li class="none-separator"><?php echo Kohana::lang('ui_main.scheduler_weekday');?>: <strong><?php echo $scheduler_weekday_text; ?></strong></li>
													<li><?php echo Kohana::lang('ui_main.scheduler_day');?>: <strong><?php echo $scheduler_day_text; ?></strong></li>
													<li><?php echo Kohana::lang('ui_main.scheduler_hour');?>: <strong><?php echo $scheduler_hour_text; ?></strong></li>
													<li><?php echo Kohana::lang('ui_main.scheduler_minute');?>: <strong><?php echo $scheduler_minute_text; ?></strong>&nbsp;&nbsp;&nbsp;<a href="http://en.wikipedia.org/wiki/Cron" target="_blank">?</a></li>
												</ul>
											</td>
											<td class="col-3">&nbsp;</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($scheduler_id)); ?>','<?php echo(rawurlencode($scheduler_name)); ?>','<?php echo $scheduler_weekday; ?>','<?php echo $scheduler_day; ?>','<?php echo $scheduler_hour; ?>','<?php echo $scheduler_minute; ?>')">Edit</a></li>
													<li class="none-separator"><a href="javascript:schedulerAction('v','ACTIVATE/DEACTIVATE','<?php echo(rawurlencode($scheduler_id)); ?>')"<?php if ($scheduler_active) echo " class=\"status_yes\"" ?>><?php echo Kohana::lang('ui_main.active');?></a></li>
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
				
				<!-- tabs -->
				<div class="tabs" id="add_edit_form" style="display:none;">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.edit');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'schedulerMain',
						 	'name' => 'schedulerMain')); ?>
						<input type="hidden" id="scheduler_id" 
							name="scheduler_id" value="" />
						<input type="hidden" id="scheduler_active" 
							name="scheduler_active" vaule="" />
						<input type="hidden" name="action" 
							id="action" value=""/>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.scheduler');?>:</strong><br />
							<?php print form::input('scheduler_name', '', ' class="text" disabled="disabled" '); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.scheduler_weekday');?>:</strong><br />
							<?php print form::dropdown('scheduler_weekday', $weekday_array, '0'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.scheduler_day');?>:</strong><br />
							<?php print form::dropdown('scheduler_day', $day_array, '0'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.scheduler_hour');?>:</strong><br />
							<?php print form::dropdown('scheduler_hour', $hour_array, '0'); ?>
						</div>												
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.scheduler_minute');?>:</strong><br />
							<?php print form::dropdown('scheduler_minute', $minute_array, '0'); ?>
						</div>						
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>			
						<div style="clear:both"></div>
						<div class="tab_form_item">
							<strong>Examples:</strong><Br />
							Every Minute: ALL | ALL | ALL | ALL <br />
							Every Hour: ALL | ALL | ALL | 0 <br />
							Midnight Every Day: ALL | ALL | 0 | 0 <br />
							Once A Week on Monday: Monday | ALL | 0 | 0 <br />
							Every 1st of the Month: ALL | 1 | 0 | 0 <br /><br />
							<a href="http://en.wikipedia.org/wiki/Cron" target="_blank">More about running CRON Tasks</a>
						</div>
					</div>
				</div>
			</div>
