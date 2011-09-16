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
				<h2>
					<?php admin::reports_subtabs("edit"); ?>
				</h2>
				<?php print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'reportForm', 'name' => 'reportForm')); ?>
					<input type="hidden" name="save" id="save" value="">
					<input type="hidden" name="location_id" id="location_id" value="<?php print $form['location_id']; ?>">
					<input type="hidden" name="incident_zoom" id="incident_zoom" value="<?php print $form['incident_zoom']; ?>">
					<input type="hidden" name="country_name" id="country_name" value="<?php echo $form['country_name'];?>" />
					<!-- report-form -->
					<div class="report-form">
						<?php
						if ($form_error) {
						?>
							<!-- red-box -->
							<div class="red-box">
								<h3><?php echo Kohana::lang('ui_main.error');?></h3>
								<ul>
								<?php
								foreach ($errors as $error_item => $error_description)
								{
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
								<h3><?php echo Kohana::lang('ui_main.report_saved');?></h3>
							</div>
						<?php
						}
						?>
						<div class="head">
							<h3><?php echo $id ? Kohana::lang('ui_main.edit_report') : Kohana::lang('ui_main.new_report'); ?></h3>
							<div class="btns" style="float:right;">
								<ul>
									<li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_main.save_report'));?></a></li>
									<li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
									<li><a href="<?php echo url::base().'admin/reports/';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a>&nbsp;&nbsp;&nbsp;</li>
									<?php if ($id) {?>
									<li><a href="<?php echo $previous_url;?>" class="btns_gray">&laquo; <?php echo strtoupper(Kohana::lang('ui_main.previous'));?></a></li>
									<li><a href="<?php echo $next_url;?>" class="btns_gray"><?php echo strtoupper(Kohana::lang('ui_main.next'));?> &raquo;</a></li>
									<?php } ?>
								</ul>
							</div>
						</div>
						<!-- f-col -->
						<div class="f-col">
							<?php
							// Action::report_pre_form_admin - Runs right before report form is rendered
							Event::run('ushahidi_action.report_pre_form_admin', $id);
							?>
							<?php if ($show_messages) { ?>
							<div class="row">
								<h4 style="margin:0;padding:0;"><a href="#" id="messages_toggle" class="show-messages"><?php echo Kohana::lang('ui_main.show_messages');?></a>&nbsp;</h4>
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
								<h4><?php echo Kohana::lang('ui_main.form');?> <span>(<?php echo Kohana::lang('ui_main.select_form_type');?>)</span></h4>
								<span class="sel-holder">
									<?php print form::dropdown('form_id', $forms, $form['form_id'],
										' onchange="formSwitch(this.options[this.selectedIndex].value, \''.$id.'\')"') ?>
								</span>
								<div id="form_loader" style="float:left;"></div>
							</div>
							<div class="row">
								<h4><?php echo Kohana::lang('ui_main.title');?></h4>
								<?php print form::input('incident_title', $form['incident_title'], ' class="text title"'); ?>
							</div>
							<div class="row">
								<h4><?php echo Kohana::lang('ui_main.description');?> <span><?php echo Kohana::lang('ui_main.include_detail');?>.</span></h4>
								<?php print form::textarea('incident_description', $form['incident_description'], ' rows="12" cols="40"') ?>
							</div>

							<?php
							// Action::report_form_admin - Runs just after the report description
							Event::run('ushahidi_action.report_form_admin', $id);
							?>

							<?php
							if (!($id))
							{ // Use default date for new report
								?>
								<div class="row" id="datetime_default">
									<h4><a href="#" id="date_toggle" class="new-cat"><?php echo Kohana::lang('ui_main.modify_date');?></a><?php echo Kohana::lang('ui_main.modify_date');?>: 
									<?php echo Kohana::lang('ui_main.today_at').' '.$form['incident_hour']
										.":".$form['incident_minute']." ".$form['incident_ampm']; ?></h4>
								</div>
								<?php
							}
							?>
							<div class="row <?php
								if (!($id))
								{ // Hide date editor for new report
									echo "hide";
								}?> " id="datetime_edit">
								<div class="date-box">
									<h4><?php echo Kohana::lang('ui_main.date');?> <span><?php echo Kohana::lang('ui_main.date_format');?></span></h4>
									<?php print form::input('incident_date', $form['incident_date'], ' class="text"'); ?>								
									<?php print $date_picker_js; ?>				    
								</div>
								<div class="time">
									<h4><?php echo Kohana::lang('ui_main.time');?> <span>(<?php echo Kohana::lang('ui_main.approximate');?>)</span></h4>
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
							<?php Event::run('ushahidi_action.report_form_admin_after_time', $id); ?>
							<div class="row">
								<h4><a href="#" id="category_toggle" class="new-cat"><?php echo Kohana::lang('ui_main.new_category');?></a><?php echo Kohana::lang('ui_main.categories');?> 
								<span><?php echo Kohana::lang('ui_main.select_multiple');?>.</span></h4>
								<?php print $new_category_toggle_js; ?>
								<!--category_add form goes here-->
			                    <div id="category_add" class="category_add">
			                        <?php
			                        print '<p>'.Kohana::lang('ui_main.add_new_category').'<hr/></p>';
                                    print form::label(array("id"=>"category_name_label", "for"=>"category_name"), Kohana::lang('ui_main.name'));
                                    print '<br/>';
                                    print form::input('category_name', $new_categories_form['category_name'], 'class=""');
                                    print '<br/>';
                                    print form::label(array("id"=>"description_label", "for"=>"description"), Kohana::lang('ui_main.description'));
                                    print '<br/>';
                                    print form::input('category_description', $new_categories_form['category_description'], 'class=""');
                                    print '<br/>';
                                    print form::label(array("id"=>"color_label", "for"=>"color"), Kohana::lang('ui_main.color'));
                                    print '<br/>';
                                    print form::input('category_color', $new_categories_form['category_color'], 'class=""');
                                    print $color_picker_js;
                                    print '<br/>';
                                    print '<span>';
                                    print '<a href="#" id="add_new_category">'.Kohana::lang('ui_main.add').'</a>';
                                    print '</span>';
                                    ?> 
                                </div>

			                    <div class="report_category">
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
							
						<?php echo $custom_forms; ?>
						</div>
						<!-- f-col-1 -->
						<div class="f-col-1">
							<div class="incident-location">
								<h4><?php echo Kohana::lang('ui_main.incident_location');?></h4>
								<div class="location-info">
									<span><?php echo Kohana::lang('ui_main.latitude');?>:</span>
									<?php print form::input('latitude', $form['latitude'], ' class="text"'); ?>
									<span><?php echo Kohana::lang('ui_main.longitude');?>:</span>
									<?php print form::input('longitude', $form['longitude'], ' class="text"'); ?>
								</div>
								<ul class="map-toggles">
						          <li><a href="#" class="smaller-map">Smaller map</a></li>
						          <li style="display:block;"><a href="#" class="wider-map">Wider map</a></li>
						          <li><a href="#" class="taller-map">Taller map</a></li>
						          <li><a href="#" class="shorter-map">Shorter Map</a></li>
						        </ul>
								<div id="divMap" class="map_holder_reports">
									<div id="geometryLabelerHolder" class="olControlNoSelect">
										<div id="geometryLabeler">
											<div id="geometryLabelComment">
												<span id="geometryLabel"><label><?php echo Kohana::lang('ui_main.geometry_label');?>:</label> <?php print form::input('geometry_label', '', ' class="lbl_text"'); ?></span>
												<span id="geometryComment"><label><?php echo Kohana::lang('ui_main.geometry_comments');?>:</label> <?php print form::input('geometry_comment', '', ' class="lbl_text2"'); ?></span>
											</div>
											<div>
												<span id="geometryColor"><label><?php echo Kohana::lang('ui_main.geometry_color');?>:</label> <?php print form::input('geometry_color', '', ' class="lbl_text"'); ?></span>
												<span id="geometryStrokewidth"><label><?php echo Kohana::lang('ui_main.geometry_strokewidth');?>:</label> <?php print form::dropdown('geometry_strokewidth', $stroke_width_array, ''); ?></span>
												<span id="geometryLat"><label><?php echo Kohana::lang('ui_main.latitude');?>:</label> <?php print form::input('geometry_lat', '', ' class="lbl_text"'); ?></span>
												<span id="geometryLon"><label><?php echo Kohana::lang('ui_main.longitude');?>:</label> <?php print form::input('geometry_lon', '', ' class="lbl_text"'); ?></span>
											</div>
										</div>
										<div id="geometryLabelerClose"></div>
									</div>
								</div>
							</div>
							<div class="incident-find-location">
								<div id="panel" class="olControlEditingToolbar"></div>
								<div class="btns" style="float:left;">
									<ul style="padding:4px;">
										<li><a href="#" class="btn_del_last"><?php echo strtoupper(Kohana::lang('ui_main.delete_last'));?></a></li>
										<li><a href="#" class="btn_del_sel"><?php echo strtoupper(Kohana::lang('ui_main.delete_selected'));?></a></li>
										<li><a href="#" class="btn_clear"><?php echo strtoupper(Kohana::lang('ui_main.clear_map'));?></a></li>
									</ul>
								</div>
								<div style="clear:both;"></div>
								<?php print form::input('location_find', '', ' title="'.Kohana::lang('ui_main.location_example').'" class="findtext"'); ?>
								<div class="btns" style="float:left;">
									<ul>
										<li><a href="#" class="btn_find"><?php echo strtoupper(Kohana::lang('ui_main.find_location'));?></a></li>
									</ul>
								</div>
								<div id="find_loading" class="incident-find-loading"></div>
								<div style="clear:both;"><?php echo Kohana::lang('ui_main.pinpoint_location');?>.</div>
							</div>
							<?php Event::run('ushahidi_action.report_form_admin_location', $id); ?>
							<div class="row">
								<div class="town">
									<h4><?php echo Kohana::lang('ui_main.reports_location_name');?> <br /><span><?php echo Kohana::lang('ui_main.detailed_location_example');?></span></h4>
									<?php print form::input('location_name', $form['location_name'], ' class="text long"'); ?>
								</div>
							</div>
				
				
							<!-- News Fields -->
							<div class="row link-row">
								<h4><?php echo Kohana::lang('ui_main.reports_news');?></h4>
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
								<h4><?php echo Kohana::lang('ui_main.reports_video');?></h4>
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
								<h4><?php echo Kohana::lang('ui_main.reports_photos');?></h4>
								<?php								
    								if ($incident_media)
                        			{
                        				// Retrieve Media
                        				foreach($incident_media as $photo) 
                        				{
                        					if ($photo->media_type == 1)
                        					{
                        						print "<div class=\"report_thumbs\" id=\"photo_". $photo->id ."\">";

                        						$thumb = $photo->media_thumb;
                        						$photo_link = $photo->media_link;
												$prefix = url::base().Kohana::config('upload.relative_directory');
                        						print "<a class='photothumb' rel='lightbox-group1' href='$prefix/$photo_link'>";
                        						print "<img src=\"$prefix/$thumb\" >";
                        						print "</a>";

                        						print "&nbsp;&nbsp;<a href=\"#\" onClick=\"deletePhoto('".$photo->id."', 'photo_".$photo->id."'); return false;\" >".Kohana::lang('ui_main.delete')."</a>";
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
											print "<a href=\"#\" class=\"rem\"  onClick='removeFormField(\"#".$this_field."_".$i."\"); return false;'>remove</a>";
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
									<h4><?php echo Kohana::lang('ui_main.personal_information');?></span></h4>
									<label>
										<span><?php echo Kohana::lang('ui_main.first_name');?></span>
										<?php print form::input('person_first', $form['person_first'], ' class="text"'); ?>
									</label>
									<label>
										<span><?php echo Kohana::lang('ui_main.last_name');?></span>
										<?php print form::input('person_last', $form['person_last'], ' class="text"'); ?>
									</label>
								</div>
								<div class="row">
									<label>
										<span><?php echo Kohana::lang('ui_main.email_address');?></span>
										<?php print form::input('person_email', $form['person_email'], ' class="text"'); ?>
									</label>
								</div>
							</div>
							<!-- f-col-bottom-1 -->
							<div class="f-col-bottom-1">
								<h4><?php echo Kohana::lang('ui_main.information_evaluation');?></h4>
								<div class="row">
									<div class="f-col-bottom-1-col"><?php echo Kohana::lang('ui_main.approve_this_report');?>?</div>
									<input type="radio" name="incident_active" value="1"
									<?php if ($form['incident_active'] == 1)
									{
										echo " checked=\"checked\" ";
									}?>> <?php echo Kohana::lang('ui_main.yes');?>
									<input type="radio" name="incident_active" value="0"
									<?php if ($form['incident_active'] == 0)
									{
										echo " checked=\"checked\" ";
									}?>> <?php echo Kohana::lang('ui_main.no');?>
								</div>
								<div class="row">
									<div class="f-col-bottom-1-col"><?php echo Kohana::lang('ui_main.verify_this_report');?>?</div>
									<input type="radio" name="incident_verified" value="1"
									<?php if ($form['incident_verified'] == 1)
									{
										echo " checked=\"checked\" ";
									}?>> <?php echo Kohana::lang('ui_main.yes');?>
									<input type="radio" name="incident_verified" value="0"
									<?php if ($form['incident_verified'] == 0)
									{
										echo " checked=\"checked\" ";
									}?>> <?php echo Kohana::lang('ui_main.no');?>									
								</div>
								<div class="row">
									<div class="f-col-bottom-1-col"><?php echo Kohana::lang('ui_main.report_edit_dropdown_1_title');?>:</div>
									<?php print form::dropdown('incident_source', 
									array(""=> Kohana::lang('ui_main.report_edit_dropdown_1_default'), 
									"1"=> Kohana::lang('ui_main.report_edit_dropdown_1_item_1'), 
									"2"=> Kohana::lang('ui_main.report_edit_dropdown_1_item_2'), 
									"3"=> Kohana::lang('ui_main.report_edit_dropdown_1_item_3'), 
									"4"=> Kohana::lang('ui_main.report_edit_dropdown_1_item_4'), 
									"5"=> Kohana::lang('ui_main.report_edit_dropdown_1_item_5'), 
									"6"=> Kohana::lang('ui_main.report_edit_dropdown_1_item_6')
									)
									, $form['incident_source']) ?>									
								</div>
								<div class="row">
									<div class="f-col-bottom-1-col"><?php echo Kohana::lang('ui_main.report_edit_dropdown_2_title');?>:</div>
									<?php print form::dropdown('incident_information', 
									array(""=> Kohana::lang('ui_main.report_edit_dropdown_1_default'), 
									"1"=> Kohana::lang('ui_main.report_edit_dropdown_2_item_1'), 
									"2"=> Kohana::lang('ui_main.report_edit_dropdown_2_item_2'), 
									"3"=> Kohana::lang('ui_main.report_edit_dropdown_2_item_3'), 
									"4"=> Kohana::lang('ui_main.report_edit_dropdown_2_item_4'), 
									"5"=> Kohana::lang('ui_main.report_edit_dropdown_2_item_5'), 
									"6"=> Kohana::lang('ui_main.report_edit_dropdown_2_item_6')
									)
									, $form['incident_information']) ?>									
								</div>								
							</div>
							<div style="clear:both;"></div>
						</div>
						<div class="btns">
							<ul>
								<li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_main.save_report'));?></a></li>
								<li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
								<?php 
								if($id)
								{
									echo "<li><a href=\"#\" class=\"btn_delete btns_red\">".strtoupper(Kohana::lang('ui_main.delete_report'))."</a></li>";
								}
								?>
								<li><a href="<?php echo url::site().'admin/reports/';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
							</ul>
						</div>						
					</div>
				<?php print form::close(); ?>
				<?php
				if($id)
				{
					// Hidden Form to Perform the Delete function
					print form::open(url::site().'admin/reports/', array('id' => 'reportMain', 'name' => 'reportMain'));
					$array=array('action'=>'d','incident_id[]'=>$id);
					print form::hidden($array);
					print form::close();
				}
				?>
			</div>
