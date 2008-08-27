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
						<img src="dashboard/chart<?php echo $timeline; ?>" alt="Incidents" title="Incidents" width="410" height="305" />
					</div>
					<!-- info-container -->
					<div class="info-container">
						<div class="i-c-head">
							<h3>Recent Reports</h3>
							<ul>
								<li class="none-separator"><a href="#">View All</a></li>
								<li><a href="#" class="rss-icon">rss</a></li>
							</ul>
						</div>
						<div class="post">
							<ul class="post-info">
								<li><a href="#" class="ok">ACTIVE:</a></li>
								<li><a href="#" class="none">VERIFIED:</a></li>
								<li class="last"><a href="#" class="mail">SOURCE:</a></li>
							</ul>
							<h4><strong>2:45 PM</strong><a href="#">Anxiety increases in IDP Camps as supplies dwindle</a></h4>
							<p>Food and other essential supplies are fast dwindling in the camps for Internally Displaced People (IDP) in Nairobi without any significant...</p>
						</div>
						<div class="post">
							<ul class="post-info">
								<li><a href="#" class="ok">ACTIVE:</a></li>
								<li><a href="#" class="ok">VERIFIED:</a></li>
								<li class="last"><a href="#" class="phone">SOURCE:</a></li>
							</ul>
							<h4><strong>2:45 PM</strong><a href="#">Anxiety increases in IDP Camps as supplies dwindle</a></h4>
							<p>Food and other essential supplies are fast dwindling in the camps for Internally Displaced People (IDP) in Nairobi without any significant...</p>
						</div>
						<div class="post">
							<ul class="post-info">
								<li><a href="#" class="none">ACTIVE:</a></li>
								<li><a href="#" class="none">VERIFIED:</a></li>
								<li class="last"><a href="#" class="phone">SOURCE:</a></li>
							</ul>
							<h4><strong>2:45 PM</strong><a href="#">Anxiety increases in IDP Camps as supplies dwindle</a></h4>
							<p>Food and other essential supplies are fast dwindling in the camps for Internally Displaced People (IDP) in Nairobi without any significant...</p>
						</div>
						<a href="#" class="view-all">View All Reports</a>
					</div>
				</div>
				<div class="column-1">
					<!-- box -->
					<div class="box">
						<h3>Quick Stats</h3>
						<ul class="nav-list">
							<li>
								<a href="#" class="reports">Reports</a>
								<strong><?php echo $reports_total; ?></strong>
								<ul>
									<li><a href="#">Unapproved</a><strong>(<?php echo $reports_unapproved; ?>)</strong></li>
									<li><a href="#"> Unverified</a><strong>(<?php echo $reports_unverified; ?>)</strong></li>
								</ul>
							</li>
							<li>
								<a href="#" class="categories">Categories</a>
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