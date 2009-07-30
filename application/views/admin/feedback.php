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
			<?php echo "($total_items)"; ?>
		</span>
	</h2>
	<!-- tabs -->
	<div class="tabs">
		<!-- tabset -->
		<ul class="tabset">
			<li><a href="#" class="active">
				<?php echo Kohana::lang('feedback.show_all'); ?> </a>
			</li>
		</ul>
		<!-- tab -->
		<div class="tab">
			<ul>
				<li><a href="#" onclick="feedbackAction('r','READ', '');">
					<?php echo Kohana::lang('feedback.read'); ?></a>
				</li>
				
				<li><a href="#" onclick="feedbackAction('u','UNREAD', '');">
					<?php echo Kohana::lang('feedback.unread'); ?></a>
				</li>
				
				<li><a href="#" onclick="feedbackAction('d','DELETE', '');">
					<?php echo Kohana::lang('feedback.delete'); ?></a>
				</li>
			</ul>
		</div>
	</div>
	<?php
	if ($form_error) {
	?>
		<!-- red-box -->
		<div class="red-box">
			<?php
			foreach ($errors as $error_item => $error_description)
			{
				print (!$error_description) ? '' : "&#8226;&nbsp;" . $error_description . "<br />";
			}
			?>
		</div>
	<?php
	}

	if ($form_saved) {
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
	}
	?>
	<!-- feedback-table -->
	<?php print form::open(NULL, array('id' => 'feedbackMain', 'name' => 'feedbackMain')); ?>
		<input type="hidden" name="action" id="action" value="">
		<input type="hidden" name="feedback_id[]" id="feedback_single" value="">
		<div class="table-holder">
			<table class="table">
				<thead>
					<tr>
						<th class="col-1">
							<input id="checkallfeedback" type="checkbox" 
							class="check-box" 
							onclick="CheckAll( this.id, 'feedback_id[]' )" />
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
								<h3>
									<?php echo Kohana::lang('feedback.feedback_no_result'); ?>
								</h3>
							</td>
						</tr>
						
						<?php
						} 
						
						foreach ( $all_feedback as $feedback )
						{
							$feedback_id = $feedback->id;
							$feedback_title = text::limit_chars($feedback->feedback_mesg, 10, "...", true);
							$feedback_mesg = text::limit_chars($feedback->feedback_mesg, 150, "...", true);
							$feedback_dateadd = $feedback->feedback_dateadd;
							$feedback_dateadd = date('Y-m-d', strtotime($feedback->feedback_dateadd));
							$feedback_read = $feedback->feedback_status;
							$person_ip = $feedback->person_ip;
							
							?>
							<tr>
								<td class="col-1"><input name="feedback_id[]" id="feedback" value="<?php echo $feedback_id; ?>" type="checkbox" class="check-box"/></td>
								<td class="col-2">
									<div class="post">
										<h4><a href="<?php echo url::base() . 'admin/feedback/view/' . $feedback_id; ?>" class="more"><?php echo $feedback_title; ?></a></h4>
										<p><?php echo $feedback_mesg; ?>... <a href="<?php echo url::base() . 'admin/feedback/view/' . $feedback_id; ?>" class="more">more</a></p>
									</div>
									<ul class="info">
										<li class="none-separator">
											<strong>IP: </strong><?php echo $person_ip; ?>
										</li>
									</ul>
								</td>
								<td class="col-3"><?php echo $feedback_dateadd; ?></td>
								<td class="col-4">
									<ul>
										<li class="none-separator">
											<?php if($feedback_read == 1 ) { ?>
											<a href="#" class="status_yes" onclick="feedbackAction('r','READ', '<?php echo $feedback_id; ?>');">
												Read 
											</a>
											<?php } else {?>
											<a href="#" onclick="feedbackAction('u','UNREAD', '<?php echo $feedback_id; ?>');">
												Uread 
											</a>
											<?php }?>
											</li>
										<li><a href="#" class="del" onclick="feedbackAction('d','DELETE', '<?php echo $feedback_id; ?>');">Delete</a></li>
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

