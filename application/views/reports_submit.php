		<div id="content">
			<div class="content-bg">
				<!-- start report form block -->
				<?php print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'reportForm', 'name' => 'reportForm')); ?>
				<input type="hidden" name="latitude" id="latitude" value="<?php echo $form['latitude']; ?>">
				<input type="hidden" name="longitude" id="longitude" value="<?php echo $form['longitude']; ?>">
				<div class="big-block">
					<div class="big-block-top">
						<div class="big-block-bottom">
							<h1>Submit A New Report</h1>
							<div class="report_left">
		                    	<div class="report_row">
		                        	<h4>Report Title</h4>
									<?php print form::input('incident_title', $form['incident_title'], ' class="text long"'); ?>
		                        </div>
		                        <div class="report_row">
		                        	<h4>Description</h4>
									<?php print form::textarea('incident_description', $form['incident_description'], ' rows="10" class="textarea long" ') ?>
		                        </div>
		                        <div class="report_row">
		                       	  <div class="date-box">
		                            	<h4>Date</h4>
										<?php print form::input('incident_date', $form['incident_date'], ' class="text short"'); ?>								
										<script type="text/javascript">
											$("#incident_date").datepicker({ 
											    showOn: "both", 
											    buttonImage: "<?php echo url::base() ?>media/img/icon-calendar.gif", 
											    buttonImageOnly: true 
											});
									    </script>
		                            </div>
		                          <div class="time">
		                            	<h4>Time</h4>
						    		  	<?php
										for ($i=1; $i <= 12 ; $i++) { 
											$hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i); 	// Add Leading Zero
										}
										for ($j=0; $j <= 59 ; $j++) { 
											$minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);	// Add Leading Zero
										}
										$ampm_array = array('pm'=>'pm','am'=>'am');
										print form::dropdown('incident_hour',$hour_array,$form['incident_hour']);
										print '<span class="dots">:</span>';
										print form::dropdown('incident_minute',$minute_array,$form['incident_minute']);
										print '<span class="dots">:</span>';
										print form::dropdown('incident_ampm',$ampm_array,$form['incident_ampm']);
										?>
		                          </div>
		                            <div style="clear:both; display:block;" id="incident_date_time"></div>
		                        </div>
		                        <div class="report_row">
		                        	<h4>Categories</h4>
							    	<div class="report_category" id="categories">
										<?php echo $categories; ?>
									</div>
		                        </div>
								
								<div class="report_optional">
									<h3>Optional Information</h3>
		                        	<div class="report_row">
			                        	<h4>First Name</h4>
								    	<input type="text" name="textfield" id="textfield" class="text long" />
			                        </div>
			                        <div class="report_row">
			                        	<h4>Last Name</h4>
								    	<input type="text" name="textfield" id="textfield" class="text long" />
			                        </div>
			                        <div class="report_row">
			                        	<h4>Email</h4>
								    	<input type="text" name="textfield" id="textfield" class="text long" />
			                        </div>
								</div>
							</div>
							
		               	  	<div class="report_right">
		                    	<div class="report_row">
		                        	<h4>Select A City/Town</h4>
		                            <?php print form::dropdown('select_city',$cities,'', ' class="select" '); ?>
		                    	</div>
		                        <div class="report_row">
		                        	<div id="divMap" class="report_map"></div>
		                        </div>
		                        <div class="report_row">
		                        	<h4>Location Name</h4>
									<?php print form::input('location_name', $form['location_name'], ' class="text long"'); ?>
		                        </div>

								<!-- News Fields -->
								<div id="divNews" class="report_row">
			                        <h4>News Source Link</h4>
									<?php
									$this_div = "divNews";
									$this_field = "incident_news";
									$this_startid = "news_id";
									$this_field_type = "text";

									if (empty($form[$this_field]))
									{
										$i = 1;
										print "<div class=\"report_row\">";
										print form::input($this_field . '[]', '', ' class="text long2"');
										print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
										print "</div>";
									}
									else
									{
										$i = 0;
										foreach ($form[$this_field] as $value) {									
											print "<div class=\"report_row\" id=\"$i\">\n";
											
											print form::input($this_field . '[]', $value, ' class="text long2"');
											print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
											if ($i != 0)
											{
												print "<a href=\"#\" class=\"rem\"  onClick='removeFormField(\"#" . $this_field . "_" . $i . "\"); return false;'>remove</a>";
											}
											print "</div>\n";
											$i++;
										}
									}
									print "<input type=\"hidden\" name=\"$this_startid\" value=\"$i\" id=\"$this_startid\">";
									?>
								</div>


								<!-- Video Fields -->
								<div id="divVideo" class="report_row">
									<h4>Video Link</h4>
									<?php
									$this_div = "divVideo";
									$this_field = "incident_video";
									$this_startid = "video_id";
									$this_field_type = "text";

									if (empty($form[$this_field]))
									{
										$i = 1;
										print "<div class=\"report_row\">";
										print form::input($this_field . '[]', '', ' class="text long2"');
										print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
										print "</div>";
									}
									else
									{
										$i = 0;
										foreach ($form[$this_field] as $value) {									
											print "<div class=\"report_row\" id=\"$i\">\n";
											
											print form::input($this_field . '[]', $value, ' class="text long2"');
											print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
											if ($i != 0)
											{
												print "<a href=\"#\" class=\"rem\"  onClick='removeFormField(\"#" . $this_field . "_" . $i . "\"); return false;'>remove</a>";
											}
											print "</div>\n";
											$i++;
										}
									}
									print "<input type=\"hidden\" name=\"$this_startid\" value=\"$i\" id=\"$this_startid\">";
									?>
								</div>
								
								
								<!-- Photo Fields -->
								<div id="divPhoto" class="report_row">
									<h4>Upload Photos</h4>
									<?php
									$this_div = "divPhoto";
									$this_field = "incident_photo";
									$this_startid = "photo_id";
									$this_field_type = "file";

									if (empty($form[$this_field]['name'][0]))
									{
										$i = 1;
										print "<div class=\"report_row\">";
										print form::upload($this_field . '[]', '', ' class="file long2"');
										print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
										print "</div>";
									}
									else
									{
										$i = 0;
										foreach ($form[$this_field]['name'] as $value) 
										{
											print "<div class=\"report_row\" id=\"$i\">\n";
											
											// print "\"<strong>" . $value . "</strong>\"" . "<BR />";
											print form::upload($this_field . '[]', $value, ' class="file long2"');
											print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
											if ($i != 0)
											{
												print "<a href=\"#\" class=\"rem\"  onClick='removeFormField(\"#" . $this_field . "_" . $i . "\"); return false;'>remove</a>";
											}
											print "</div>\n";
											$i++;
										}
									}
									print "<input type=\"hidden\" name=\"$this_startid\" value=\"$i\" id=\"$this_startid\">";
									?>
								</div>
										
								<div style="clear:both;"></div>
		                  	</div>
                    
		                    <div class="report_bottom">
		                        <div class="report_row">
		                        	<input name="submit" type="submit" value="Submit Report" class="btn_blue" />
		                        </div>
		                    </div>
					  </div>
					</div>
				</div>
				<?php print form::close(); ?>
				<!-- end report form block -->
			</div>
		</div>