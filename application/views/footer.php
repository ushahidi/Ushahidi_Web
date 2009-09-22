<?php 
/**
 * Footer view page.
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
		<!-- end content block <> start footer block -->
		<div id="footer">
			<div class="footer-info">
				<ul>
					<li><a href="<?php echo url::base() ?>"><?php echo Kohana::lang('ui_main.home'); ?></a></li>
					<li><a href="<?php echo url::base() . "reports/submit" ?>"><?php echo Kohana::lang('ui_main.report_an_incident'); ?></a></li>
					<li><a href="<?php echo url::base() . "alerts" ?>"><?php echo Kohana::lang('ui_main.alerts'); ?></a></li>
					<li><a href="<?php echo url::base() . "help" ?>"><?php echo Kohana::lang('ui_main.help'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.about'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.contact'); ?></a></li>
					<li><a href="#"><?php echo Kohana::lang('ui_main.blog'); ?></a></li>
				</ul>
				
				<p><?php echo Kohana::lang('ui_main.copyright'); ?></p>
				<p class="feedback_title">
					<a href="javascript:showForm('table-holder')"><?php echo Kohana::lang('ui_main.feedback'); ?></a>
				</p>
				<div id="table-holder" class="feedback_forms">
					
					<table class="table">
						<tbody>
							<?php print form::open(NULL, array('id' => 'footerfeedbackMain', 
								'name' => 'footerfeedbackMain')); ?>
							<?php print form::hidden('person_ip',getenv("REMOTE_ADDR"),'')?>
							<tr>
								<td>
									<?php print form::textarea("feedback_message",'',' rows="5" cols="50"');?>
									<br /><br />
									<?php
										print(empty($errors['feedback_message'])) ?'': $errors['feedback_message'].'<br /><br />';
									?>
								</td>
								<td>
									<div class="or_txt">
										Or
									</div>
								</td>
								<td>
									<div class="detailed_feedback">
										<a href="http://feedback.ushahidi.com/fillsurvey.php?sid=5">Provide Detailed Feedback</a>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<?php print form::input('person_email','Email address','size="40" onclick="clearField();"');?>
									<?php print form::button('submit', 
										Kohana::lang('feedback.feedback_reply_send'),'onclick="formSubmit();"'); ?>
									<br /><br />
									<?php 
										print(empty($errors['person_email'])) ?'': $errors['person_email'].'<br /><br />';
									?>
								</td>
							</tr>
							<?php print form::close();?>
						</tbody>
					</table>
				</div>
			</div>
			<strong class="f-logo"><a href="http://www.ushahidi.com">Ushahidi</a></strong>
			<img src="<?php echo $tracker_url; ?>" />
		</div>
		<!-- end footer block -->
	</div>
	<?php echo $ushahidi_stats; ?>
	<?php echo $google_analytics; ?>
	
	<!-- Task Scheduler -->
	<img src="<?php echo url::base() . 'scheduler'; ?>" height="1" width="1" border="0" />
</body>
</html>