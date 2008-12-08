			<div class="bg">
				<h2><a href="<?php echo url::base() . 'admin/manage' ?>" class="active">Categories</a><span>(<a href="#add">Add New</a>)</span><a href="<?php echo url::base() . 'admin/manage/organizations' ?>">Organizations</a><a href="<?php echo url::base() . 'admin/manage/feeds' ?>">News Feeds</a></h2>
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
						<h3>The Category Has Been <?php echo $form_action; ?>!</h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'catListing',
					 	'name' => 'catListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="category_id" id="category_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2">Category</th>
										<th class="col-3">Color</th>
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
										$locale = $category->locale;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $category_title; ?></h4>
													<p><?php echo $category_description; ?>...</p>
												</div>
											</td>
											<td class="col-3"><img src="<?php echo url::base() . "swatch/?c=" . $category_color . "&w=30&h=30"; ?>"></td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($category_id)); ?>','<?php echo(rawurlencode($category_title)); ?>','<?php echo(rawurlencode($category_description)); ?>','<?php echo(rawurlencode($category_color)); ?>','<?php echo(rawurlencode($locale)); ?>')">Edit</a></li>
													<li class="none-separator"><a href="javascript:catAction('v','SHOW/HIDE','<?php echo(rawurlencode($category_id)); ?>')"<?php if ($category_visible) echo " class=\"status_yes\"" ?>>Visible</a></li>
<li><a href="javascript:catAction('d','DELETE','<?php echo(rawurlencode($category_id)); ?>')" class="del">Delete</a></li>
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
						<input type="hidden" id="category_id" 
							name="category_id" value="" />
						<input type="hidden" name="action" 
							id="action" value="a"/>
						<div class="tab_form_item">
							<strong>Category Name:</strong><br />
							<?php print form::input('category_title', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong>Description:</strong><br />
							<?php print form::input('category_description', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong>Color:</strong><br />
							<?php print form::input('category_color', '', ' class="text"'); ?>
							<script type="text/javascript" charset="utf-8">
								$(document).ready(function() {
									$('#category_color').ColorPicker({
										onSubmit: function(hsb, hex, rgb) {
											$('#category_color').val(hex);
										},
										onChange: function(hsb, hex, rgb) {
											$('#category_color').val(hex);
										},
										onBeforeShow: function () {
											$(this).ColorPickerSetColor(this.value);
										}
									})
									.bind('keyup', function(){
										$(this).ColorPickerSetColor(this.value);
									});
								});
							</script>
						</div>
						<div class="tab_form_item">
							<strong>Locale:</strong><br />
							<?php print form::dropdown('locale', $locale_array, '') ?>
						</div>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>			
					</div>
				</div>
			</div>