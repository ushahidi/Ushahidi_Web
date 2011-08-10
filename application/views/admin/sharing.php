<?php 
/**
 * Sharing view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Sharing view
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php admin::manage_subtabs("sharing"); ?>
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
						<h3><?php echo $form_action; ?>!</h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'sharingListing',
					 	'name' => 'sharingListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="sharing_id" id="sharing_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2"><?php echo Kohana::lang('ui_main.sharing');?></th>
										<th class="col-3"><?php echo Kohana::lang('ui_main.color');?></th>
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
									foreach ($shares as $share)
									{
										$sharing_id = $share->id;
										$sharing_name = $share->sharing_name;
										$sharing_color = $share->sharing_color;
										$sharing_url = $share->sharing_url;
										$sharing_active = $share->sharing_active;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $sharing_name; ?></h4>
												</div>
												<ul class="links">
													<?php
													if($sharing_url)
													{
														?><li class="none-separator"><strong><?php echo text::auto_link("http://".$sharing_url); ?></strong></li><?php
													}
													?>
												</ul>
											</td>
											<td class="col-3">
											<?php echo "<img src=\"".url::base()."swatch/?c=".$sharing_color."&w=30&h=30\">"; ?>
											</td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($sharing_id)); ?>','<?php echo(rawurlencode($sharing_url)); ?>','<?php echo(rawurlencode($sharing_name)); ?>','<?php echo(rawurlencode($sharing_color)); ?>')"><?php echo Kohana::lang('ui_main.edit');?></a></li>
													<li class="none-separator">
													<?php if($sharing_active) {?>
													<a href="javascript:sharingAction('h','HIDE',<?php echo rawurlencode($sharing_id);?>)" class="status_yes"><?php echo Kohana::lang('ui_main.visible');?></a>
													<?php } else {?>
													<a href="javascript:sharingAction('v','SHOW',<?php echo rawurlencode($sharing_id);?>)" class="status_yes"><?php echo Kohana::lang('ui_main.hidden');?></a>
													<?php } ?>
													</li>
<li><a href="javascript:sharingAction('d','DELETE','<?php echo(rawurlencode($sharing_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'sharingMain', 'name' => 'sharingMain')); ?>
						<input type="hidden" id="sharing_id" 
							name="sharing_id" value="" />
						<input type="hidden" name="action" 
							id="action" value="a"/>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.name');?>:</strong><br />
							<?php print form::input('sharing_name', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.site_url');?>:</strong><br />
							<?php print form::input('sharing_url', '', ' class="text long"'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.color');?>:</strong><br />
							<?php print form::input('sharing_color', '', ' class="text"'); ?>
							<script type="text/javascript" charset="utf-8">
								$(document).ready(function() {
									$('#sharing_color').ColorPicker({
										onSubmit: function(hsb, hex, rgb) {
											$('#sharing_color').val(hex);
										},
										onChange: function(hsb, hex, rgb) {
											$('#sharing_color').val(hex);
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
							&nbsp;<br />
							<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>			
					</div>
				</div>
			</div>
