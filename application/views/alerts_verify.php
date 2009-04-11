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
			<div class="big-block-top">
				<div class="big-block-bottom">
					<h1>Get Alerts</h1>
					<!-- green-box/ red-box depending on verification result -->
					<?php
					if ($errno == ER_CODE_NOT_FOUND)
					{
						echo "<div class=\"red-box\">";
						echo "<div class=\"alert_response\">";
						echo Kohana::lang('alerts.code_not_found');
                   		echo "</div>";

						echo "<div class=\"alert_confirm\">";
								
								print form::open('/alerts/verify');
								print form::input('alert_code', '');
								print "&nbsp;&nbsp;";
								print form::submit('button', 'Confirm', ' class="btn_blue"');
								print form::close();
						echo "</div>";
						echo "</div>";
					}
					elseif ($errno == ER_CODE_ALREADY_VERIFIED)
					{
						echo "<div class=\"red-box\">";
						echo "<div class=\"alert_response\" align=\"center\">";
						echo Kohana::lang('alerts.code_already_verified');
                        echo "</div>";
                   		echo "</div>";
					}

                    elseif ($errno == ER_CODE_VERIFIED)
                    {
                        echo "<div class=\"green-box\">";
                        echo "<div class=\"alert_response\" align=\"center\">";
                   		echo Kohana::lang('alerts.code_verified');
                        echo "</div>";
                        echo "</div>";
                    }

                    ?>
                </div>
			</div>
		</div>
		<!-- end alerts block -->
	</div>
</div>
