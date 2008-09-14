			<div class="bg">
				<h2><?php echo $title; ?> <a href="<?php echo url::base() . 'admin/settings' ?>">Map</a><a href="<?php echo url::base() . 'admin/settings/sms' ?>" class="active">SMS</a><a href="<?php echo url::base() . 'admin/settings/sharing' ?>">Sharing</a></h2>
				<div class="report-form">
					<div class="head">
						<h3>SMS Setup Options</h3>
						<input type="image" src="<?php echo url::base() ?>media/img/admin//btn-cancel.gif" class="cancel-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin//btn-save-settings.gif" class="save-rep-btn" />
					</div>
					<!-- column -->
		
					<div class="sms_nav_holder">
						<a href="#" class="active">Option 1: Use Frontline SMS</a>
						<a href="#">Option 2: Use a Global SMS Gateway</a>
					</div>
		
					<div class="sms_holder">
						<table style="width: 630px;" class="my_table">
							<tr>
								<td style="width:60px;">
									<span class="big_blue_span">Step 1:</span>
								</td>
								<td>
									<h4 class="fix">Download Frontline SMS and install it on your computer. <sup><a href="#">?</a></sup></h4>
									<p>
										This is some descriptive text that talks about Frontline SMS a bit more.  Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed.
									</p>
									<a href="#" class="no_border">
										<img src="<?php echo url::base() ?>media/img/admin//download_frontline_engine.gif" />
									</a>
						
								</td>
							</tr>
							<tr>
								<td>
									<span class="big_blue_span">Step 2:</span>
								</td>
								<td>
									<h4 class="fix">Sync with Ushahidi <sup><a href="#">?</a></sup></h4>
									<p>
										This is some descriptive text that talks Syncing with Ushahidi a bit more.  Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed.
									</p>
									<p class="sync_key">
										Your Ushahidi Sync Key: <span>32WSC402!</span>
									</p>
								</td>
							</tr>
							<tr>
								<td>
									<span class="big_blue_span">Step 3:</span>
								</td>
								<td>
									<h4 class="fix">Enter phone number(s) connected to Frontline SMS in the field(s) below. <sup><a href="#">?</a></sup></h4>
									<p>
										This is some descriptive text about entering SMS Phone Numbers.  Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed.
									</p>
									<input type="text" class="text title_2" /> <a href="#" class="add"></a>
								</td>
							</tr>
						</table>
					</div>
		
					<div class="simple_border"></div>
		
					<input type="image" src="<?php echo url::base() ?>media/img/admin//btn-save-settings.gif" class="save-rep-btn" />
					<input type="image" src="<?php echo url::base() ?>media/img/admin//btn-cancel.gif" class="cancel-btn" />
				</div>
			</div>