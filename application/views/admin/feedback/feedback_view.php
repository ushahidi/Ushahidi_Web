<?php 
/**
 * Reports edit view page.
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
	<!-- f-col-bottom-1 -->
	<div style="width:100%;background:#DDD;padding:10px;">
		<h4><?php print Kohana::lang('feedback.feedback'); ?></h4>
		<?php print form::open(NULL,array('id' => 'rpelyForm',
				'name' => 'replyForm',
				'method' => 'post')); ?>
		<div class="row">
			<div class="f-col-bottom-1-col">
				<?php print Kohana::lang('feedback.feedback_reply_message')?>
			</div>
			<?php print $feedback->feedback_mesg?>		
		</div>
		<div class="row">
			<div class="f-col-bottom-1-col">
				<?php print Kohana::lang('feedback.feedback_person_email')?>
			</div>
			<?php print $feedback->person_email;?>							
		</div>
		
		<div class="row">
			<div class="f-col-bottom-1-col">
				<?php print Kohana::lang('feedback.feedback_person_ip')?>
			</div>
			<?php print $feedback->person_ip;?>							
		</div>
										
	</div>
	<div style="clear:both;"></div>
	<?php
	if ($form_error) {
	?>
			<!-- red-box -->
			<div class="red-box">
				<h3>Error!</h3>
				<ul>
				<?php
				foreach ($errors as $error_item => $error_description)
				{
					print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
				}
				?>
				</ul>
		</div>
		<br /><br />
	<?php
	}

	if ($message_sent) {
	?>
		<!-- green-box -->
		<div class="green-box">
			<h3><?php print Kohana::lang('feedback.feedback_confirm_mesg');?></h3>
		</div>
		<br /><br />
	<?php
	}
	?>
	<div style="width:100%;padding:10px;">
		
		<h3><?php print Kohana::lang('feedback.feedback_send_reply')?></h3>
		</br /><br />
		<div class="row">
			<br /></br />
			<div class="f-col-bottom-1-col">
				<?php print Kohana::lang('feedback.feedback_reply_message')?>
			</div>
			<?php print form::textarea('feedback_message', '', 'rows="12" cols="57"'); ?>								
		</div>
		
		<div class="row">
			<br /></br />
			<div class="f-col-bottom-1-col">
				&nbsp;
			`</div>
				<input type="hidden" name="person_email" 
					id="person_email" value="<?php print $feedback->person_email?>" />
			<?php print form::submit('submit', 
				Kohana::lang('feedback.feedback_reply_send')); ?>								
		</div>
	</div>
	<?php print form::close(); ?>
</div>
