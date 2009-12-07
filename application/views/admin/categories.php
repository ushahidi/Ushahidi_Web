<?php 
/**
 * Categories view page.
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
					<a href="<?php echo url::base() . 'admin/manage' ?>" class="active">Categories</a>
					<span>(<a href="#add">Add New</a>)</span>
					<a href="<?php echo url::base() . 'admin/manage/forms' ?>">Forms</a>
					<a href="<?php echo url::base() . 'admin/manage/organizations' ?>">Organizations</a>
					<a href="<?php echo url::base() . 'admin/manage/pages' ?>">Pages</a>
					<a href="<?php echo url::base() . 'admin/manage/feeds' ?>">News Feeds</a>
					<a href="<?php echo url::base() . 'admin/manage/layers' ?>">Layers</a>
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
										$parent_id = $category->parent_id;
										$category_title = $category->category_title;
										$category_description = substr($category->category_description, 0, 150);
										$category_color = $category->category_color;
										$category_image = $category->category_image;
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
											<td class="col-3">
											<?php if (!empty($category_image))
											{
												echo "<img src=\"".url::base()."media/uploads/".$category_image."\">";
												echo "&nbsp;[<a href=\"javascript:catAction('i','DELETE ICON','".rawurlencode($category_id)."')\">delete</a>]";
											}
											else
											{
												echo "<img src=\"".url::base()."swatch/?c=".$category_color."&w=30&h=30\">";
											}
											?>
											</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($category_id)); ?>','<?php echo(rawurlencode($parent_id)); ?>','<?php echo(rawurlencode($category_title)); ?>','<?php echo(rawurlencode($category_description)); ?>','<?php echo(rawurlencode($category_color)); ?>','<?php echo(rawurlencode($category_image)); ?>')">Edit</a></li>
													<li class="none-separator"><a href="javascript:catAction('v','SHOW/HIDE','<?php echo(rawurlencode($category_id)); ?>')"<?php if ($category_visible) echo " class=\"status_yes\"" ?>>Visible</a></li>
<li><a href="javascript:catAction('d','DELETE','<?php echo(rawurlencode($category_id)); ?>')" class="del">Delete</a></li>
												</ul>
											</td>
										</tr>
										<?php
										
										// Get All Category Children
										foreach ($category->children as $child)
										{
											$category_id = $child->id;
											$parent_id = $child->parent_id;
											$category_title = $child->category_title;
											$category_description = substr($child->category_description, 0, 150);
											$category_color = $child->category_color;
											$category_image = $child->category_image;
											$category_visible = $child->category_visible;
											?>
											<tr>
												<td class="col-1">&nbsp;</td>
												<td class="col-2_sub">
													<div class="post">
														<h4><?php echo $category_title; ?></h4>
														<p><?php echo $category_description; ?>...</p>
													</div>
												</td>
												<td class="col-3">
												<?php if (!empty($category_image))
												{
													echo "<img src=\"".url::base()."media/uploads/".$category_image."\">";
													echo "&nbsp;[<a href=\"javascript:catAction('i','DELETE ICON','".rawurlencode($category_id)."')\">delete</a>]";
												}
												else
												{
													echo "<img src=\"".url::base()."swatch/?c=".$category_color."&w=30&h=30\">";
												}
												?>
												</td>
												<td class="col-4">
													<ul>
														<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($category_id)); ?>','<?php echo(rawurlencode($parent_id)); ?>','<?php echo(rawurlencode($category_title)); ?>','<?php echo(rawurlencode($category_description)); ?>','<?php echo(rawurlencode($category_color)); ?>','<?php echo(rawurlencode($category_image)); ?>')">Edit</a></li>
														<li class="none-separator"><a href="javascript:catAction('v','SHOW/HIDE','<?php echo(rawurlencode($category_id)); ?>')"<?php if ($category_visible) echo " class=\"status_yes\"" ?>>Visible</a></li>
	<li><a href="javascript:catAction('d','DELETE','<?php echo(rawurlencode($category_id)); ?>')" class="del">Delete</a></li>
													</ul>
												</td>
											</tr>
											<?php
										}										
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
						<?php print form::open(NULL,array('enctype' => 'multipart/form-data', 
							'id' => 'catMain', 'name' => 'catMain')); ?>
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
							<strong>Parent Category:</strong><br />
							<?php print form::dropdown('parent_id', $parents_array, '0'); ?>
						</div>
						<div style="clear:both"></div>
						<div class="tab_form_item">
							<strong>Image/Icon:</strong><br />
							<?php
								
								// I removed $category_image from the second parameter to fix bug #161
								print form::upload('category_image', '', '');
							
							?>
						</div>
						<div style="clear:both"></div>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>			
					</div>
				</div>
			</div>
