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
						echo "<div class=\"alert_response\">
							This verification code was not found! Please
							confirm that you entered the code
							correctly. You may use the form below to re-enter
							your verification code:</div>";
                   		echo "</div>";

						echo "<div class=\"alert_confirm\">";
						echo "<div class=\"label\">Please enter the confirmation [CODE] 
								you received below: </div>";
								
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
						echo "<div class=\"alert_response\">
							This code has been verified before!</div>";
                   		echo "</div>";
					}

                    elseif ($errno == ER_CODE_VERIFIED)
                    {
                        echo "<div class=\"green-box\">";
                        echo "<div class=\"alert_response\">
                                Your code was verified correctly. You will now
                                receive alerts about incidents as they happen.</div>";
                   		echo "</div>";
                    }

                    ?>
                </div>
			</div>
		</div>
		<!-- end alerts block -->
	</div>
</div>
