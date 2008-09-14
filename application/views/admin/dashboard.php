			<div class="bg">
				<h2><?php echo $title; ?></h2>
				<!-- column -->
				<div class="column">
					<!-- box -->
					<div class="box">
						<h3>Reports Timeline</h3>
						<ul class="inf">
							<li class="none-separator">View:<a href="dashboard?chart=day">Today</a></li>
							<li><a href="dashboard">This Month</a></li>
						</ul>
						<img src="<?php echo url::base() . 'admin/dashboard/chart' . $timeline ?>" alt="Incidents" title="Incidents" width="410" height="305" />
					</div>
					<!-- info-container -->
					<div class="info-container">
						<div class="i-c-head">
							<h3>Recent Reports</h3>
							<ul>
								<li class="none-separator"><a href="<?php echo url::base() . 'admin/reports' ?>">View All</a></li>
								<li><a href="#" class="rss-icon">rss</a></li>
							</ul>
						</div>
						<?php
						if ($reports_total == 0)
						{
						?>
						<div class="post">
							<h3>No Results To Display!</h3>
						</div>
						<?php	
						}
						foreach ($incidents as $incident)
						{
							$incident_id = $incident->id;
							$incident_title = $incident->incident_title;
							$incident_description = substr($incident->incident_description, 0, 150);
							$incident_date = $incident->incident_date;
							$incident_date = date('g:i A', strtotime($incident->incident_date));
							$incident_mode = $incident->incident_mode;	// Mode of submission... WEB/SMS/EMAIL?
							
							if ($incident_mode == 1)
							{
								$submit_mode = "mail";
							}
							elseif ($incident_mode == 2)
							{
								$submit_mode = "sms";
							}
							elseif ($incident_mode == 3)
							{
								$submit_mode = "mail";
							}
							
							// Incident Status
							$incident_approved = $incident->incident_active;
							if ($incident_approved == '1')
							{
								$incident_approved = "ok";
							}
							else
							{
								$incident_approved = "none";
							}
							
							$incident_verified = $incident->incident_verified;
							if ($incident_verified == '1')
							{
								$incident_verified = "ok";
							}
							else
							{
								$incident_verified = "none";
							}
							?>
							<div class="post">
								<ul class="post-info">
									<li><a href="#" class="<?php echo $incident_approved; ?>">ACTIVE:</a></li>
									<li><a href="#" class="<?php echo $incident_verified ?>">VERIFIED:</a></li>
									<li class="last"><a href="#" class="<?php echo $submit_mode; ?>">SOURCE:</a></li>
								</ul>
								<h4><strong><?php echo $incident_date; ?></strong><a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>"><?php echo $incident_title; ?></a></h4>
								<p><?php echo $incident_description; ?>...</p>
							</div>
							<?php
						}
						?>
						<a href="<?php echo url::base() . 'admin/reports' ?>" class="view-all">View All Reports</a>
					</div>
				</div>
				<div class="column-1">
					<!-- box -->
					<div class="box">
						<h3>Quick Stats</h3>
						<ul class="nav-list">
							<li>
								<a href="<?php echo url::base() . 'admin/reports' ?>" class="reports">Reports</a>
								<strong><?php echo $reports_total; ?></strong>
								<ul>
									<li><a href="<?php echo url::base() . 'admin/reports?status=a' ?>">Unapproved</a><strong>(<?php echo $reports_unapproved; ?>)</strong></li>
									<li><a href="<?php echo url::base() . 'admin/reports?status=v' ?>"> Unverified</a><strong>(<?php echo $reports_unverified; ?>)</strong></li>
								</ul>
							</li>
							<li>
								<a href="<?php echo url::base() . 'admin/manage' ?>" class="categories">Categories</a>
								<strong><?php echo $categories; ?></strong>
							</li>
							<li>
								<a href="#" class="locations">Locations</a>
								<strong><?php echo $locations; ?></strong>
							</li>
							<li>
								<a href="#" class="media">Incoming Media</a>
								<strong><?php  ?></strong>
							</li>
						</ul>
					</div>
					<!-- info-container -->
					<div class="info-container">
						<div class="i-c-head">
							<h3>Incoming Media</h3>
							<ul>
								<li class="none-separator"><a href="#">View All</a></li>
								<li><a href="#" class="rss-icon">rss</a></li>
							</ul>
						</div>
						<div class="post">
							<h4><a href="#">Anxiety increases in IDP Camps as supplies dwindle</a></h4>
							<em class="date">The Associated Press - Feb 26, 2008 11:45 AM</em>
							<p>One person was slashed at mauche belonging to the Kikuyu tribe. The Kikuyu tribe in revenge burnt down acar from the kalenjin community at k.</p>
						</div>
						<div class="post">
							<h4><a href="#">Kalonzo urges youth to shun violence</a></h4>
							<em class="date">YouTube - Feb 25, 2008 12:45 AM</em>
							<p>in the Kenyan capital leave more dead in a flare up of ethnically motivated violence. They bring the death toll to more...</p>
						</div>
						<div class="post">
							<h4><a href="#">Kenya Violence continues - 27 Jan 08</a></h4>
							<em class="date">Standard, Kenya - Jan. 23, 2008 10:30 PM</em>
							<p>Al Jazeera's Mohammed Adow reports from the Rift Valley region where ethnic violence continues despite mediation efforts...</p>
						</div>
						<a href="#" class="view-all">View All Reports</a>
					</div>
				</div>
			</div>