<?php 
/**
 * Feedback view page.
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
		<?php echo Kohana::lang('feedback.feedback'); ?> 
		<span>
			(<?php echo "";//$total_items; ?>)
		</span>
	</h2>
	<!-- tabs -->
	<div class="tabs">
		<!-- tabset -->
		<ul class="tabset">
			<li><a href="?status=0" <?php //if ($status != 'a' && $status !='v') 
				echo "class=\"active\""; ?>>
				<?php echo Kohana::lang('feedback.show_all'); ?> </a>
			</li>
		</ul>
		<!-- tab -->
		<div class="tab">
			<ul>
				<li><a href="#" onclick="reportAction('r','READ', '');">
					<?php echo Kohana::lang('feedback.read'); ?></a>
				</li>
				
				<li><a href="#" onclick="reportAction('u','UNREAD', '');">
					<?php echo Kohana::lang('feedback.unread'); ?></a>
				</li>
				
				<li><a href="#" onclick="reportAction('d','DELETE', '');">
					<?php echo Kohana::lang('feedback.delete'); ?></a>
				</li>
			</ul>
		</div>
	</div>
	<?php
	//if ($form_error) {
	?>
		<!-- red-box -->
		<div class="red-box">
			<h3><?php echo Kohana::lang('feedback.error_title'); ?></h3>
			<ul><?php echo Kohana::lang('feedback.error_msg'); ?></ul>
		</div>
	<?php
	//}

	//if ($form_saved) {
	?>
		<!-- green-box -->
		<div class="green-box" id="submitStatus">
			<h3>
				<?php echo Kohana::lang('feedback.feedback'); ?>
				 <?php echo "";//$form_action; ?> 
				<a href="#" id="hideMessage" class="hide">
					<?php echo Kohana::lang('feedback.hide_msg'); ?></a>
			</h3>
		</div>
	<?php
	//}
	?>
	<!-- report-table -->
	<?php print form::open(NULL, array('id' => 'feedbackMain', 'name' => 'feedbackMain')); ?>
		<input type="hidden" name="action" id="action" value="">
		<input type="hidden" name="incident_id[]" id="incident_single" value="">
		<div class="table-holder">
			<table class="table">
				<thead>
					<tr>
						<th class="col-1">
							<input id="checkallincidents" type="checkbox" 
							class="check-box" 
							onclick="CheckAll( this.id, 'incident_id[]' )" />
						</th>
						<th class="col-2">
							<?php echo Kohana::lang('feedback.feedback_details'); ?>
						</th>
						<th class="col-3">
							<?php echo Kohana::lang('feedback.feedback_date'); ?>
						</th>
						<th class="col-4">
							<?php echo Kohana::lang('feedback.feedback_actions'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr class="foot">
						<td colspan="4">
							<?php echo "";//$pagination; ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					//if ($total_items == 0)
					{
					?>
						<tr>
							<td colspan="4" class="col">
								<h3>
									<?php echo Kohana::lang('feedback.feedback_no_result'); ?>
								</h3>
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

