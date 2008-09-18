		<div id="content">
			<div class="content-bg">
				<!-- start map and media filter -->
				<div class="big-block">
					<div class="big-block-top">
						<div class="big-block-bottom">
							<div class="big-map-block">
								<div class="filter">
									<strong>MEDIA FILTER</strong>
									<ul>
										<li><a class="active" href="#"><span>Reports</span></a></li>
										<li><a href="#"><span>News</span></a></li>
										<li><a href="#"><span>Pictures</span></a></li>
										<li><a href="#"><span>Video</span></a></li>
										<li><a href="#"><span>All</span></a></li>
									</ul>
								</div>
								<div id="map" class="map-holder"></div>
								<div class="slider-holder"></div>
							</div>
							<div class="category">
								<strong class="title">CATEGORY FILTER</strong>
								<div class="grey-box">
									<div class="grey-box-bg">
										<ul>
											<li><a class="active" href="#"><span style="background:no-repeat url(<?php echo url::base() . 'swatch/?c=ffffff&w=16&h=16&.png' ?>); background-position:left center;">All Categories</span></a></li>
											<?php
											foreach ($categories as $category => $category_info)
											{
												$category_title = $category_info[0];
												$category_color = $category_info[1];
												echo '<li><a href="#"><span style="background:no-repeat url('. url::base() . "swatch/?c=" . $category_color . "&w=16&h=16&.png" . '); background-position:left center;">' . $category_title . '</span></a></li>';
											}
											?>
										</ul>
									</div>
								</div>
								<div class="report-btns">
									<a class="btn-red" href="#"><span>Submit an Incident!</span></a>
									<a class="btn-grey" href="#"><span>Submit via SMS</span></a>
								</div>
								<p>Send your SMS to <strong>6007</strong> on your phone</p>
							</div>
						</div>
					</div>
				</div>
				<!-- end map and media filter <> start incidents and news blocks -->
				<div class="blocks-holder">
					<div class="small-block incidents">
						<h3>Incidents <span>(from map above listed chronologically)</span></h3>
						<div class="block-bg">
							<div class="block-top">
								<div class="block-bottom">
									<ul>
										<li>
											<ul class="title">
												<li class="w-01">TITLE</li>
												<li class="w-02">LOCATION</li>
												<li class="w-03">DATE</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
												<li class="w-02">Eldoret</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
												<li class="w-02">Kisumu</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
												<li class="w-02">Eldoret</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
												<li class="w-02">Kisumu</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
												<li class="w-02">Eldoret</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
												<li class="w-02">Kisumu</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
												<li class="w-02">Eldoret</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
												<li class="w-02">Kisumu</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
									</ul>
									<a class="btn-more" href="#"><span>MORE</span></a>
								</div>
							</div>
						</div>
					</div>
					<div class="small-block news">
						<h3>Official &amp; Mainstream News</h3>
						<div class="block-bg">
							<div class="block-top">
								<div class="block-bottom">
									<ul>
										<li>
											<ul class="title">
												<li class="w-01">TITLE</li>
												<li class="w-02">SOURCE</li>
												<li class="w-03">DATE</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
												<li class="w-02">BBC</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
												<li class="w-02">Yahoo!</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Police shoot and shoot and kill 2 at road...</a></li>
												<li class="w-02">Kenya.gov</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Young boy wandering wandering alone in...</a></li>
												<li class="w-02">CNBC</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Church burned in burned in Eldoret with...</a></li>
												<li class="w-02">BBC</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Thousands trapped in trapped in forest....</a></li>
												<li class="w-02">Yahoo!</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Police shoot and shoot and kill 2 at road...</a></li>
												<li class="w-02">Kenya.gov</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
										<li>
											<ul>
												<li class="w-01"><a href="#">Young boy wandering wandering alone in...</a></li>
												<li class="w-02">CNBC</li>
												<li class="w-03">18 Jan 2008</li>
											</ul>
										</li>
									</ul>
									<a class="btn-more" href="#"><span>MORE</span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- end start incidents and news blocks -->
			</div>
		</div>