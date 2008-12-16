<div id="content">
	<div class="content-bg">
		<!-- start alerts block -->
		<div class="big-block">
			<div class="big-block-top">
				<div class="big-block-bottom">
					<h1>Get Alerts</h1>
					<!-- green-box -->
					<div class="green-box">
						<h3>Your Alert Has Been Saved!</h3>
					
						<?php 
						if (!empty($alert_mobile))
						{
							?>
							<div class="alert_response">
								Your [MOBILE] Alert request has been created and a
								verification message has been sent to <u><strong><?php echo
								$alert_mobile; ?></strong></u>
								You will not receive Alerts on this location until you enter the verification
								code below to confirm your request.
								<div class="alert_confirm">
									<div class="label">Please enter the SMS confirmation [CODE] 
										you received on your mobile phone below:
									</div>
									<?php 
									print form::open();
									print form::input('alert_code', '');
									print "&nbsp;&nbsp;";
									print form::submit('button', 'Confirm My Mobile Number', ' class="btn_blue"');
									print form::close();
									?>
								</div>
							</div>
							<?php
						}
						?>
						<?php 	
						if (!empty($alert_email))
						{
						?>
						<div class="alert_response">
							Your [EMAIL] Alert request has been
							created and a verification message has been sent
							to <u><strong><?php echo $alert_email; ?></strong></u> You will
							not receive Alerts on this location until you click the link in
							the verification email and confirm your request.
						</div>  
						<?php 
						}
						?>
						<div class="alert_response">
							<a href="<?php echo
							url::base().'alerts' ?>">Return to the Alerts page to create more
							alerts</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end alerts block -->
	</div>
</div>
