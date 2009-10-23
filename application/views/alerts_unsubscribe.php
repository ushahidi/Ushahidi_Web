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
						<!-- start alerts block -->
						<div class="big-block">
							<h1>Get Alerts</h1>
								<?php
									if ($unsubscribed)
									{
										echo '<div class="green-box">';
										echo '<div class="alert_response" align="center">';
										$settings = kohana::config('settings');
										echo Kohana::lang('alerts.unsubscribed')
												.$settings['site_name']; 
										echo '</div>';
										echo '</div>';
									}
									else
									{
										echo '<div class="red-box">';
										echo '<div class="alert_response" align="center">';
										echo Kohana::lang('alerts.unsubscribe_failed');
										echo '</div>';
										echo '</div>';
									}
								?>
						</div>

						<!-- end alerts block -->
					</div>
				</div>
			</div>
		</div>
	</div>
