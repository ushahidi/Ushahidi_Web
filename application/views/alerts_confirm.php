<?php 
/**
 * Alerts confirm view page.
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
<div id="content">
	<div class="content-bg">
		<!-- start alerts block -->
		<div class="big-block">
			<div class="big-block-top">
				<div class="big-block-bottom">
					<h1>Get Alerts</h1>
					<!--<div class="green-box">
						<h3>Your Alert Has Been Saved!</h3>-->
					
						<?php 
						if (!empty($alert_mobile))
						{
							if ($sms_confirmation_saved)
							{
							?>
							<div class="green-box">
								<h3><?php echo Kohana::lang('alerts.mobile_ok_head'); ?></h3>

								<div class="alert_response">
										<?php echo Kohana::lang('alerts.mobile_alert_request_created'); ?><u><strong>
										<?php echo $alert_mobile; ?></strong></u>.
										<?php echo Kohana::lang('alerts.verify_code'); ?>
										<div class="alert_confirm">
										<div class="label">
										<?php echo Kohana::lang('alerts.mobile_code'); ?>
										</div>
										<?php 
										print form::open('/alerts/verify');
										print form::input('alert_code', '');
										print "&nbsp;&nbsp;";
										print form::submit('button', 'Confirm My Alert Request', ' class="btn_blue"');
										print form::close();
										?>
										</div>
								</div>
							</div>
							<?php
							}
							else
							{	
							//XXX: Format error message
							?>

								<div class="red-box">
								<h3><?php echo Kohana::lang('alerts.mobile_error_head'); ?></h3>

										<div class="alert_response">
										<?php echo Kohana::lang('alerts.error'); ?>
										</div>
								</div>
								<?php
							}
						}
						?>

						
						<?php	
						if (!empty($alert_email))
						{
							if ($email_confirmation_saved)
							{
							?>
								<div class="green-box">
								<h3> <?php echo Kohana::lang('alerts.email_ok_head'); ?></h3>

										<div class="alert_response">
										<?php echo Kohana::lang('alerts.email_alert_request_created'); ?>
										 <u><strong><?php echo $alert_email; ?></strong></u>.
										 <?php echo Kohana::lang('alerts.verify_code'); ?>
										 </div> 
								</div> 
							<?php 
							}
							else
							{
							?>

								<div class="red-box">
								<h3><?php echo Kohana::lang('alerts.email_error_head'); ?></h3>

										<div class="alert_response">
										<?php echo Kohana::lang('alerts.error'); ?>
										</div>
								</div>
							<?php
							}
						}
							?>
					
						<?php
						if ($email_confirmation_saved ||
							$sms_confirmation_saved)
						{
						// Only show this if there is at least one successful
						// request
						?>	
						<div class="green-box">
								<div class="alert_response">
								<a href="<?php echo url::base().'alerts'?>">
								<?php echo Kohana::lang('alerts.create_more_alerts'); ?>
								</a>
								</div>
						</div>
						<?php
						}
						?>
				</div>
			</div>
		</div>
		<!-- end alerts block -->
	</div>
</div>
