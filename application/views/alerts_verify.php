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
