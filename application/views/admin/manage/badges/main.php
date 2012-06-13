<?php
/**
 * Badges view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Badges View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
			<div class="bg">
				<h2>
					<?php admin::manage_subtabs("badges"); ?>
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
						<?php print form::open(NULL,array('enctype' => 'multipart/form-data', 'id' => 'badgeMain', 'name' => 'badgeMain')); ?>
						<input type="hidden" id="id" name="id" value="<?php echo $form['id']; ?>" />
						<input type="hidden" name="action" id="action" value="a"/>

						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.name');?>:</strong><br />
							<?php print form::input('name', $form['name'], ' class="text"'); ?>
						</div>

						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.description');?>:</strong><br />
							<?php print form::input('description', $form['description'], ' class="text"'); ?>
						</div>
						
						<div class="tab_form_item" style="clear:both;">
							
							<strong><?php echo Kohana::lang('ui_main.badge_select');?>:</strong><br />
							<input type="hidden" name="selected_badge" value="" id="selected_badge"  />
							<?php
								foreach($badge_packs as $pack_name => $pack)
								{
									echo '<h4>'.$pack_name.' '.Kohana::lang('ui_main.badge_pack').'</h4>';
									foreach($pack as $badge_filename)
									{
										$badge_url = url::base().'media/img/badge_packs/'.$pack_name.'/'.$badge_filename;
										$encoded_badge = base64_encode($pack_name.'/'.$badge_filename);
										echo '<img src="'.$badge_url.'" id="badge_'.$encoded_badge.'" class="badge_selection transparent-25" />'."\n";
									}
								}
							?>
						</div>

						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.badge_image_upload_your_own');?>:</strong><br />
							<?php echo form::upload('image', '', ''); ?>
						</div>

						<div style="clear:both"></div>
						<div class="tab_form_item">
							<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_main.save');?>" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>

				<!-- badge-table -->
				<div>

					<?php print form::open(NULL,array('id' => 'badgeListing', 'name' => 'badgeListing')); ?>
						<input type="hidden" name="action" id="action" class="js_action" value="" />
						<input type="hidden" name="badge_id" id="badge_id" value="" />
						<input type="hidden" name="assign_user" id="assign_user" value="" />
						<input type="hidden" name="revoke_user" id="revoke_user" value="" />
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr class="nodrag">
										<th class="col-1">&nbsp;</th>
										<th class="col-2" style="width:80px;"><?php echo Kohana::lang('ui_main.badges');?></th>
										<th class="col-3" style="width:600px;">&nbsp;</th>
										<th class="col-4" style="width:120px;"><?php echo Kohana::lang('ui_main.actions');?></th>
									</tr>
								</thead>
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
									$i = 1;
									foreach ($badges as $badge)
									{
								?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2" style="width:80px;">
												<img src="<?php echo $badge['img_m']; ?>" alt="<?php echo Kohana::lang('ui_main.badge').' '.$badge['id'];?>" width="80" height="80" />
											</td>
											<td class="col-3" style="width:600px;font-weight:normal;">
												<strong><?php echo $badge['name']; ?></strong>
												<br/><?php echo $badge['description']; ?>
												<br/><?php echo Kohana::lang('ui_admin.assignments'); ?>: <?php echo count($badge['users']);?>

												<br/><?php echo form::dropdown('assign_user_'.$badge['id'], array_diff($users, $badge['users']), 'standard'); ?>
													<a href="javascript:badgeAction('b','<?php echo utf8::strtoupper(htmlspecialchars(Kohana::lang('ui_admin.assign')));?>','<?php echo rawurlencode($badge['id']); ?>')"><?php echo Kohana::lang('ui_admin.assign');?></a>
													<?php if(count($badge['users']) > 0) { ?>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<?php echo form::dropdown('revoke_user_'.$badge['id'], $badge['users'], 'standard'); ?>
														<a href="javascript:badgeAction('r','<?php echo utf8::strtoupper(htmlspecialchars(Kohana::lang('ui_admin.revoke')));?>','<?php echo rawurlencode($badge['id']); ?>')"><?php echo Kohana::lang('ui_admin.revoke');?></a>
													<?php } ?>

											</td>
											<td class="col-4" style="width:120px;">

												<ul>
													<li><a href="javascript:badgeAction('d','<?php echo utf8::strtoupper(htmlspecialchars(Kohana::lang('ui_admin.delete_badge')));?>','<?php echo rawurlencode($badge['id']); ?>')" class="del"><?php echo Kohana::lang('ui_admin.delete_badge');?></a></li>
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

			</div>
