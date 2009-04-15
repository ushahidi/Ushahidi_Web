<?php 
/**
 * Reports edit view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2><?php print $title; ?> <span></span><a href="<?php print url::base() ?>admin/reports">View Reports</a><a href="<?php print url::base() ?>admin/reports/download">Download Reports</a><a href="<?php print url::base() ?>admin/reports/upload">Upload Reports</a></h2>
				<?php print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'reportForm', 'name' => 'reportForm')); ?>
					<input type="hidden" name="save" id="save" value="">
					<input type="hidden" name="location_id" id="location_id" value="<?php print $form['location_id']; ?>">
					<!-- report-form -->
					<div class="report-form">
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

						if ($form_saved) {
						?>
							<!-- green-box -->
							<div class="green-box">
								<h3>Your Report Has Been Saved!</h3>
							</div>
						<?php
						}
						?>
						<div class="head">
							<h3>New Report</h3>
							<input type="image" src="<?php print url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
							<input type="image" src="<?php print url::base() ?>media/img/admin/btn-save-report.gif" class="save-rep-btn" />
						</div>
						<!-- f-col -->
						<div class="f-col">
							<?php if ($show_messages) { ?>
							<div class="row">
								<h4 style="margin:0;padding:0;"><a href="#" id="messages_toggle" class="show-messages">Show Messages</a>&nbsp;</h4>
								<!--messages table goes here-->
			                    <div id="show_messages">
									<?php
									foreach ($all_messages as $message) {
										echo "<div class=\"message\">";
										echo "<strong><u>" . $message->message_from . "</u></strong> - ";
										echo $message->message;
										echo "</div>";
									}
									?>
								</div>
							</div>
							<?php } ?>
							<div class="row">
								<h4>Form <span>(Select A Form Type)</span></h4>
								<span class="sel-holder">
									<?php print form::dropdown('form_id', $forms, $form['form_id'],
										' onchange="formSwitch(this.options[this.selectedIndex].value, \''.$id.'\')"') ?>
								</span>
								<div id="form_loader" style="float:left;"></div>
							</div>
							<div class="row">
								<h4>Item Title</h4>
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
									<?php print $date_picker_js; ?>				    
								</div>
								<div class="time">
									<h4>Time <span>(Approximate)</span></h4>
									<?php
									print '<span class="sel-holder">' .
								    form::dropdown('incident_hour', $hour_array,
									$form['incident_hour']) . '</span>';
									
									print '<span class="dots">:</span>';
									
									print '<span class="sel-holder">' .
									form::dropdown('incident_minute',
									$minute_array, $form['incident_minute']) .
									'</span>';
									print '<span class="dots">:</span>';
									
									print '<span class="sel-holder">' .
									form::dropdown('incident_ampm', $ampm_array,
									$form['incident_ampm']) . '</span>';
									?>
								</div>
							</div>
							<div class="row">
								<h4><a href="#" id="category_toggle" class="new-cat">new category</a>Categories 
								<span>Select as many as needed.</span></h4>
								<?php print $new_category_toggle_js; ?>
								<!--category_add form goes here-->
			                    <div id="category_add" class="category_add">
			                        <?php
			                        print '<p>Add New Category<hr/></p>';
                                    print form::label(array("id"=>"category_name_label", "for"=>"category_name"), 'Name');
                                    print '<br/>';
                                    print form::input('category_name', $new_categories_form['category_name'], 'class=""');
                                    print '<br/>';
                                    print form::label(array("id"=>"description_label", "for"=>"description"), 'Description');
                                    print '<br/>';
                                    print form::input('category_description', $new_categories_form['category_description'], 'class=""');
                                    print '<br/>';
                                    print form::label(array("id"=>"color_label", "for"=>"color"), 'Color');
                                    print '<br/>';
                                    print form::input('category_color', $new_categories_form['category_color'], 'class=""');
                                    print $color_picker_js;
                                    print '<br/>';
                                    print '<span>';
                                    print '<a href="#" id="add_new_category">Add</a>';
                                    print '</span>';
                                    print form::close();
                                    ?> 
                                </div>

			                    <div class="category">
                        	    <?php
                        		//format categories for 2 column display
                                $this_col = 1; // First column
                                $max_col = round($categories_total/2); // Maximum number of columns
                                
                                foreach ($categories as $category => $category_extra)
                                {
                                    $category_title = $category_extra[0];
                                    $category_color = $category_extra[1];
                                    if ($this_col == 1) 
                                        print "<ul>";
                                
                                    if (!empty($form['incident_category']) 
                                        && in_array($category, $form['incident_category'])) {
                                        $category_checked = TRUE;
                                    }
                                    else
                                    {
                                        $category_checked = FALSE;
                                    }
                                                                                                    
                                    print "<li><label>";
                                    print form::checkbox('incident_category[]', $category, $category_checked, ' class="check-box"');
                                    print "$category_title";
                                    print "</label></li>";
                               
                                    if ($this_col == $max_col) 
                                        print "</ul>\n";
                              
                                    if ($this_col < $max_col)
                                    {
                                        $this_col++;
                                    } 
                                    else 
                                    {
                                        $this_col = 1;
                                    }
                                }
                                
                                ?>
			                        <ul id="user_categories">
			                        </ul>
								</div>
							</div>
							<div id="custom_forms">
								<?php
								foreach ($disp_custom_fields as $field_id => $field_property)
								{
									echo "<div class=\"row\">";
									echo "<h4>" . $field_property['field_name'] . "</h4>";
									if ($field_property['field_type'] == 1)
									{ // Text Field
										// Is this a date field?
										if ($field_property['field_isdate'] == 1)
										{
											echo form::input('custom_field['.$field_id.']', $form['custom_field'][$field_id],
												' id="custom_field_'.$field_id.'" class="text"');
											echo "<script type=\"text/javascript\">
													$(document).ready(function() {
													$(\"#custom_field_".$field_id."\").datepicker({ 
													showOn: \"both\", 
													buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\", 
													buttonImageOnly: true 
													});
													});
												</script>";
										}
										else
										{
											echo form::input('custom_field['.$field_id.']', $form['custom_field'][$field_id],
												' id="custom_field_'.$field_id.'" class="text custom_text"');
										}
									}
									elseif ($field_property['field_type'] == 2)
									{ // TextArea Field
										echo form::textarea('custom_field['.$field_id.']', $form['custom_field'][$field_id], ' class="custom_text" rows="3"');
									}
									echo "</div>";
								}
								?>
							</div>			
						</div>
						<!-- f-col-1 -->
						<div class="f-col-1">
							<div class="incident-location">
								<h4>Incident Location</h4>
								<div class="location-info">
									<span>Latitude:</span>
									<?php print form::input('latitude', $form['latitude'], ' readonly="readonly" class="text"'); ?>
									<span>Longitude:</span>
									<?php print form::input('longitude', $form['longitude'], ' readonly="readonly" class="text"'); ?>
								</div>
								<div id="divMap" style="width: 494px; height: 400px; float:left; margin:-1px; border:3px solid #c2c2c2;"></div>
							</div>
							<div class="row">
								<div class="town">
									<h4>Location Name</h4>
									<?php print form::input('location_name', $form['location_name'], ' class="text"'); ?>
								</div>
								<div class="location">
									<h4>Location</h4>
									<span class="sel-holder">
										<?php print form::dropdown('country_id',$countries,$form['country_id']); ?>
									</span>
									&nbsp;&nbsp;<a href="#" id="findAddress" onClick="return false;">Find!</a>
								</div>
							</div>
				
				
							<!-- News Fields -->
							<div class="row link-row">
								<h4>News Source Link</h4>
							</div>
							<div id="divNews">
								<?php
								$this_div = "divNews";
								$this_field = "incident_news";
								$this_startid = "news_id";
								$this_field_type = "text";
					
								if (empty($form[$this_field]))
								{
									$i = 1;
									print "<div class=\"row link-row\">";
									print form::input($this_field . '[]', '', ' class="text long"');
									print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
									print "</div>";
								}
								else
								{
									$i = 0;
									foreach ($form[$this_field] as $value) {									
										print "<div ";
										if ($i != 0) {
											print "class=\"row link-row second\" id=\"" . $this_field . "_" . $i . "\">\n";
										}
										else
										{
											print "class=\"row link-row\" id=\"$i\">\n";
										}
										print form::input($this_field . '[]', $value, ' class="text long"');
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
							<div class="row link-row">
								<h4>Video Link</h4>
							</div>
							<div id="divVideo">
								<?php
								$this_div = "divVideo";
								$this_field = "incident_video";
								$this_startid = "video_id";
								$this_field_type = "text";
					
								if (empty($form[$this_field]))
								{
									$i = 1;
									print "<div class=\"row link-row\">";
									print form::input($this_field . '[]', '', ' class="text long"');
									print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
									print "</div>";
								}
								else
								{
									$i = 0;
									foreach ($form[$this_field] as $value) {									
										print "<div ";
										if ($i != 0) {
											print "class=\"row link-row second\" id=\"" . $this_field . "_" . $i . "\">\n";
										}
										else
										{
											print "class=\"row link-row\" id=\"$i\">\n";
										}
										print form::input($this_field . '[]', $value, ' class="text long"');
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
							<div class="row link-row">
								<h4>Upload Photos</h4>
								<?php								
    								if ($incident != "0")
                        			{
                        				// Retrieve Media
                        				foreach($incident->media as $photo) 
                        				{
                        					if ($photo->media_type == 1)
                        					{
                        						print "<div class=\"report_thumbs\" id=\"photo_". $photo->id ."\">";
                        						print "<img src=\"" . url::base() . "media/uploads/" . $photo->media_thumb . "\" >";
                        						print "&nbsp;&nbsp;<a href=\"#\" onClick=\"deletePhoto('". $photo->id ."', 'photo_". $photo->id ."'); return false;\" >Delete</a>";
                        						print "</div>";
                        					}
                        				}
                        			}
			                    ?>
							</div>
							<div id="divPhoto">
								<?php
								$this_div = "divPhoto";
								$this_field = "incident_photo";
								$this_startid = "photo_id";
								$this_field_type = "file";
					
								if (empty($form[$this_field]['name'][0]))
								{
									$i = 1;
									print "<div class=\"row link-row\">";
									print form::upload($this_field . '[]', '', ' class="text long"');
									print "<a href=\"#\" class=\"add\" onClick=\"addFormField('$this_div','$this_field','$this_startid','$this_field_type'); return false;\">add</a>";
									print "</div>";
								}
								else
								{
									$i = 0;
									foreach ($form[$this_field]['name'] as $value) 
									{
										print "<div ";
										if ($i != 0) {
											print "class=\"row link-row second\" id=\"" . $this_field . "_" . $i . "\">\n";
										}
										else
										{
											print "class=\"row link-row\" id=\"$i\">\n";
										}
										// print "\"<strong>" . $value . "</strong>\"" . "<BR />";
										print form::upload($this_field . '[]', $value, ' class="text long"');
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
						</div>
						<!-- f-col-bottom -->
						<div class="f-col-bottom-container">
							<div class="f-col-bottom">
								<div class="row">
									<h4>Personal Information <span>Optional.</span></h4>
									<label>
										<span>First Name</span>
										<?php print form::input('person_first', $form['person_first'], ' class="text"'); ?>
									</label>
									<label>
										<span>Last Name</span>
										<?php print form::input('person_last', $form['person_last'], ' class="text"'); ?>
									</label>
								</div>
								<div class="row">
									<label>
										<span>Email Address</span>
										<?php print form::input('person_email', $form['person_email'], ' class="text"'); ?>
									</label>
								</div>
							</div>
							<!-- f-col-bottom-1 -->
							<div class="f-col-bottom-1">
								<h4>Information Evaluation</h4>
								<div class="row">
									<div class="f-col-bottom-1-col">Approve this report?</div>
									<input type="radio" name="incident_active" value="1"
									<?php if ($form['incident_active'] == 1)
									{
										echo " checked=\"checked\" ";
									}?>> Yes
									<input type="radio" name="incident_active" value="0"
									<?php if ($form['incident_active'] == 0)
									{
										echo " checked=\"checked\" ";
									}?>> No
								</div>
								<div class="row">
									<div class="f-col-bottom-1-col">Verify this report?</div>
									<input type="radio" name="incident_verified" value="1"
									<?php if ($form['incident_verified'] == 1)
									{
										echo " checked=\"checked\" ";
									}?>> Yes
									<input type="radio" name="incident_verified" value="0"
									<?php if ($form['incident_verified'] == 0)
									{
										echo " checked=\"checked\" ";
									}?>> No									
								</div>
								<div class="row">
									<div class="f-col-bottom-1-col">Source Reliability:</div>
									<?php print form::dropdown('incident_source', $incident_source_array, $form['incident_source']) ?>									
								</div>
								<div class="row">
									<div class="f-col-bottom-1-col">Information Probability:</div>
									<?php print form::dropdown('incident_information', $incident_information_array, $form['incident_information']) ?>									
								</div>								
							</div>
							<div style="clear:both;"></div>
						</div>
						<input id="save_only" type="image" src="<?php print url::base() ?>media/img/admin/btn-save-report.gif" class="save-rep-btn" />
						<input id="save_close" type="image" src="<?php print url::base() ?>media/img/admin/btn-save-and-close.gif" class="save-close-btn" />
						<input id="cancel" type="image" src="<?php print url::base() ?>media/img/admin/btn-cancel.gif" class="cancel-btn" />
					</div>
				<?php print form::close(); ?>
			</div>
