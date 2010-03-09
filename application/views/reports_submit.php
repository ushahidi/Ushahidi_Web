<?php 
/**
 * Reports submit view page.
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
				<div id="content">
					<div class="content-bg">
						<!-- start report form block -->
						<?php print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'reportForm', 'name' => 'reportForm', 'class' => 'gen_forms')); ?>
						<input type="hidden" name="latitude" id="latitude" value="<?php echo $form['latitude']; ?>">
						<input type="hidden" name="longitude" id="longitude" value="<?php echo $form['longitude']; ?>">
						<div class="big-block">
							<h1><?php echo Kohana::lang('ui_main.reports_submit_new'); ?></h1>
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
							<div class="row">
								<input type="hidden" name="form_id" id="form_id" value="<?php echo $id?>">
							</div>
							<div class="report_left">
								<div class="report_row">
									<h4><?php echo Kohana::lang('ui_main.reports_title'); ?></h4>
									<?php print form::input('incident_title', $form['incident_title'], ' class="text long"'); ?>
								</div>
								<div class="report_row">
									<h4><?php echo Kohana::lang('ui_main.reports_description'); ?></h4>
									<?php print form::textarea('incident_description', $form['incident_description'], ' rows="10" class="textarea long" ') ?>
								</div>
								<div class="report_row" id="datetime_default">
									<h4><a href="#" id="date_toggle" class="show-more"><?php echo Kohana::lang('ui_main.modify_date'); ?></a><?php echo Kohana::lang('ui_main.date_time'); ?>: 
										<?php echo Kohana::lang('ui_main.today_at')." "."<span id='current_time'>".$form['incident_hour']
											.":".$form['incident_minute']." ".$form['incident_ampm']."</span>"; ?></h4>
								</div>
								<div class="report_row hide" id="datetime_edit">
									<div class="date-box">
										<h4><?php echo Kohana::lang('ui_main.reports_date'); ?></h4>
										<?php print form::input('incident_date', $form['incident_date'], ' class="text short"'); ?>								
										<script type="text/javascript">
											$().ready(function() {
												$("#incident_date").datepicker({ 
													showOn: "both", 
													buttonImage: "<?php echo url::base() ?>media/img/icon-calendar.gif", 
													buttonImageOnly: true 
												});
											});
										</script>
									</div>
									<div class="time">
										<h4><?php echo Kohana::lang('ui_main.reports_time'); ?></h4>
										<?php
											for ($i=1; $i <= 12 ; $i++) { 
												$hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i);	 // Add Leading Zero
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
										<script type="text/javascript">
var now = new Date();
var h=now.getHours();
var m=now.getMinutes();
var ampm="am";
if (h>=12) ampm="pm"; 
if (h>12) h-=12;
var hs=(h<10)?("0"+h):h;
var ms=(m<10)?("0"+m):m;
$("#current_time").text(hs+":"+ms+" "+ampm);
$("#incident_hour option[value='"+hs+"']").attr("selected","true");
$("#incident_minute option[value='"+ms+"']").attr("selected","true");
$("#incident_ampm option[value='"+ampm+"']").attr("selected","true");

										</script>

								<div class="report_row">
									<h4><?php echo Kohana::lang('ui_main.reports_categories'); ?></h4>
									<div class="report_category" id="categories">
										<?php
										$selected_categories = array();
                    if (!empty($form['incident_category']) && is_array($form['incident_category'])) {
											$selected_categories = $form['incident_category'];
										}
										$columns = 2;
										echo category::tree($categories, $selected_categories, 'incident_category', $columns);
										?>
									</div>
								</div>
								
								<div id="custom_forms">
									
                                    <?php
                                    
									foreach ($disp_custom_fields as $field_id => $field_property)
									{
										echo "<div class=\"report_row\">";
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

								<div class="report_optional">
									<h3><?php echo Kohana::lang('ui_main.reports_optional'); ?></h3>
									<div class="report_row">
											 <h4><?php echo Kohana::lang('ui_main.reports_first'); ?></h4>
											 <?php print form::input('person_first', $form['person_first'], ' class="text long"'); ?>
									</div>
									<div class="report_row">
										<h4><?php echo Kohana::lang('ui_main.reports_last'); ?></h4>
										<?php print form::input('person_last', $form['person_last'], ' class="text long"'); ?>
									</div>
									<div class="report_row">
										<h4><?php echo Kohana::lang('ui_main.reports_email'); ?></h4>
										<?php print form::input('person_email', $form['person_email'], ' class="text long"'); ?>
									</div>
								</div>
							</div>
							<div class="report_right">
								<?php if (!$multi_country)
											{
								?>
								<div class="report_row">
									<h4><?php echo Kohana::lang('ui_main.reports_find_location'); ?></h4>
									<?php print form::dropdown('select_city',$cities,'', ' class="select" '); ?>
								</div>
								<?php
									 }
								?>
								<div class="report_row">
									<div id="divMap" class="report_map"></div>
									<div class="report-find-location">
										<?php print form::input('location_find', '', 'title='.Kohana::lang('ui_main.location_example').' class="findtext"'); ?>
										<div style="float:left;margin:9px 0 0 5px;"><input type="button" name="button" id="button" value="<?php echo Kohana::lang('ui_main.find_location'); ?>" class="btn_find" /></div>
										<div id="find_loading" class="report-find-loading"></div>
										<div style="clear:both;" id="find_text"><?php echo Kohana::lang('ui_main.pinpoint_location'); ?>.</div>
									</div>
								</div>
								
								<div class="report_row">
									<h4><?php echo Kohana::lang('ui_main.reports_location_name'); ?><br /><span class="example"><?php echo Kohana::lang('ui_main.detailed_location_example'); ?></span></h4>
									<?php print form::input('location_name', $form['location_name'], ' class="text long"'); ?>
								</div>
			
								<!-- News Fields -->
								<div id="divNews" class="report_row">
									<h4><?php echo Kohana::lang('ui_main.reports_news'); ?></h4>
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
												print "<a href=\"#\" class=\"rem\"	onClick='removeFormField(\"#" . $this_field . "_" . $i . "\"); return false;'>remove</a>";
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
									<h4><?php echo Kohana::lang('ui_main.reports_video'); ?></h4>
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
													print "<a href=\"#\" class=\"rem\"	onClick='removeFormField(\"#" . $this_field . "_" . $i . "\"); return false;'>remove</a>";
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
									<h4><?php echo Kohana::lang('ui_main.reports_photos'); ?></h4>
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
													print "<a href=\"#\" class=\"rem\"	onClick='removeFormField(\"#" . $this_field . "_" . $i . "\"); return false;'>remove</a>";
												}
												print "</div>\n";
												$i++;
											}
										}
										print "<input type=\"hidden\" name=\"$this_startid\" value=\"$i\" id=\"$this_startid\">";
									?>
	
								</div>
													
								<div class="report_row">
									<input name="submit" type="submit" value="<?php echo Kohana::lang('ui_main.reports_btn_submit'); ?>" class="btn_submit" /> 
								</div>
							</div>
						</div>
						<?php print form::close(); ?>
						<!-- end report form block -->
					</div>
				</div>
			</div>
		</div>
	</div>
