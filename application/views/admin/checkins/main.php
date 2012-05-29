<?php 
/**
 * Checkins view page.
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
		<?php echo Kohana::lang('ui_admin.checkins'); ?>
	</h2>

<?php Event::run('ushahidi_action.admin_checkins_custom_layout'); ?>
	
<?php // Kill the rest of the page if this event has been utilized by a plugin ?>
<?php if ( ! Event::has_run('ushahidi_action.admin_checkins_custom_layout')): ?>

	<!-- tabs -->
	<div class="tabs">
		<!-- tab -->
		<div class="tab">
			<ul>
				<li><a href="#" onClick="checkinsAction('d', 'DELETE', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
			</ul>
		</div>
	</div>
	<?php if ($form_error): ?>
		<!-- red-box -->
		<div class="red-box">
			<h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
			<ul><?php echo Kohana::lang('ui_main.select_one'); ?></ul>
		</div>
	<?php endif; ?>

	<?php if ($form_saved): ?>
		<!-- green-box -->
		<div class="green-box" id="submitStatus">
			<h3><?php echo Kohana::lang('ui_admin.checkins');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
		</div>
	<?php endif; ?>

	<!-- report-table -->
	<?php print form::open(NULL, array('id' => 'checkinMain', 'name' => 'checkinMain')); ?>
		<input type="hidden" name="action" id="action" value="">
		<input type="hidden" name="level"  id="level"  value="">
		<input type="hidden" name="checkin_id[]" id="checkin_single" value="">
		<div class="table-holder">
			<table class="table">
				<thead>
					<tr>
						<th class="col-1"><input id="checkall" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'checkin_id[]' )" /></th>
						<th class="col-2"><?php echo Kohana::lang('ui_main.message_details');?></th>
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
					<?php if ($total_items == 0): ?>
						<tr>
							<td colspan="4" class="col">
								<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
							</td>
						</tr>
					<?php endif; ?>
					<?php
					foreach ($checkins as $checkin)
					{
						$checkin_id = $checkin->id;
						$checkin_from = $checkin->user->email;
						$incident_id = 0; // TODO: Change this so we can make reports out of checkins
						$checkin_description = text::auto_link($checkin->checkin_description);
						$checkin_date = date("Y-m-d H:i:s", strtotime($checkin->checkin_date));
						?>
						<tr>
							<td class="col-1"><input name="checkin_id[]" id="checkin" value="<?php echo $checkin_id; ?>" type="checkbox" class="check-box"/></td>
							<td class="col-2">
								<div class="post">
									<p><?php echo $checkin_description; ?></p>
									<?php
									// Action::checkin_extra_admin  - Checkin Additional/Extra Stuff
									Event::run('ushahidi_action.checkin_extra_admin', $checkin_id);
									?>
								</div>
								<ul class="info">
									<?php if(strstr($checkin_from,'@')): ?>
										<a href="mailto:'.$checkin_from.'"><?php echo $checkin_from; ?></a>
									<?php else: ?>
										<small><em>Unknown Email Address</em><?php echo $checkin_from; ?></small>
									<?php endif; ?>
								</ul>
							</td>
							<td class="col-3"><?php echo $checkin_date; ?></td>
							<td class="col-4">
								<ul>
									<!-- TODO: Add Create Report Functionality
									<?php
									if ($incident_id != 0) {
										echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>View Report</strong></a></li>";
									}else{
										echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit?mid=' . $checkin_id ."\">Create Report?</a></li>";
									}
									?>
									-->
									<li><a href="javascript:checkinsAction('d','DELETE','<?php echo(rawurlencode($checkin_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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
<?php endif; ?>
</div>
