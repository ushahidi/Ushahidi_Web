			<div class="bg">
				<h2><?php echo $title; ?> <span>(<?php echo $reports_total; ?>)</span><a href="create">Create New Report</a></h2>
				<form action="#">
					<!-- report-form -->
					<div class="report-form">
						<div class="head">
							<h3>New Report</h3>
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-report.gif" class="save-rep-btn" />
						</div>
						<!-- f-col -->
						<div class="f-col">
							<div class="row">
								<h4>Incident Title</h4>
								<input type="text" class="text title" />
							</div>
							<div class="row">
								<h4>Description <span>Please include as much detail as possible.</span></h4>
								<textarea rows="12" cols="40"></textarea>
							</div>
							<div class="row">
								<div class="date-box">
									<h4>Date <span>(mm/dd/yyyy)</span></h4>
									<input type="text" class="text" />
									<a href="#" class="calendar">calendar</a>
								</div>
								<div class="time">
									<h4>Time <span>(Approximate)</span></h4>
									<input type="text" class="text" />
									<span class="dots">:</span>
									<input type="text" class="text" />
									<span class="sel-holder">
										<select>
											<option selected="selected">&nbsp;</option>
											<option>&nbsp;</option>
										</select>
									</span>
								</div>
							</div>
							<div class="row">
								<h4><a href="#" class="new-cat">new category</a>Categories <span>Select as many as needed.</span></h4>
								<div class="category">
									<ul>
										<li><label><input type="checkbox" class="check-box" /><span>Category 1</span></label></li>
										<li><label><input type="checkbox" class="check-box" /><span>Category 2</span></label></li>
										<li><label><input type="checkbox" class="check-box" /><span>Category 3</span></label></li>
										<li><label><input type="checkbox" class="check-box" /><span>Category 4</span></label></li>
										<li><label><input type="checkbox" class="check-box" /><span>Category 5</span></label></li>
									</ul>
									<ul>
										<li><label><input type="checkbox" class="check-box" /><span>Category 6</span></label></li>
										<li><label><input type="checkbox" class="check-box" /><span>Category 7</span></label></li>
										<li><label><input type="checkbox" class="check-box" /><span>Category 8</span></label></li>
									</ul>
								</div>
							</div>
						</div>
						<!-- f-col-1 -->
						<div class="f-col-1">
							<div class="incident-location">
								<h4>Incident Location</h4>
								<div class="location-info">
									<span>Latitude:</span>
									<strong>-1.274359</strong>
									<span>Longitude:</span>
									<strong>36.813106</strong>
								</div>
								<img src="<?php echo url::base() ?>media/img/admin/img-map.gif" alt=""/>
							</div>
							<div class="row">
								<div class="town">
									<h4>Nearest Town</h4>
									<span class="sel-holder">
										<select>
											<option selected="selected">item-1</option>
											<option>item-2</option>
										</select>
									</span>
								</div>
								<div class="location">
									<h4>Location</h4>
									<span class="sel-holder">
										<select>
											<option selected="selected">item-1</option>
											<option>item-2</option>
										</select>
									</span>
								</div>
							</div>
							<div class="row link-row">
								<h4>News Source Link</h4>
								<input type="text" class="text long" />
								<a href="#" class="add">add</a>
							</div>
							<div class="row link-row">
								<h4>Video Link</h4>
								<input type="text" class="text long" />
							</div>
							<div class="row link-row second">
								<input type="text" class="text long" />
								<a href="#" class="add">add</a>
								<a href="#" class="rem">remove</a>
							</div>
						</div>
						<!-- f-col-bottom -->
						<div class="f-col-bottom">
							<div class="row">
								<h4>Personal Information <span>Optional.</span></h4>
								<label>
									<span>First Name</span>
									<input type="text" class="text" />
								</label>
								<label>
									<span>Last Name</span>
									<input type="text" class="text" />
								</label>
							</div>
							<div class="row">
								<label>
									<span>Email Address</span>
									<input type="text" class="text email" />
								</label>
							</div>
						</div>
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-report.gif" class="save-rep-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save-and-close.gif" class="save-close-btn" />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
					</div>
				</form>
			</div>
