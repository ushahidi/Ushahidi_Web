		<div class="bg">
				<h2>
					<a href="<?php echo url::base() . 'admin/manage' ?>">Categories</a>
					<a href="<?php echo url::base() . 'admin/manage/forms' ?>">Forms</a>
					<a href="<?php echo url::base() . 'admin/manage/organizations' ?>">Organizations</a>
					<a href="<?php echo url::base() . 'admin/manage/pages' ?>">Pages</a>
					<a href="<?php echo url::base() . 'admin/manage/feeds' ?>">News Feeds</a>
					<a href="<?php echo url::base() . 'admin/manage/layers' ?>">Layers</a>
					<a href="<?php echo url::base() . 'admin/manage/reporters' ?>" class="active">Reporters</a>
					<span>(<a href="#add">Add New</a>)</span>
				</h2>
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
						<h3>The Reporter Has Been <?php echo $form_action; ?></h3>
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
										<th class="col-2">Reporter</th>
										<th class="col-3">Service</th>
										<th class="col-4">Actions</th>
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
												<h3>No Results To Display!</h3>
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
	'<?php echo(rawurlencode($reporter_date)); ?>')">Edit</a></li>
													<li><a href="javascript:orgAction('d','DELETE','<?php echo(rawurlencode($reporter_id)); ?>')" class="del">Delete</a></li>
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
						<li><a href="#" class="active">Add/Edit</a></li>
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
							<strong>Service:</strong><br />
							<?php print form::dropdown('service_id', $service_array, ''); ?>
						</div>
						<div class="tab_form_item">
							<strong>Reporter Level:</strong><br />
							<?php print form::dropdown('level_id', $level_array, ''); ?>
						</div>
						<!--div class="tab_form_item2">
							<strong>Service User ID:</strong><br />
							<?php //print form::input('service_userid', $form['service_userid'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Service Username:</strong><br />
							<?php //print form::input('service_account', $form['service_account'], ' class="text long"'); ?>
						</div-->
						<!--div class="tab_form_item2">
							<strong>Reporter Firstname:</strong><br />
							<?php //print form::input('reporter_first', $form['reporter_first'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Reporter Lastname:</strong><br />
							<?php //print form::input('reporter_last', $form['reporter_last'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Reporter Email:</strong><br />
							<?php //print form::input('reporter_email', $form['reporter_email'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Phone:</strong><br />
							<?php print form::input('reporter_phone', $form['reporter_phone'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Reporter IP Address:</strong><br />
							<?php //print form::input('reporter_ip', $form['reporter_ip'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Reporter Date:</strong><br />
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
