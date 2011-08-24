<?php
/**
 * Actions view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Sharing view
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
<script type="text/javascript">
$(document).ready(function() {

<?php
	// Set up some variables so javascript can display the right form fields

	// ----- TRIGGERS & QUALIFIERS ------

	echo 'var advanced_fields = new Array;'."\n";
	foreach($trigger_advanced_options as $tao_action => $tao_data)
	{
		echo 'advanced_fields[\''.$tao_action.'\'] = [';
		$i = 0;
		foreach($tao_data as $tao) {
			if($i != 0) echo ',';
			echo '\''.$tao.'\'';
			$i++;
		}
		echo '];'."\n";
	}
	echo "\n";
	echo 'var advanced_option_areas = new Array(';
	$i = 0;
	foreach($advanced_option_areas as $aoa)
	{
		if($i != 0) echo ',';
		echo '\''.$aoa.'\'';
		$i++;
	}
	echo ');';

	// ----- RESPONSES ------

	echo "\n";
	echo 'var response_advanced_fields = new Array;'."\n";
	foreach($response_advanced_options as $rao_action => $rao_data)
	{
		echo 'response_advanced_fields[\''.$rao_action.'\'] = [';
		$i = 0;
		foreach($rao_data as $rao) {
			if($i != 0) echo ',';
			echo '\''.$rao.'\'';
			$i++;
		}
		echo '];'."\n";
	}

	echo 'var response_advanced_option_areas = new Array(';
	$i = 0;
	foreach($response_advanced_option_areas as $raoa)
	{
		if($i != 0) echo ',';
		echo '\''.$raoa.'\'';
		$i++;
	}
	echo ');';

	// Allowed responses for triggers
	echo "\n";
	echo 'var trigger_allowed_responses = new Array;'."\n";
	foreach($trigger_allowed_responses as $trigger => $allowed_responses)
	{
		echo 'trigger_allowed_responses[\''.$trigger.'\'] = [';
		$i = 0;
		foreach($allowed_responses as $allowed_response) {
			if($i != 0) echo ',';
			echo '\''.$allowed_response.'\'';
			$i++;
		}
		echo '];'."\n";
	}

	echo "\n";
	echo 'var response_options = new Array;'."\n";
	foreach($response_options as $response_key => $response_name)
	{
		echo 'response_options[\''.$response_key.'\'] = \''.$response_name.'\';'."\n";
	}
?>

	// ----- ACTIONS & TRIGGERS

	$('#action_trigger').change(function() {
		// When the trigger is selected, load the advanced fields.

		hide_advanced_options();
		hide_response_advanced_options();
		hide_response();

		for(i=0; i<advanced_option_areas.length; i++) {
			if(jQuery.inArray(advanced_option_areas[i], advanced_fields[$('#action_trigger').val()]) != -1){
				// From here we need to enable the extra form fields
				$('#action_form_'+advanced_option_areas[i]).slideDown();
			}
		}

		// Set response for action
		build_response_select($('#action_trigger').val());
		show_response();

	});

	function hide_advanced_options(){
		$('#action_form_location').slideUp();
		$('#action_form_keyword').slideUp();
		$('#action_form_user').slideUp();
		$('#action_form_category').slideUp();
		$('#action_form_on_specific_count').slideUp();
		$('#action_form_between_times').slideUp();
		$('#action_form_days_of_the_week').slideUp();
		$('#action_form_specific_days').slideUp();
	}
	hide_advanced_options();

	// ----- RESPONSES

	$('#action_response').change(function() {
		// When the trigger is selected, load the advanced fields.

		hide_response_advanced_options();

		for(i=0; i<response_advanced_option_areas.length; i++) {
			if(jQuery.inArray(response_advanced_option_areas[i], response_advanced_fields[$('#action_response').val()]) != -1){
				// From here we need to enable the extra form fields
				$('#action_form_'+response_advanced_option_areas[i]).slideDown();
			}
		}
	});

	function hide_response_advanced_options(){
		$('#action_form_email_subject').slideUp();
		$('#action_form_email_body').slideUp();
		$('#action_form_email_send_address').slideUp();
		$('#action_form_add_category').slideUp();
		$('#action_form_verify').slideUp();
		$('#action_form_badge').slideUp();
	}
	hide_response_advanced_options();

	function hide_response(){
		$('#action_form_response').slideUp();
	}
	hide_response();

	function show_response(){
		$('#action_form_response').slideDown();
		hide_trigger_select_messages();
	}

	function build_response_select(trigger){
		var selected = '';
		$('#action_response').html('');
		$.each(trigger_allowed_responses[trigger], function(k, response_key) {
			
			selected = '';
			if(response_key == 'log_it') {
				selected = 'selected';
			}
			
			$('#action_response').append('<option value="'+response_key+'" '+selected+'>'+response_options[response_key]+'</option>');
		});
	}

	function hide_trigger_select_messages(){
		$('#trigger_first_response').hide();
		$('#trigger_first_qualifiers').hide();
	}

	function show_trigger_select_messages(){
		$('#trigger_first_response').show();
		$('#trigger_first_qualifiers').show();
	}

	var selected_specific_days = new Array();
	$('#action_specific_days_calendar')
		.datePicker(
			{
				startDate:'01/01/2000', // date obviously in the past
				inline:true,
				selectMultiple:true
			}
		)
		.bind(
			'dateSelected',
			function(e, selectedDate, $td, state)
			{
				//console.log('You ' + (state ? '' : 'un') + 'selected ' + selectedDate);
				if(state){
					// selected
					selected_specific_days.push(String(selectedDate));
				}else{
					// unselected, remove from array
					selected_specific_days = jQuery.grep(selected_specific_days, function (a) { return a != String(selectedDate); });
				}
				$("#action_specific_days").attr("value", selected_specific_days.join(','));
			}
		);
	
	
	
	
});
</script>

			<div class="bg">
				<h2>
					<?php admin::manage_subtabs("actions"); ?>
				</h2>
				<div style="width:100%;background-color:#FFD8D9;padding:4px 0px;"><img src="<?php echo url::file_loc('img'); ?>media/img/experimental.png" alt="<?php echo Kohana::lang('ui_admin.experimental');?>" style="position:relative;float:left;padding-left:250px;padding-right:5px;"/>This is an experimental feature. The Ushahidi and Crowdmap Teams cannot be <br/>responsible for any mishaps, bugs or quirks that show up when using Actions.</div>
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
							echo (!$error_description) ? '' : "<li>" . $error_description . "</li>";
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
						<h3><?php echo $form_action; ?>!</h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php echo form::open('/admin/manage/actions/changestate',array('id' => 'actionListing', 'name' => 'actionListing')); ?>
						<input type="hidden" name="action_id" id="action_id" value="">
						<input type="hidden" name="action_switch_to" id="action_switch_to" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1" style="width:125px;"><?php echo Kohana::lang('ui_admin.triggers'); ?></th>
										<th class="col-2" style="width:275px;"><?php echo Kohana::lang('ui_admin.qualifiers');?></th>
										<th class="col-3" style="width:125px;"><?php echo Kohana::lang('ui_admin.response');?></th>
										<th class="col-4" style="width:275px;text-align:left;"><?php echo Kohana::lang('ui_admin.actions');?></th>
										<th class="col-5" style="width:100px;"><?php echo Kohana::lang('ui_admin.state');?></th>
									</tr>
								</thead>
								<tfoot>
									<tr class="foot">
										<td colspan="5">
											<!--TODO: Pagination-->
										</td>
									</tr>
								</tfoot>
								<tbody>
									<?php
									if ($total_items == 0)
									{
									?>
										<tr>
											<td colspan="5" class="col">
												<h3><?php echo Kohana::lang('ui_main.no_results'); ?></h3>
											</td>
										</tr>
									<?php
									}

									foreach ($actions as $action)
									{
										$action_id = $action->action_id;
										$trigger = $action->action;
										$qualifiers = unserialize($action->qualifiers);
										$response = $action->response;
										$response_vars = unserialize($action->response_vars);
										$active = $action->active;

										$qualifier_string = '';
										foreach($qualifiers as $qkey => $qval){

											// Show username
											if($qkey == 'user') $qval = $user_options[$qval];

											// Don't show geometry variable because we show it with location
											if($qkey == 'geometry') continue;

											// between_times doesn't actually show anything
											if($qkey == 'between_times') continue;
											
											// Convert seconds to something easier to digest
											if($qkey == 'between_times_1' OR $qkey == 'between_times_2')
											{
												$time = $qval;
												if($time == 0)
												{
													$hours = '00';
													$minutes = '00';
												}else{
													$hours = floor($qval / 3600);
													if($hours < 10) $hours = '0'.$hours;
													$minutes = ($qval % 3600) * 60;
													if($minutes < 10) $minutes = '0'.$minutes;
												}
												$qval = $hours.':'.$minutes.' <small>('.$site_timezone.')</small>';
											}
											
											// Make sure we show the right language for days of the week
											if($qkey == 'days_of_the_week')
											{
												foreach($qval as $key => $day){
													$qval[$key] = $days[$day];
												}
											}
											
											// If there's nothing there, don't show it
											if($qval === '' OR $qval === 0 OR $qval === '0') continue;

											// If it's really long and not an exempted key, chop off the end
											if( is_string($qval) AND strlen($qval) > 150 
												AND $qkey != 'location' AND $qkey != 'days_of_the_week')
											{
												$qval = substr($qval,0,150).'&#8230;';
											}

											// If it's a specific location, show the polygon on a static map
											if($qkey == 'location' && $qval == 'specific') {
												// TODO: Find some more intuitive way to illustrate where this is.
												//$qval = print_r($qualifiers['geometry'],true);

												$geometry = str_ireplace('{"geometry":"POLYGON((','',$qualifiers['geometry']);
												$geometry = str_ireplace('))"}','',$geometry);
												$polygon = explode(',',$geometry[0]);

												$qval = 'Geofenced<br/>';
												$qval .= '<img src ="http://maps.googleapis.com/maps/api/staticmap?size=275x200&path=color:0xff0000ff|weight:2|fillcolor:0xFFFF0033';
												foreach($polygon as $pk => $pv)
												{
													$point = str_ireplace(' ',',',$pv);
													$point = explode(',',$point);
													$qval .= '|'.$point[1].','.$point[0];
												}

												$qval .= '&sensor=false" />';
											}else{

												// If it's not a location, break the array into a string
												if(is_array($qval))
												{
													$qval = implode(', ',$qval);
												}

											}
											
											$qualifier_string .= '<strong>'.$qkey.'</strong>: '.$qval.'<br/>';
										}

										$response_string ='';
										foreach($response_vars as $rkey => $rval){

											$display_val = $rval;
											if(is_array($rval))
											{
												$display_val = implode(',',$rval);
											}elseif($rkey == 'email_send_address' AND $rval = '1'){
												$display_val = '<em>'.Kohana::lang('ui_admin.triggering_user').'</em>';
											}

											$response_string .= '<strong>'.$rkey.'</strong>: '.$display_val.'<br/>';
										}

										?>
										<tr>
											<td class="col-1" style="width:125px;font-weight:bold;">
												<?php echo $trigger_options[$trigger]; ?>
											</td>
											<td class="col-2" style="width:275px;">
												<?php echo $qualifier_string; ?>
											</td>
											<td class="col-3" style="width:125px;">
												<?php echo $response_options[$response]; ?>
											</td>
											<td class="col-4" style="width:275px;border-right:0px;">
												<?php echo $response_string; ?>
											</td>
											<td class="col" style="width:100px;border-left:0px;">

												<?php if($active) {?>
													<?php echo Kohana::lang('ui_admin.currently_active'); ?><br/><a href="javascript:actionsAction('0','DEACTIVATE',<?php echo rawurlencode($action_id);?>)" class="status_yes"><?php echo Kohana::lang('ui_main.deactivate'); ?></a>
												<?php } else {?>
													<?php echo Kohana::lang('ui_admin.currently_inactive'); ?><br/><a href="javascript:actionsAction('1','ACTIVATE',<?php echo rawurlencode($action_id);?>)" class="status_no"><?php echo Kohana::lang('ui_main.activate'); ?></a>
												<?php } ?>

											</td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>
					<?php echo form::close(); ?>
				</div>

				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php echo form::open(NULL,array('id' => 'actionsMain', 'name' => 'actionsMain')); ?>

						<div id="divMap" style="width:900px;height:350px;border:0px;">
							<div id="geometryLabelerHolder" class="olControlNoSelect">
								<div id="geometryLabeler">
									<span id="geometry[]"><?php echo form::input('geometry[]'); ?></span>
								</div>
								<div id="geometryLabelerClose"></div>
							</div>
							<a href="#" class="btn_clear" style="float:right;margin-top:350px;padding:25px;"><?php echo strtoupper(Kohana::lang('ui_main.clear_map'));?></a>
						</div>

						<script type="text/javascript">
						$(document).ready(function() {
							// Close map to start
							//hide_map();

							$('.action_location').click(function(){
								// Check value.
								if($(this).val() == 'specific'){
									// Open map
									show_map();
								}else{
									// Close map (since it's 'anywhere')
									hide_map();
								}
							});

						});
						</script>

						<input type="hidden" id="action_id" name="action_id" value="" />
						<input type="hidden" name="form_action" id="form_action" value="a"/>
						
						<div style="float:right;padding:25px 25px 0 0;text-align:right;">
							<?php echo Kohana::lang('ui_admin.server_time').' '.date("m/d/Y H:i:s",time()).' ('.$site_timezone.')'; ?><br/>
							<a href="<?php echo url::base(); ?>admin/settings/site"><small><?php echo Kohana::lang('ui_admin.modify_timezone'); ?></small></a>
						</div>

						<h3><?php echo Kohana::lang('ui_admin.trigger'); ?></h3>

						<div id="actions_qualifier_section">

							<div class="tab_form_item" id="action_form_trigger">
								<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("tooltips.actions.trigger"); ?>"><?php echo Kohana::lang('ui_admin.trigger'); ?>:</a></h4>
								<?php echo form::dropdown('action_trigger', $trigger_options, 'standard'); ?>
							</div>

						</div>

						<div style="clear:both"></div>

						<div id="actions_qualifier_section" style="padding-top:10px;">

							<h3><?php echo Kohana::lang('ui_admin.qualifiers'); ?></h3>

							<div class="tab_form_item" id="trigger_first_qualifiers"><?php echo Kohana::lang('ui_admin.select_trigger_before_qualifiers'); ?></div>

							<div class="tab_form_item" id="action_form_user" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.user")); ?>"><?php echo Kohana::lang('ui_admin.user'); ?>:</a></h4>
								<?php echo form::dropdown('action_user', $user_options, 'standard'); ?>
							</div>

							<div class="tab_form_item" id="action_form_location" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.location")); ?>"><?php echo Kohana::lang('ui_main.location'); ?>:</a></h4>
								<?php echo form::radio('action_location', 'anywhere', TRUE, ' class="action_location"').' '.Kohana::lang('ui_admin.anywhere'); ?><br/>
								<?php echo form::radio('action_location', 'specific', FALSE, ' class="action_location"').' '.Kohana::lang('ui_admin.specific_area'); ?>
							</div>

							<div class="tab_form_item" id="action_form_keyword" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.keywords")); ?>"><?php echo Kohana::lang('ui_admin.keywords'); ?>:</a></h4>
								<?php echo form::input('action_keyword',''); ?>
							</div>

							<div class="tab_form_item" id="action_form_category" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.category")); ?>"><?php echo Kohana::lang('ui_main.category'); ?>:</a></h4>
								<?php
									// categories, selected_categories, form field name, number of columns
									echo category::tree($categories, array(), 'action_category', 2);
								?>
							</div>

							<div class="tab_form_item" id="action_form_on_specific_count" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.on_specific_count")); ?>"><?php echo Kohana::lang('ui_admin.on_specific_count');?>:</a></h4>
								<?php echo Kohana::lang('ui_admin.count').' '.form::input('action_on_specific_count','',' style="width:25px;"'); ?><br/>
								<?php echo form::radio('action_on_specific_count_collective', '0', TRUE).' '.Kohana::lang('ui_admin.triggering_user'); ?><br/>
								<?php echo form::radio('action_on_specific_count_collective', '1', FALSE).' '.Kohana::lang('ui_admin.entire_collective'); ?>

							</div>
							
							<div class="tab_form_item" id="action_form_days_of_the_week" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.days_of_the_week")); ?>"><?php echo Kohana::lang('ui_admin.days_of_the_week');?>:</a></h4>
								<?php
									echo form::dropdown(array('name' => 'action_days_of_the_week[]', 'multiple' => 'multiple', 'size' => 7), $days, array('standard', 'basic'));
								?>
							</div>
							
							<div class="tab_form_item" id="action_form_between_times" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.between_times")); ?>"><?php echo Kohana::lang('ui_admin.between_times');?>:</a></h4>
								<?php
									$hours = range(0,24);
									foreach($hours as $hour_key => $hour){
										if($hour < 10) $hours[$hour_key] = '0'.$hour;
									}
									
									$minutes = range(0,59);
									foreach($minutes as $minute_key => $minute){
										if($minute < 10) $minutes[$minute_key] = '0'.$minute;
									}
								?>
								<?php echo form::dropdown('action_between_times_hour_1', $hours, 'standard'); ?> : <?php echo form::dropdown('action_between_times_minute_1', $minutes, 'standard'); ?>
								<center><?php echo Kohana::lang('ui_main.and');?></center>
								<?php echo form::dropdown('action_between_times_hour_2', $hours, 'standard'); ?> : <?php echo form::dropdown('action_between_times_minute_2', $minutes, 'standard'); ?>
							</div>
							
							<div class="tab_form_item" id="action_form_specific_days" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.specific_days")); ?>"><?php echo Kohana::lang('ui_admin.specific_days');?>:</a></h4>
								<div id="action_specific_days_calendar" class="action_specific_days_calendar" name="action_specific_days_calendar"></div>
								<input type="hidden" name="action_specific_days" id="action_specific_days" value=""  />
							</div>
							
						</div>

						<div style="clear:both"></div>

						<div id="actions_response_section" style="padding-top:10px;">

							<h3><?php echo Kohana::lang('ui_admin.response'); ?></h3>

							<div class="tab_form_item" id="trigger_first_response"><?php echo Kohana::lang('ui_admin.select_trigger_before_response'); ?></div>

							<div class="tab_form_item" id="action_form_response" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.response")); ?>"><?php echo Kohana::lang('ui_admin.response'); ?>:</a></h4>
								<?php
									// This dropdown is special since it will write all options and then be
									//   changed as soon as an action trigger is selected. It does this
									//   so the advanced options for responses will show up properly.
									echo form::dropdown('action_response', $response_options, 'standard', 'log_it');
								?>
							</div>

							<div class="tab_form_item" id="action_form_email_send_address" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.send_to")); ?>"><?php echo Kohana::lang('ui_admin.send_to');?>:</a></h4>
								<?php echo form::radio('action_email_send_address', '0', TRUE).' '.Kohana::lang('ui_admin.triggering_user'); ?><br/>
								<?php echo form::radio('action_email_send_address', '1', FALSE); ?>
								<?php echo form::input('action_email_send_address_specific',''); ?>
							</div>

							<div class="tab_form_item" id="action_form_email_subject" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.email_subject")); ?>"><?php echo Kohana::lang('ui_admin.subject');?>:</a></h4>
								<?php echo form::input('action_email_subject',''); ?>
							</div>

							<div class="tab_form_item" id="action_form_email_body" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.email_body")); ?>"><?php echo Kohana::lang('ui_admin.body');?>:</a></h4>
								<?php echo form::textarea('action_email_body',''); ?>
							</div>

							<div class="tab_form_item" id="action_form_add_category" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.add_to_category")); ?>"><?php echo Kohana::lang('ui_admin.add_to_category'); ?>:</a></h4>
								<?php
									// categories, selected_categories, form field name, number of columns
									echo category::tree($categories, array(), 'action_add_category', 2);
								?>
							</div>

							<div class="tab_form_item" id="action_form_verify" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.verify")); ?>"><?php echo Kohana::lang('ui_admin.mark_as');?>:</a></h4>
								<?php echo form::radio('action_verify', '0', TRUE).' '.Kohana::lang('ui_main.unverified'); ?><br/>
								<?php echo form::radio('action_verify', '1', FALSE).' '.Kohana::lang('ui_main.verified'); ?>
							</div>
							
							<div class="tab_form_item" id="action_form_badge" style="margin-right:75px;">
								<h4><a href="#" class="tooltip" title="<?php echo htmlspecialchars(Kohana::lang("tooltips.actions.assign_badge")); ?>"><?php echo Kohana::lang('ui_admin.assign_badge'); ?>:</a></h4>
								<?php
									echo form::dropdown('action_badge', $badges, 'standard');
								?>
							</div>

						</div>

						<div style="clear:both"></div>

						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php echo form::close(); ?>
					</div>
				</div>
			</div>
