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
							<!-- green-box/ red-box depending on verification result -->
							<?php
								// SWITCH based on the value of the $errno
								switch ($errno)
								{
									// IF the code provided was not found ...
									case ER_CODE_NOT_FOUND:
							?>
		          <div class="red-box">
		            <div class="alert_response">
		              <?php echo Kohana::lang('alerts.code_not_found'); ?>
		            </div>
		          </div>
							<?php
								break;
		        
								// IF the code provided means the alert has already been verified ...
								case ER_CODE_ALREADY_VERIFIED:
							?>
		          <div class="red-box">
		            <div class="alert_response" align="center">
		              <?php echo Kohana::lang('alerts.code_already_verified'); ?>
		            </div>
		          </div>
							<?php
								break;
								// IF the code provided means the code is now verified ...
								case ER_CODE_VERIFIED:
							?>
		          <div class="green-box">
		            <div class="alert_response" align="center">
		              <?php echo Kohana::lang('alerts.code_verified'); ?>
		            </div>
		          </div>
							<?php
								break;
								} // End switch
							?>
		        </div>
						<!-- end alerts block -->
					</div>
				</div>
			</div>
		</div>
	</div>
