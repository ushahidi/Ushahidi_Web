			<div class="bg">
				<h2><a href="<?php echo url::base() . 'admin/manage' ?>">Categories</a><a href="<?php echo url::base() . 'admin/manage/organizations' ?>">Organizations</a><a href="<?php echo url::base() . 'admin/manage/feeds' ?>" class="active">News Feeds</a><span>(<a href="#add">Add New</a>)</span></h2>
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
						<h3>Your Category Has Been Saved!</h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(); ?>
						<input type="hidden" name="action" id="action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2">Feed</th>
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
									foreach ($categories as $category)
									{
										$category_id = $category->id;
										$category_title = $category->category_title;
										$category_description = substr($category->category_description, 0, 150);
										$category_color = $category->category_color;
										$category_visible = $category->category_visible;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $category_title; ?></h4>
													<p><?php echo $category_description; ?>...</p>
												</div>
											</td>
											
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($category_id)); ?>','<?php echo(rawurlencode($category_title)); ?>','<?php echo(rawurlencode($category_description)); ?>','<?php echo(rawurlencode($category_color)); ?>')">Edit</a></li>
													<li class="none-separator"><a href="#"<?php if ($category_visible) echo " class=\"status_yes\"" ?>>Visible</a></li>
<li><a href="#" onclick="userAction('d',
	'<?php echo(rawurlencode($category_id)); ?>',
	'<?php echo(rawurlencode($category_title)); ?>',
	'<?php echo(rawurlencode($category_description)); ?>',
	'<?php echo(rawurlencode($category_color)); ?>',
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
					<?php print form::close(); ?>
				</div>
				
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active">Add/Edit</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'catMain',
						 	'name' => 'catMain')); ?>
						<input type="hidden" id="feed_id" 
							name="feed_id" value="" />
						<input type="hidden" name="action" 
							id="action" value=""/>
						<div class="tab_form_item">
							<strong>Feed Name:</strong><br />
							<?php print form::input('feed_name', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong>Feed URL:</strong><br />
							<?php print form::input('feed_url', '', ' class="text long"'); ?>
						</div>						
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>			
					</div>
				</div>
			</div>