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
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php admin::manage_subtabs("alerts"); ?>
				</h2>				
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::site()."admin/manage/alerts/"; ?>" <?php if ($type == '0' OR empty($type) ) echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="<?php echo url::site()."admin/manage/alerts/index/"; ?>?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.sms');?></a></li>
						<li><a href="<?php echo url::site()."admin/manage/alerts/index/"; ?>?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.email');?></a></li>
					</ul>
					
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('method'=>'get', 'id' => 'alertSearch', 'name' => 'alertSearch')); ?>
							<input type="hidden" name="action" id="action" value="s"/>
							<input type="hidden" name="type" value="<?php echo $type; ?>"/>
							<ul>
								<li>
									<a href="#" onclick="alertAction('d','<?php echo strtoupper(Kohana::lang('ui_main.delete')); ?>', '');">
									<?php echo strtoupper(Kohana::lang('ui_main.delete'));?></a>
								</li>
								<li style="float:right;">
									<?php print form::input('ak', $keyword, ' class="text" style="float:left;height:20px;"'); ?>
									<a href="#" onclick="javascript:alertSearch.submit();">
									<?php echo Kohana::lang('ui_main.search');?></a>
								</li>
							</ul>
						<?php print form::close(); ?>
					</div>
				</div>
				<?php if ($form_error): ?>
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
				<?php endif; ?>
				
				<?php if ($form_saved): ?>
					<!-- green-box -->
					<div class="green-box">
						<h3><?php echo Kohana::lang('ui_main.alert_has_been');?> <?php echo $form_action; ?>!</h3>
					</div>
				<?php endif; ?>
				
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'alertMain', 'name' => 'alertMain')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="alert_id[]" id="alert_single" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1"><input id="checkallalerts" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'alert_id[]' )" /></th>
										<th class="col-2"><?php echo Kohana::lang('ui_admin.alerts');?></th>
										<th class="col-3"><?php echo Kohana::lang('ui_main.sent');?></th>
										<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
									</tr>
								</thead>
								<tfoot>
									<tr class="foot">
										<td colspan="4"><?php echo $pagination; ?></td>
									</tr>
								</tfoot>
								<tbody>
									<?php if ($total_items == 0): ?>
										<tr>
											<td colspan="4" class="col">
												<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
											</td>
										</tr>
									<?php endif; ?>
									<?php
									foreach ($alerts as $alert)
									{?>
										<tr>
											<td class="col-1"><input name="alert_id[]" id="alert" value="<?php echo $alert->id; ?>" type="checkbox" class="check-box"/></td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $alert->alert_recipient; ?></h4>
												</div>
												<ul class="info">
													<li class="none-separator">
														<?php echo Kohana::lang('ui_main.location');?>: 
														<strong><?php echo $alert->alert_lat.','.$alert->alert_lon; ?></strong>
													</li>
													<li class="none-separator">
														<?php echo Kohana::lang('ui_main.radius');?>: 
														<strong><?php echo $alert->alert_radius; ?></strong>
													</li>
												</ul>
											</td>
											<td><?php echo $alert->alert_sent->count(); ?></td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="javascript:alertAction('d','DELETE','<?php echo(rawurlencode($alert->id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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
			</div>
