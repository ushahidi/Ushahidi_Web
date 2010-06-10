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
							<h1>Get Alerts</h1>
							
							<!-- Mobile Alert -->
							<div class="green-box">
								<?php
								if ($alert_mobile)
								{
									echo "<h3>".Kohana::lang('alerts.mobile_ok_head')."</h3>";
								}
								?>
								<div class="alert_response">
									<?php 
									if ($alert_mobile)
									{
										echo Kohana::lang('alerts.mobile_alert_request_created')."<u><strong>".
											$alert_mobile."</strong></u>.".
											Kohana::lang('alerts.verify_code');
									}
									?>
									<div class="alert_confirm">
										<div class="label">
											<u><?php echo Kohana::lang('alerts.mobile_code'); ?></u>
										</div>
										<?php 
										print form::open('/alerts/verify');
										print "Verification Code:<BR>".form::input('alert_code', '', ' class="text"')."<BR>";
										print "Mobile Phone:<BR>".form::input('alert_mobile', $alert_mobile, ' class="text"')."<BR>";
										print form::submit('button', 'Confirm My Alert Request', ' class="btn_submit"');
										print form::close();
										?>
									</div>
								</div>
							</div>
							<!-- / Mobile Alert -->
							
							
							<!-- Email Alert -->
							<div class="green-box">
								<?php
								if ($alert_email)
								{
									echo "<h3>".Kohana::lang('alerts.email_ok_head')."</h3>";
								}
								?>
								
								<div class="alert_response">
									<?php 
									if ($alert_email)
									{
										echo Kohana::lang('alerts.email_alert_request_created')."<u><strong>".
											$alert_email."</strong></u>.".
											Kohana::lang('alerts.verify_code');
									}
									?>
									<div class="alert_confirm">
										<div class="label">
											<u><?php echo Kohana::lang('alerts.email_code'); ?></u>
										</div>
										<?php 
										print form::open('/alerts/verify');
										print "Verification Code:<BR>".form::input('alert_code', '', ' class="text"')."<BR>";
										print "Email Address:<BR>".form::input('alert_email', $alert_email, ' class="text"')."<BR>";
										print form::submit('button', 'Confirm My Alert Request', ' class="btn_submit"');
										print form::close();
										?>
									</div>
								</div>
							</div>
							<!-- / Email Alert -->
							
							
							<!-- Return -->
							<div class="green-box">
								<div class="alert_response">
									<a href="<?php echo url::site().'alerts'?>">
									<?php echo Kohana::lang('alerts.create_more_alerts'); ?>
									</a>
								</div>
							</div>
							<!-- / Return -->
							
						</div>
						<!-- end alerts block -->
						
						
					</div>
				</div>
			</div>
		</div>
	</div>