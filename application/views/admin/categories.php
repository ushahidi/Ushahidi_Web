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
					<?php admin::manage_subtabs("categories"); ?>
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
						<h3><?php echo Kohana::lang('ui_main.category_has_been');?> <?php echo $form_action; ?>!</h3>
					</div>
				<?php
				}
				?>
				
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active" onclick="show_addedit(true)"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab" id="addedit" style="display:none">
						<?php print form::open(NULL,array('enctype' => 'multipart/form-data', 
							'id' => 'catMain', 'name' => 'catMain')); ?>
						<input type="hidden" id="category_id" name="category_id" value="<?php echo $form['category_id']; ?>" />
						<input type="hidden" name="action" id="action" value="a"/>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.category_name');?>:</strong><br />
							<?php print form::input('category_title', $form['category_title'], ' class="text"'); ?><br/>
							<a href="#" id="category_translations" class="new-cat" style="clear:both;">Category Translations</a>
							<div id="category_translations_form_fields" style="display:none;">
								<div style="clear:both;"></div>
								<?php
									foreach($locale_array as $lang_key => $lang_name){
										echo '<div style="margin-top:10px;"><strong>'.$lang_name.':</strong></div>';
										print form::input('category_title_lang['.$lang_key.']', $form['category_title_'.$lang_key], ' class="text" id="category_title_'.$lang_key.'"');
										echo '<br />';
									}
								?>

							</div>
						</div>

						<script type="text/javascript">
						    $(document).ready(function() {

						    $('a#category_translations').click(function() {
							    $('#category_translations_form_fields').toggle(400);
							    $('#category_translations').toggle(0);
							    return false;
							});

							});
						</script>

						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.description');?>:</strong><br />
							<?php print form::input('category_description', $form['category_description'], ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_admin.color');?>:</strong><br />
							<?php print form::input('category_color', $form['category_color'], ' class="text"'); ?>
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
							<strong><?php echo Kohana::lang('ui_main.parent_category');?>:</strong><br />
							<?php print form::dropdown('parent_id', $parents_array, '0'); ?>
						</div>
						<div style="clear:both"></div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.image_icon');?>:</strong><br />
							<?php

								// I removed $category_image from the second parameter to fix bug #161
								print form::upload('category_image', '', '');
							?>
						</div>
						<div style="clear:both"></div>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>			
					</div>
				</div>
				
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'catListing',
					 	'name' => 'catListing')); ?>
						<input type="hidden" name="action" id="category_action" value="">
						<input type="hidden" name="category_id" id="category_id_action" value="">
						<div class="table-holder">
							<table class="table" id="categorySort">
								<thead>
									<tr class="nodrag">
										<th class="col-1">&nbsp;</th>
										<th class="col-2"><?php echo Kohana::lang('ui_main.category');?></th>
										<th class="col-3"><?php echo Kohana::lang('ui_main.color');?></th>
										<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
									</tr>
								</thead>
								<tfoot>
									<tr class="foot nodrag">
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
										<tr class="nodrag">
											<td colspan="4" class="col" id="row1">
												<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
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
										$category_trusted = $category->category_trusted;
										$category_locals = array();
										foreach($category->category_lang as $category_lang){
											$category_locals[$category_lang->locale] = $category_lang->category_title;
										}
										?>
										<tr id="<?php echo $category_id; ?>">
											<td class="col-1 col-drag-handle">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $category_title; ?></h4>
													<p><?php echo $category_description; ?></p>
												</div>
											</td>
											<td class="col-3">
											<?php if (!empty($category_image))
											{
												echo "<img src=\"".url::base().Kohana::config('upload.relative_directory')."/".$category_image."\">";
												echo "&nbsp;[<a href=\"javascript:catAction('i','DELETE ICON','".rawurlencode($category_id)."')\">".Kohana::lang('ui_main.delete')."</a>]";
											}
											else
											{
												echo "<img src=\"".url::base()."swatch/?c=".$category_color."&w=30&h=30\">";
											}
											?>
											</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($category_id)); ?>','<?php echo(rawurlencode($parent_id)); ?>','<?php echo(rawurlencode($category_title)); ?>','<?php echo(rawurlencode($category_description)); ?>','<?php echo(rawurlencode($category_color)); ?>','<?php echo(rawurlencode($category_image)); ?>'<?php
													foreach($locale_array as $lang_key => $lang_name){
														echo ',';
														if(isset($category_locals[$lang_key])){
															echo ' \''.rawurlencode($category_locals[$lang_key]).'\'';
														}else{
															echo ' \'\'';
														}
													}
													?>)"><?php echo Kohana::lang('ui_main.edit');?></a></li>
													<li class="none-separator"><a class="status_yes" href="javascript:catAction('v','SHOW/HIDE','<?php echo(rawurlencode($category_id)); ?>')"><?php if ($category_visible) { echo Kohana::lang('ui_main.visible'); } else { echo Kohana::lang('ui_main.hidden'); }?></a></li>
<li><a href="javascript:catAction('d','DELETE','<?php echo(rawurlencode($category_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
												</ul>
												
												<?php if($category_trusted == 1) { ?>
												<div class="right">
													<a href="#" class="tooltip" title="<?php echo htmlentities(Kohana::lang('ui_admin.special_category_explanation'),ENT_QUOTES);?>"><strong><?php echo Kohana::lang('ui_admin.special_category');?></strong></a>
												</div>
												<?php } ?>
												
											</td>
										</tr>
										<?php
										
										// Get All Category Children
										foreach ( $category->orderby('category_position', 'asc')->children as $child)
										{
											$category_id = $child->id;
											$parent_id = $child->parent_id;
											$category_title = $child->category_title;
											$category_description = substr($child->category_description, 0, 150);
											$category_color = $child->category_color;
											$category_image = $child->category_image;
											$category_visible = $child->category_visible;

											$child_category_locals = array();
											foreach($child->category_lang as $category_lang){
												$child_category_locals[$category_lang->locale] = $category_lang->category_title;
											}

											?>
											<tr id="<?php echo $category_id; ?>">
												<td class="col-1 col-drag-handle">&nbsp;</td>
												<td class="col-2_sub">
													<div class="post">
														<h4><?php echo $category_title; ?></h4>
														<p><?php echo $category_description; ?>...</p>
													</div>
												</td>
												<td class="col-3">
												<?php if (!empty($category_image))
												{
													echo "<img src=\"".url::base().Kohana::config('upload.relative_directory')."/".$category_image."\">";
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
														<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($category_id)); ?>','<?php echo(rawurlencode($parent_id)); ?>','<?php echo(rawurlencode($category_title)); ?>','<?php echo(rawurlencode($category_description)); ?>','<?php echo(rawurlencode($category_color)); ?>','<?php echo(rawurlencode($category_image)); ?>'<?php
													foreach($locale_array as $lang_key => $lang_name){
														echo ',';
														if(isset($child_category_locals[$lang_key])){
															echo ' \''.rawurlencode($child_category_locals[$lang_key]).'\'';
														}else{
															echo ' \'\'';
														}
													}
													?>)"><?php echo Kohana::lang('ui_main.edit');?></a></li>
														<li class="none-separator"><a href="javascript:catAction('v','SHOW/HIDE','<?php echo(rawurlencode($category_id)); ?>')"<?php if ($category_visible) echo " class=\"status_yes\"" ?>><?php echo Kohana::lang('ui_main.visible');?></a></li>
	<li><a href="javascript:catAction('d','DELETE','<?php echo(rawurlencode($category_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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

			</div>
