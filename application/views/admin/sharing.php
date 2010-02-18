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
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>	
		<div class="bg">
			<h2><?php echo $title; ?> 
				<a href="<?php echo url::base() . 'admin/settings/site' ?>">Site</a>
				<a href="<?php echo url::base() . 'admin/settings' ?>">Map</a>
				<a href="<?php echo url::base() . 'admin/settings/sms' ?>">SMS</a>
				<a href="<?php echo url::base() . 'admin/settings/sharing' ?>" class="active">Sharing</a>
				<a href="<?php echo url::base() . 'admin/settings/email' ?>">Email</a>
				<a href="<?php echo url::base() . 'admin/settings/themes' ?>">Themes</a>
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
					<h3>The Share Has Been <?php echo $form_action; ?>!</h3>
				</div>
			<?php
			}
			?>
				
			<!-- tabs -->
			<div class="tabs">
				<!-- tabset -->
				<a name="add"></a>
				<ul class="tabset">
					<li><a href="#" class="active">Add/Edit A Share</a></li>
				</ul>
				<!-- tab -->
				<div class="tab">
					<?php print form::open(NULL,array('id' => 'sharingMain',
					 	'name' => 'sharingMain')); ?>
					<input type="hidden" name="action" 
						id="sharing_action" value="a"/>
					<input type="hidden" id="sharing_id_action" 
						name="sharing_id" value="" />
					<input type="hidden" id="sharing_type" 
						name="sharing_type" value="" />	
					<div class="tab_form_item">
						<strong>Site Url:</strong><br />
						<?php print form::input('sharing_url', 'http://', ' class="text long2"'); ?>
					</div>
					<div class="tab_form_item">
						<strong>Color:</strong><br />
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
					<div class="tab_form_item">
						<strong>Access Limits:</strong> (Sending)<br />
						<?php print form::dropdown('sharing_limits', $sharing_limits_array, ''); ?>
					</div>				
					<div class="tab_form_item">
						&nbsp;<br />
						<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
					</div>
					<div class="tab_form_item" id="sharing_loading"></div>
					<div style="clear:both;"></div>
					<div class="tab_form_item">
						The following information will be sent with this request:
						<div class="sharing_siteinfo">
						Website: <span><?php echo url::base(); ?></span>, 
						Site Email: <span><?php echo $site_email; ?></span>
						</div>
					</div>
					<?php print form::close(); ?>			
				</div>
			</div>
			
			
			<!-- tabs -->
			<div class="tabs">
				<!-- tabset -->
				<ul class="tabset">
					<li><a href="?status=0" <?php if ($status != 's' && $status !='r') echo "class=\"active\""; ?>>Show All</a></li>
					<li><a href="?status=s" <?php if ($status == 's') echo "class=\"active\""; ?>>Sending To</a></li>
					<li><a href="?status=r" <?php if ($status == 'r') echo "class=\"active\""; ?>>Receiving From</a></li>
				</ul>
				<!-- tab -->
				<div class="tab">
					&nbsp;
				</div>
			</div>
				
				
			<!-- report-table -->
			<div class="report-form">				
				<div class="table-holder">
					<table class="table">
						<thead>
							<tr>
								<th class="col-1">&nbsp;</th>
								<th class="col-2">Organization</th>
								<th class="col-3">&nbsp;</th>
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
							foreach ($shares as $share)
							{
								$share_id = $share->id;
								$sharing_type = $share->sharing_type;
								$sharing_limits = $share->sharing_limits;
								$sharing_site_name = $share->sharing_site_name;
								$sharing_email = $share->sharing_email;
								$sharing_color = $share->sharing_color;
								$sharing_key = $share->sharing_key;
								$sharing_url = "http://".$share->sharing_url;
								$sharing_active = $share->sharing_active;
								$sharing_date = $share->sharing_date;
								$sharing_dateaccess = $share->sharing_dateaccess;
							
								$sharing_site_name = ($sharing_site_name) ? $sharing_site_name : "??? <span>[ SHARE AWAITING APPROVAL ]</span>";
								$sharing_image = ($sharing_type == 1) ? "down" : "up";		// Up or down image
								$sharing_image .= ($sharing_active == 1) ? "" : "_gray";	// If inactive use gray image
								$sharing_color = ($sharing_type == 2) ? "ffffff" : $sharing_color;
								
								$sharing_dateaccess = ($sharing_dateaccess) ? date("Y-m-d H:i:s", $sharing_dateaccess)
								 	: "Never";
								?>
								<?php print form::open(NULL,array('id' => 'share_action_' . $share_id,
								 	'name' => 'share_action_' . $share_id )); ?>
									<input type="hidden" name="action" id="action_<?php echo $share_id;?>" value="">
									<input type="hidden" name="share_id" value="<?php echo $share_id;?>">
									<tr id="tr_<?php echo $share_id; ?>">
										<td class="col-1"><img src="<?php echo url::base().'media/img/admin/sharing_'.$sharing_image.'.gif'; ?>" style="margin-right:10px;"></td>
										<td class="col-2">
											<div class="post">
												<h4><?php echo $sharing_site_name; ?></h4>
												<p><?php echo html::anchor($sharing_url); ?></p>
												<div class="sharing_dispinfo">
													<ul class="info">
														<li class="none-separator">Date Added: <strong><?php echo $sharing_date; ?></strong></li>
														<li>Key: <strong><?php echo $sharing_key; ?></strong></li>
													</ul>
													<ul class="info">
														<li class="none-separator">Contact: <strong><?php echo html::mailto($sharing_email); ?></strong></li>
														<li>Last Access: <strong><?php echo $sharing_dateaccess; ?></strong></li>
													</ul>
													<?php if ($sharing_type == 2)
													{
														?>
														<ul class="info">
															<li class="none-separator">Access Limited To: <strong><?php echo $sharing_limits_array[$sharing_limits]; ?></strong></li>
														</ul>
														<?php
													}?>
												</div>
											</div>
										</td>
										<td><?php echo "<img src=\"".url::base()."swatch/?c=".$sharing_color."&w=30&h=30\">";?></td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($share_id)); ?>','<?php echo(rawurlencode($sharing_url)); ?>','<?php echo(rawurlencode($sharing_color)); ?>','<?php echo(rawurlencode($sharing_limits)); ?>','<?php echo(rawurlencode($sharing_type)); ?>')">Edit</a></li>
												<li class="none-separator"><a href="javascript:sharingAction('v','ACTIVATE/DEACTIVATE','<?php echo(rawurlencode($share_id)); ?>')"<?php if ($sharing_active) echo " class=\"status_yes\"" ?>>Active</a></li>
												<li><a href="javascript:sharingAction('d','DELETE','<?php echo(rawurlencode($share_id)); ?>')" class="del">Delete</a></li>
											</ul>
										</td>
									</tr>
								<?php print form::close();
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			
			
		</div>