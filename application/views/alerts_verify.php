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
							correctly.</div>";
					}
					elseif ($errno == ER_CODE_ALREADY_VERIFIED)
					{
						echo "<div class=\"red-box\">";
						echo "<div class=\"alert_response\">
							This code has been verified before!</div>";
					}

                    elseif ($errno == ER_CODE_VERIFIED)
                    {
                        echo "<div class=\"green-box\">";
                        echo "<div class=\"alert_response\">
                                Your code was verified correctly. You will now
                                receive alerts about incidents as they happen.</div>";
                    }
                   	echo "</div>";

                    ?>
                </div>
			</div>
		</div>
		<!-- end alerts block -->
	</div>
</div>
