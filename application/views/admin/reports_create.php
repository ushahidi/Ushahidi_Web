			<div class="bg">
				<h2><?php echo $title; ?> <span>(<?php echo $reports_total; ?>)</span><a href="create">Create New Report</a></h2>
				<?php form::open(); ?>
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
								<?php print form::input('incident_title', $form['incident_title'], ' class="text title"'); ?>
							</div>
							<div class="row">
								<h4>Description <span>Please include as much detail as possible.</span></h4>
								<?php print form::textarea('incident_description', $form['incident_description'], ' rows="12" cols="40"') ?>
							</div>
							<div class="row">
								<div class="date-box">
									<h4>Date <span>(mm/dd/yyyy)</span></h4>
									<?php print form::input('incident_date', $form['incident_date'], ' class="text"'); ?>								
									<script type="text/javascript">
										$("#incident_date").datepicker({ 
										    showOn: "both", 
										    buttonImage: "<?php echo url::base() ?>media/img/admin/icon-calendar.gif", 
										    buttonImageOnly: true 
										});
								    </script>					    
								</div>
								<div class="time">
									<h4>Time <span>(Approximate)</span></h4>
									<?php
									for ($i=0; $i <= 12 ; $i++) { 
										$hour_array[] = sprintf("%02d", $i); 	// Add Leading Zero 
									}
									for ($j=0; $j <= 59 ; $j++) { 
										$minute_array[] = sprintf("%02d", $j);	// Add Leading Zero
									}
									$ampm_array = array('am'=>'am','pm'=>'pm');
									print '<span class="sel-holder">' . form::dropdown('incident_hour',$hour_array,$form['incident_hour']) . '</span>';
									print '<span class="dots">:</span>';
									print '<span class="sel-holder">' . form::dropdown('incident_minute',$minute_array,$form['incident_minute']) . '</span>';
									print '<span class="dots">:</span>';
									print '<span class="sel-holder">' . form::dropdown('incident_ampm',$ampm_array,$form['incident_ampm']) . '</span>';
									?>
								</div>
							</div>
							<div class="row">
								<h4><a href="#" id="category_toggle" class="new-cat">new category</a>Categories <span>Select as many as needed.</span></h4>
								<script type="text/javascript">
									$('#category_add').show('slow');
									$('a#category_toggle').click(function() {
									  $('#category_add').toggle(400);
									  return false;
									});
								</script>
								<div id="category_add" class="category_add">
									<div class="row">
										<h4>Category Name</h4>
										<?php print form::input('incident_title', $form['incident_title'], ' class="text"'); ?>
									</div>
									<div class="row">
										<h4>Category Description</h4>
										<?php print form::input('incident_description', $form['incident_description'], ' class="text"') ?>
									</div>									
								</div>
								<div class="category">
									<?php
									$this_col = 1;		//First column
									$max_col = round($categories_total/2);		//Maximum number of columns
									foreach ($categories as $category => $category_extra)
									{
										$category_title = $category_extra[0];
										$category_color = $category_extra[1];
										if ($this_col == 1) print "<ul>";
										
										print "\n<li><label>";
										print form::checkbox('category[]', $category, FALSE, ' class="check-box"');
										print "$category_title";
										print "</label></li>";
										
										if ($this_col == $max_col) print "\n</ul>\n";
										
										if ($this_col < $max_col){
											$this_col++;
										} else {
											$this_col = 1;
										}
									}
									
									?>
								</div>
							</div>
						</div>
						<!-- f-col-1 -->
						<div class="f-col-1">
							<div class="incident-location">
								<h4>Incident Location</h4>
								<div class="location-info">
									<span>Latitude:</span>
									<?php print form::input('latitude', '', ' class="text"'); ?>
									<span>Longitude:</span>
									<?php print form::input('longitude', '', ' class="text"'); ?>
								</div>
								<script type="text/javascript" charset="utf-8">
									<?php echo $mapjs; ?>
								</script>
								<div id="divMap" style="width: 494px; height: 400px; float:left; margin:-1px; border:3px solid #c2c2c2;"></div>
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
							
							<!-- News Fields -->
							<div class="row link-row">
								<h4>News Source Link</h4>
								<input type="hidden" name="news_id" value="1" id="news_id">
							</div>
							<div id="divNews">
								<div class="row link-row">
									<?php print form::input('news[]', 'news', ' class="text long"'); ?>
									<a href="#" class="add" onClick="addFormField('divNews','news','news_id'); return false;">add</a>
								</div>
							</div>

							<!-- Video Fields -->
							<div class="row link-row">
								<h4>Video Link</h4>
								<input type="hidden" name="video_id" value="1" id="video_id">
							</div>
							<div id="divVideo">
								<div class="row link-row">
									<?php print form::input('video[]', 'video', ' class="text long"'); ?>
									<a href="#" class="add" onClick="addFormField('divVideo','video','video_id'); return false;">add</a>
								</div>
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
				<?php form::close(); ?>
			</div>
