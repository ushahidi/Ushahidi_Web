		<div class="bg">
				<h2>
					<?php admin::messages_subtabs($service_id); ?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::site()."admin/messages/index/".$service_id; ?>?type=1"><?php echo Kohana::lang('ui_main.inbox');?></a></li>
						<?php
						if ($service_id == 1)
						{
							?><li><a href="<?php echo url::site()."admin/messages/index/".$service_id; ?>?type=2"><?php echo Kohana::lang('ui_main.outbox');?></a></li><?php
						}
						?>
						<li><a href="<?php echo url::site()."admin/messages/reporters/?s=".$service_id; ?>" class="active">Reporters (<?php echo $total_items; ?>)</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onClick="reportersAction('d', 'DELETE', '', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
							<?php foreach($levels as $level) { ?>
								<li><a href="#" onClick="reportersAction('l', 'Mark As <?php echo $level->level_title?>', '', <?php echo $level->id?>)" class="reporters_tab_<?php echo $level->id;?>"><?php echo $level->level_title?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>				
				
				
				<!-- tabs -->
				<div class="tabs">
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('method' => 'get',
						 	'id' => 'searchReporters')); ?>
						<div class="tab_form_item">
							<?php print form::input('k', $keyword, ' class="text long"'); ?>
						</div>
						<div class="tab_form_item">
							<?php print form::dropdown('s', $search_type_array, $search_type); ?>
						</div>				
						<div class="tab_form_item">
							<a href="#" onclick="submitSearch()"><strong><?php echo Kohana::lang('ui_admin.search');?></strong></a>
						</div>
						<?php print form::close(); ?>			
					</div>
				</div>
				
				<div class="tabs" id="addedit" style="display:none">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#add" class="active"><?php echo Kohana::lang('ui_main.edit');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab reporters">
						<?php print form::open(NULL,array('id' => 'reporterEdit',
						 	'name' => 'reporterEdit')); ?>
						<input type="hidden" name="action" 
							id="action" value="a"/>
						<input type="hidden" id="reporter_id" 
							name="reporter_id[]" value="<?php echo $form['reporter_id']; ?>" />
						<input type="hidden" id="service_account" name="service_account" value="">
						<input type="hidden" id="service_name" name="service_name" value="">
						<input type="hidden" id="location_id" name="location_id" value="">
						<div style="clear:both;"></div>							
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.reporter');?>:</strong><br />
							<h3 id="reporter_account"><?php echo $form['service_account']; ?></h3>
						</div>
						<div style="clear:both;"></div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.service');?>:</strong><br />
							<h3 id="reporter_service"><?php echo $form['service_name']; ?></h3>
						</div>
						<div style="clear:both;"></div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.reporter_level');?>:</strong><br />
							<?php print form::dropdown('level_id', $level_array, $form['level_id']); ?>
						</div>
						<div style="clear:both;"></div>
						<div id="reporter_location">
							<h3>Give this Reporter A Location <span>(Giving the reporter a location will allow their reports to be mapped immediately if they are trusted)</span></h3>
							<div class="tab_form_item">
								<strong><?php echo Kohana::lang('ui_main.location');?>:</strong><br />
								<?php print form::input('location_name', $form['location_name'], ' class="text"'); ?>
							</div>
							<div class="tab_form_item">
								<strong><?php echo Kohana::lang('ui_main.latitude');?>:</strong><br />
								<?php print form::input('latitude', $form['latitude'], ' class="text"'); ?>
							</div>
							<div class="tab_form_item">
								<strong><?php echo Kohana::lang('ui_main.longitude');?>:</strong><br />
								<?php print form::input('longitude', $form['longitude'], ' class="text"'); ?>
							</div>
							<div style="clear:both;"></div>
							<div class="tab_form_item">
								<strong><?php echo Kohana::lang('ui_main.location');?>:</strong><br />
								<div id="ReporterMap"></div>
							</div>
							<div style="clear:both;"></div>
						</div>
						<div class="tab_form_item">
							<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_main.save');?>" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>				
							
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
						<h3><?php echo Kohana::lang('ui_main.reporter_has_been');?> <?php echo $form_action; ?></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'reporterMain',
					 	'name' => 'reporterMain')); ?>
						<input type="hidden" name="action" id="reporter_action" value="">
						<input type="hidden" name="reporter_id[]" id="reporter_single" value="">
						<input type="hidden" name="level_id" id="level_id_main" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1"><input id="checkall" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'reporter_id[]' )" /></th>
										<th class="col-2"><?php echo Kohana::lang('ui_main.reporter');?></th>
										<th class="col-3"><?php echo Kohana::lang('ui_main.service');?></th>
										<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
									</tr>
								</thead>
								<tfoot>
									<tr class="foot">
										<td colspan="4">
											<?php echo $pagination; ?>
										</td>
									</tr>
								</tfoot>
								<tbody>
									<?php
									if ($total_items == 0)
									{
									?>
										<tr>
											<td colspan="4" class="col">
												<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
											</td>
										</tr>
									<?php	
									} 
									foreach ($reporters as $reporter)
									{
										$reporter_id = $reporter->id;   
								        $service_id = $reporter->service_id;
										$level_id = $reporter->level_id;
								        $service = new Service_Model($service_id);
								        $service_name = $service->service_name;
									$service_account = $reporter->service_account;
											if ($keyword)
											{
												$service_account = str_ireplace($keyword,
													"<span class=\"highlight\">$keyword</span>", $service_account);
											}
											
							    		// Get Location Information
										$location_id = "";
										$location_name = "";
										$latitude = "";
										$longitude = "";
										$location = $reporter->location;
										if ($location->loaded)
										{
											$location_id = $location->id;
											$location_name = $location->location_name;
											$latitude = $location->latitude;
											$longitude = $location->longitude;
										}
										
										// Get Message Information
										$message_count = $reporter->message->count();
										
										// Get Reporter Level
										$reporter_level = $level_array[$level_id];
										?>
										<tr>
											<td class="col-1"><input name="reporter_id[]" id="reporter" value="<?php echo $reporter_id; ?>" type="checkbox" class="check-box"/></td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $service_account; ?> <span>[<a href="<?php echo url::site()."admin/messages/index/".$service_id."?rid=".$reporter_id;?>">View Messages</a>]</span></h4>
												</div>
												<ul class="info">
													<li class="none-separator"><?php echo Kohana::lang('ui_main.messages');?>: <strong><?php echo $message_count; ?></strong></li>
													<li class="none-separator">Reporter Level: <strong class="reporters_<?php echo $level_id?>"><?php echo $reporter_level; ?></strong></li>
												</ul>
											</td>
											<td class="col-3">
												<div>
													<?php echo $service_name; ?>
												</div>
											</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields(
	'<?php echo(rawurlencode($reporter_id)); ?>',
	'<?php echo(rawurlencode($level_id)); ?>',
	'<?php echo(rawurlencode($service_name)); ?>',
	'<?php echo(rawurlencode($reporter->service_account)); ?>',
	'<?php echo(rawurlencode($location_id)); ?>',
	'<?php echo(rawurlencode($location_name)); ?>',	
	'<?php echo(rawurlencode($latitude)); ?>',
	'<?php echo(rawurlencode($longitude)); ?>')"><?php echo Kohana::lang('ui_main.edit');?></a></li>
													<li><a href="javascript:reportersAction('d','DELETE','<?php echo(rawurlencode($reporter_id)); ?>', '')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
												</ul>
											</td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>
					<?php print form::close(); ?>
				</div>
				
			</div>
