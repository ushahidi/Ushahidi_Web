<?php 
/**
 * Layers view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Layers view
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<a href="<?php echo url::base() . 'admin/manage' ?>">Categories</a>
					<a href="<?php echo url::base() . 'admin/manage/forms' ?>">Forms</a>
					<a href="<?php echo url::base() . 'admin/manage/organizations' ?>">Organizations</a>
					<a href="<?php echo url::base() . 'admin/manage/pages' ?>">Pages</a>
					<a href="<?php echo url::base() . 'admin/manage/feeds' ?>">News Feeds</a>
					<a href="<?php echo url::base() . 'admin/manage/layers' ?>" class="active">Layers</a>
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
						<h3>The Layer Has Been <?php echo $form_action; ?>!</h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'layerListing',
					 	'name' => 'layerListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="layer_id" id="layer_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2">Layer</th>
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
									foreach ($layers as $layer)
									{
										$layer_id = $layer->id;
										$layer_name = $layer->layer_name;
										$layer_color = $layer->layer_color;
										$layer_url = $layer->layer_url;
										$layer_file = $layer->layer_file;
										$layer_visible = $layer->layer_visible;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $layer_name; ?></h4>
												</div>
												<ul class="info">
													<?php
													if($layer_file)
													{
														?><li class="none-separator">KMZ/KML File: <strong><?php echo $layer_file; ?></strong>
														&nbsp;[<a href="javascript:layerAction('i','DELETE FILE','<?php echo rawurlencode($layer_id);?>')">Delete</a>]</li>
														<?php
													}
													?>
												</ul>
												<ul class="links">
													<?php
													if($layer_url)
													{
														?><li class="none-separator">KML URL: <strong><?php echo text::auto_link($layer_url); ?></strong></li><?php
													}
													?>
												</ul>
											</td>
											<td class="col-3">
											<?php echo "<img src=\"".url::base()."swatch/?c=".$layer_color."&w=30&h=30\">"; ?>
											</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($layer_id)); ?>','<?php echo(rawurlencode($layer_name)); ?>','<?php echo(rawurlencode($layer_url)); ?>','<?php echo(rawurlencode($layer_color)); ?>','<?php echo(rawurlencode($layer_file)); ?>')">Edit</a></li>
													<li class="none-separator"><a href="javascript:layerAction('v','SHOW/HIDE','<?php echo(rawurlencode($layer_id)); ?>')"<?php if ($layer_visible) echo " class=\"status_yes\"" ?>>Visible</a></li>
<li><a href="javascript:layerAction('d','DELETE','<?php echo(rawurlencode($layer_id)); ?>')" class="del">Delete</a></li>
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
						<?php print form::open(NULL,array('enctype' => 'multipart/form-data', 
							'id' => 'layerMain', 'name' => 'layerMain')); ?>
						<input type="hidden" id="layer_id" 
							name="layer_id" value="" />
						<input type="hidden" name="action" 
							id="action" value="a"/>
						<input type="hidden" name="layer_file_old" 
							id="layer_file_old" value=""/>
						<div class="tab_form_item">
							<strong>Layer Name:</strong><br />
							<?php print form::input('layer_name', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong>Layer URL:</strong><br />
							<?php print form::input('layer_url', '', ' class="text long"'); ?>
						</div>
						<div class="tab_form_item">
							<strong>Color:</strong><br />
							<?php print form::input('layer_color', '', ' class="text"'); ?>
							<script type="text/javascript" charset="utf-8">
								$(document).ready(function() {
									$('#layer_color').ColorPicker({
										onSubmit: function(hsb, hex, rgb) {
											$('#layer_color').val(hex);
										},
										onChange: function(hsb, hex, rgb) {
											$('#layer_color').val(hex);
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
						<div style="clear:both"></div>
						<div class="tab_form_item">
							<strong>Upload KMZ/KML File:</strong><br />
							<?php print form::upload('layer_file', '', ''); ?>
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
