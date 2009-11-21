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

				<div id="content">
					<div class="content-bg">
						<!-- start contacts block -->
						<div class="big-block">
							<h1><?php echo Kohana::lang('ui_main.contact'); ?></h1>
							<div id="contact_us" class="contact">
								<?php
								if ($form_error)
								{
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
									<?php
								}

								if ($form_sent)
								{
									?>
									<!-- green-box -->
									<div class="green-box">
										<h3>Your Message Has Been Sent!</h3>
									</div>
									<?php
								}								
								?>
								<?php print form::open(NULL, array('id' => 'contactForm', 'name' => 'contactForm')); ?>
								<div class="report_row">
									<strong>Your Name:</strong><br />
									<?php print form::input('contact_name', $form['contact_name'], ' class="text"'); ?>
								</div>
								<div class="report_row">
									<strong>Your E-Mail Address:</strong><br />
									<?php print form::input('contact_email', $form['contact_email'], ' class="text"'); ?>
								</div>
								<div class="report_row">
									Your Phone Number:<br />
									<?php print form::input('contact_phone', $form['contact_phone'], ' class="text"'); ?>
								</div>
								<div class="report_row">
									<strong>Message Subject:</strong><br />
									<?php print form::input('contact_subject', $form['contact_subject'], ' class="text"'); ?>
								</div>								
								<div class="report_row">
									<strong>Message:</strong><br />
									<?php print form::textarea('contact_message', $form['contact_message'], ' rows="4" cols="40" class="textarea long" ') ?>
								</div>		
								<div class="report_row">
									<strong>Security Code:</strong><br />
									<?php print $captcha->render(); ?><br />
									<?php print form::input('captcha', $form['captcha'], ' class="text"'); ?>
								</div>
								<div class="report_row">
									<input name="submit" type="submit" value="Send Message" class="btn_submit" />
								</div>
								<?php print form::close(); ?>
							</div>
							
						</div>
						<!-- end contacts block -->
					</div>
				</div>
			</div>
		</div>
	</div>
