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
		<?php print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'replyForm', 'name' => 'replyForm')); ?>
		<div class="row">
			<div class="f-col-bottom-1-col">Title:</div>
			<?php print $feedback->feedback_title;?>
		</div>
		<div class="row">
			<div class="f-col-bottom-1-col">Message Detail:</div>
			<?php print $feedback->feedback_mesg?>		
		</div>
		<div class="row">
			<div class="f-col-bottom-1-col">Name:</div>
			<?php print $feedback->person_full_name;?>								
		</div>
		<div class="row">
			<div class="f-col-bottom-1-col">Email:</div>
			<?php print $feedback->person_email;?>							
		</div>
		
		<div class="row">
			<div class="f-col-bottom-1-col">IP:</div>
			<?php print $feedback->person_ip;?>							
		</div>
										
	</div>
	<div style="clear:both;"></div>
	<div style="width:100%;padding:10px;">
		
		<h3>Send Reply</h3>
		</br /><br />
		<div class="row">
			<div class="f-col-bottom-1-col">Title:</div>
			<?php print form::input('feedback_title', $feedback->feedback_title, 
			' class="text long"'); ?>							
		</div>
		
		<div class="row">
			<br /></br />
			<div class="f-col-bottom-1-col">Message:</div>
			<?php print form::textarea('feedback_message', '', ' rows="12" cols="57"'); ?>								
		</div>
		
		<div class="row">
			<br /></br />
			<div class="f-col-bottom-1-col">&nbsp;</div>
			<?php print form::submit('submit', 'Send'); ?>								
		</div>
		
	</div>
	<?php print form::close(); ?>
</div>
