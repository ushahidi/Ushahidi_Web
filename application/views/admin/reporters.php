		<div class="bg">
				<h2>
					<a href="<?php echo url::site() . 'admin/manage' ?>"><?php echo Kohana::lang('ui_main.categories');?></a>
					<a href="<?php echo url::site() . 'admin/manage/forms' ?>"><?php echo Kohana::lang('ui_main.forms');?></a>
					<a href="<?php echo url::site() . 'admin/manage/organizations' ?>"><?php echo Kohana::lang('ui_main.organizations');?></a>
					<a href="<?php echo url::site() . 'admin/manage/pages' ?>"><?php echo Kohana::lang('ui_main.pages');?></a>
					<a href="<?php echo url::site() . 'admin/manage/feeds' ?>"><?php echo Kohana::lang('ui_main.news_feeds');?></a>
					<a href="<?php echo url::site() . 'admin/manage/layers' ?>"><?php echo Kohana::lang('ui_main.layers');?></a>
					<a href="<?php echo url::site() . 'admin/manage/reporters' ?>" class="active"><?php echo Kohana::lang('ui_main.reporters');?></a>
					<span>(<a href="#add"><?php echo Kohana::lang('ui_main.add_new');?></a>)</span>
					<a href="<?php echo url::site() . 'admin/manage/scheduler' ?>"><?php echo Kohana::lang('ui_main.scheduler');?></a>
				</h2>
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
					<?php print form::open(NULL,array('id' => 'rptrListing',
					 	'name' => 'orgListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="reporter_id" id="rptr_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
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
							    		$service_userid = $reporter->service_userid;
							    		$service_account = $reporter->service_account;
							    		$reporter_first = $reporter->reporter_first;
							    		$reporter_last = $reporter->reporter_last;
							    		$reporter_email = $reporter->reporter_email;
							    		$reporter_phone = $reporter->reporter_phone;
							    		$reporter_ip = $reporter->reporter_ip;
							    		$reporter_date = $reporter->reporter_date;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $reporter_first . ' ' . $reporter_last; ?></h4>
													<p><?php echo $service_userid . ': ' . $service_account; ?>...</p>
												</div>
											</td>
											<td class="col-3">
												<div>
													<h4><?php echo $service_name; ?></h4>
												</div>
											</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields(
	'<?php echo(rawurlencode($reporter_id)); ?>',
	'<?php echo(rawurlencode($service_id)); ?>',
	'<?php echo(rawurlencode($level_id)); ?>',
	'<?php echo(rawurlencode($service_userid)); ?>',
	'<?php echo(rawurlencode($service_account)); ?>',
	'<?php echo(rawurlencode($reporter_first)); ?>',
	'<?php echo(rawurlencode($reporter_last)); ?>',
	'<?php echo(rawurlencode($reporter_email)); ?>',
	'<?php echo(rawurlencode($reporter_phone)); ?>',
	'<?php echo(rawurlencode($reporter_ip)); ?>',
	'<?php echo(rawurlencode($reporter_date)); ?>')"><?php echo Kohana::lang('ui_main.edit');?></a></li>
													<li><a href="javascript:orgAction('d','DELETE','<?php echo(rawurlencode($reporter_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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
				
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'rptrMain',
						 	'name' => 'rptrMain')); ?>
						<input type="hidden" id="reporter_id" 
							name="reporter_id" value="<?php echo $form['reporter_id']; ?>" />
						<input type="hidden" name="action" 
							id="action" value="a"/>							
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.service');?>:</strong><br />
							<?php print form::dropdown('service_id', $service_array, ''); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.reporter_level');?>:</strong><br />
							<?php print form::dropdown('level_id', $level_array, ''); ?>
						</div>
						<!--div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.service_user_id');?>:</strong><br />
							<?php //print form::input('service_userid', $form['service_userid'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.service_username');?>:</strong><br />
							<?php //print form::input('service_account', $form['service_account'], ' class="text long"'); ?>
						</div-->
						<!--div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.reporter_firstname');?>:</strong><br />
							<?php //print form::input('reporter_first', $form['reporter_first'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.reporter_last_name');?>:</strong><br />
							<?php //print form::input('reporter_last', $form['reporter_last'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.reporter_email');?>:</strong><br />
							<?php //print form::input('reporter_email', $form['reporter_email'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.reporter_phone');?>:</strong><br />
							<?php print form::input('reporter_phone', $form['reporter_phone'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.reporter_ip_address');?>:</strong><br />
							<?php //print form::input('reporter_ip', $form['reporter_ip'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.reporter_date');?>:</strong><br />
							<?php //print form::input('reporter_date', $form['reporter_date'], ' class="text long"'); ?>
						</div-->
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>
				
			</div>
			<script type="text/javascript">
			// Levels JS
			function fillFields(id, service_id, level_id, service_userid, service_account, 
								reporter_first, reporter_last, 
			                    reporter_email, reporter_phone, reporter_ip, 
			                    reporter_date)
			{
				$("#reporter_id").attr("value", unescape(id));
				$("#service_id").attr("value", unescape(service_id));
				$("#level_id").attr("value", unescape(level_id));
	    		$("#service_userid").attr("value", unescape(service_userid));
	    		$("#service_account").attr("value", unescape(service_account));
	    		$("#reporter_first").attr("value", unescape(reporter_first));
	    		$("#reporter_last").attr("value", unescape(reporter_last));
	    		$("#reporter_email").attr("value", unescape(reporter_email));
	    		$("#reporter_phone").attr("value", unescape(reporter_phone));
	    		$("#reporter_ip").attr("value", unescape(reporter_ip));
	    		$("#reporter_date").attr("value", unescape(reporter_date));
				
			}
			</script>
