			<div class="bg">
				<h2>
					<a href="<?php echo url::base() . 'admin/manage' ?>">Categories</a>
					<a href="<?php echo url::base() . 'admin/manage/forms' ?>">Forms</a>
					<a href="<?php echo url::base() . 'admin/manage/organizations' ?>">Organizations</a>
					<a href="<?php echo url::base() . 'admin/manage/feeds' ?>">News Feeds</a>
					<a href="<?php echo url::base() . 'admin/manage/levels' ?>" class="active">Reporter Levels</a>
					<span>(<a href="#add">Add New</a>)</span>
					<a href="<?php echo url::base() . 'admin/manage/reporters' ?>">Reporters</a>
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
						<h3>The Level Has Been <?php echo $form_action; ?>!</h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'levListing',
					 	'name' => 'levListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="level_id" id="level_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2">Level</th>
										<th class="col-3">Weight</th>
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
									foreach ($levels as $level)
									{
										$level_id = $level->id;
										$level_title = $level->level_title;
										$level_description = substr($level->level_description, 0, 150);
										$level_weight = $level->level_weight;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $level_title; ?></h4>
													<p><?php echo $level_description; ?>...</p>
												</div>
											</td>
											<td class="col-3"> <?php echo $level_weight; ?></td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($level_id)); ?>','<?php echo(rawurlencode($level_title)); ?>','<?php echo(rawurlencode($level_description)); ?>','<?php echo(rawurlencode($level_weight)); ?>')">Edit</a></li>
<li><a href="javascript:catAction('d','DELETE','<?php echo(rawurlencode($level_id)); ?>')" class="del">Delete</a></li>
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
				
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active">Add/Edit</a></li>
						<li><a href="#">Add Language</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'catMain',
						 	'name' => 'catMain')); ?>
						<input type="hidden" id="level_id" 
							name="level_id" value="" />
						<input type="hidden" name="action" 
							id="action" value="a"/>
						<div class="tab_form_item">
							<strong>level Name:</strong><br />
							<?php print form::input('level_title', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong>Description:</strong><br />
							<?php print form::input('level_description', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong>Weight:</strong><br />
							<?php print form::input('level_weight', '', ' class="text"'); ?>
						</div>
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
			function fillFields(id, level_title, level_description, level_weight)
			{
				$("#level_id").attr("value", unescape(id));
				$("#level_title").attr("value", unescape(level_title));
				$("#level_description").attr("value", unescape(level_description));
				$("#level_weight").attr("value", unescape(level_weight));
			}
			</script>
