			<div class="bg">
				<h2><a href="<?php echo url::base() . 'admin/manage' ?>">Categories</a><a href="<?php echo url::base() . 'admin/manage/organizations' ?>" class="active">Organizations</a><span>(<a href="#add">Add New</a>)</span><a href="<?php echo url::base() . 'admin/manage/feeds' ?>">News Feeds</a></h2>
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
						<h3>Your Organization Has Been Saved!</h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2">Organization</th>
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
									foreach ($organizations as $organization)
									{
										$organization_id = $organization->id;
										$organization_name =
										 $organization->organization_name;
										$organization_description = substr($organization->organization_description, 0, 150);
										$organization_website = 
											$organization->organization_website;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $organization_name; ?></h4>
													<p><?php echo $organization_description; ?>...</p>
												</div>
											</td>
											
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields(
	'<?php echo(rawurlencode($organization_id)); ?>',
	'<?php echo(rawurlencode($organization_name)); ?>',
	'<?php echo(rawurlencode($organization_website)); ?>',
	'<?php echo(rawurlencode($organization_description)); ?>')">Edit</a></li>
													
<li><a href="#" onclick="userAction('d',
	'<?php echo(rawurlencode($organization_id)); ?>',
	'<?php echo(rawurlencode($organization_name)); ?>',
	'<?php echo(rawurlencode($organization_website)); ?>',
	'<?php echo(rawurlencode($organization_description)); ?>',
	'DELETE');" class="del">Delete</a></li>
												</ul>
											</td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>
				</div>
				
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active">Add/Edit</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'orgMain',
						 	'name' => 'orgMain')); ?>
						<input type="hidden" id="organization_id" 
							name="organization_id" value="" />
						<input type="hidden" name="action" 
							id="action" value=""/>							
						<div class="tab_form_item2">
							<strong>Organization Name:</strong><br />
							<?php print form::input('organization_name', '', ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Organization Website:</strong><br />
							<?php print form::input('organization_website', '', ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Organization Description:</strong><br />
							<?php print form::textarea('organization_description', '', ' rows="12" cols="60" '); ?>
						</div>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>
				
			</div>