		<div id="content">
			<div class="content-bg">
				<!-- start alerts block -->
				<div class="big-block">
					<div class="big-block-top">
						<div class="big-block-bottom">
							<h1>Get Alerts</h1>
							<?php
							if ($form_error) {
							?>
								<!-- red-box -->
								<div class="red-box">
									<h3>Error!</h3>
									<ul>
									<?php
									foreach ($errors as $error_item => $error_description)
									{
										// print "<li>" . $error_description . "</li>";
										print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
									}
									?>
									</ul>
								</div>
							<?php
							}
							?>
							<div class="step-1">
								<h2><strong>Step 1</strong> - Select your city or location:</h2>
									<div class="location">
										<form>
										<label>Alert me if a report is filed in, or around:</label>
										<?php print form::dropdown('alert_city',$cities,'', ' class="select" '); ?>
										</form>
									</div>
								<div class="map">
									<p>Or, place a spot on the map below, and we will alert you when a report is submitted within 20 kilometers.</p>
									<div class="map-holder" id="divMap"></div>
								</div>
							</div>
							<?php print form::open() ?>
								<input type="hidden" id="alert_lat" name="alert_lat" value="<?php echo $form['alert_lat']; ?>">
								<input type="hidden" id="alert_lon" name="alert_lon" value="<?php echo $form['alert_lon']; ?>">
								<div class="step-2-holder">
									<div class="step-2">
										<h2><strong>Step 2</strong> - Send alerts to my:</h2>
										<div class="holder">
											<div class="box">
												<label>
													<?php
													if ($form['alert_mobile_yes'] == 1) {
														$checked = true;
													}
													else
													{
														$checked = false;
													}
													print form::checkbox('alert_mobile_yes', '1', $checked);
													?>
													<span><strong>Mobile phone:</strong><br />enter mobile number with country code</span>
												</label>
												<span><?php print form::input('alert_mobile', $form['alert_mobile'], ' class="text long" '); ?></span>
											</div>
											<div class="box">
												<label>
													<?php
													if ($form['alert_email_yes'] == 1) {
														$checked = true;
													}
													else
													{
														$checked = false;
													}
													print form::checkbox('alert_email_yes', '1', $checked);
													?>
													<span><strong>Email Address:</strong><br />enter email address</span>
												</label>
												<span><?php print form::input('alert_email', $form['alert_email'], ' class="text long" '); ?></span>
											</div>
											<div class="box">
												<label>
													<input type="checkbox" checked="checked" readonly="readonly" />
													<span>RSS Feeds (copy the url below)</span>
												</label>
												<span><input type="text" value="http://feeds.ushahidi.com/some_variable" readonly="readonly"  class="text long" /></span>
											</div>
										</div>
									</div>
									<input id="btn-send-alerts" type="submit" value="Send Me Alerts!" />
								</div>
							<?php print form::close(); ?>
						</div>
					</div>
				</div>
				<!-- end alerts block -->
			</div>
		</div>