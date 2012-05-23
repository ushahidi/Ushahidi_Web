<?php 
/**
 * Blocks view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Blocks View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php admin::manage_subtabs("blocks"); ?>
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
				
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'blockListing',
					 	'name' => 'blockListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="block" id="block" value="">
						<div class="table-holder">
							<table class="table" id="blockSort">
								<thead>
									<tr class="nodrag">
										<th class="col-1">&nbsp;</th>
										<th class="col-2"><?php echo Kohana::lang('ui_admin.blocks');?></th>
										<th class="col-3">&nbsp;</th>
										<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
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
									foreach ($sorted_blocks as $key)
									{
										$block = $registered_blocks[$key];
										$block_name = $block['name'];
										$block_description = $block['description'];
										
										$block_visible = FALSE;
										if (in_array($key, $active_blocks))
										{
											$block_visible = TRUE;
										}
										?>
										<tr id="<?php echo $key; ?>"<?php if ( ! $block_visible) echo " class=\"nodrag nodrop\"" ?>>
											<td class="col-1 <?php if ( $block_visible) echo "col-drag-handle" ?>">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $block_name; ?></h4>
													<p><?php echo $block_description; ?></p>
												</div>
											</td>
											<td class="col-3">&nbsp;</td>
											<td class="col-4">
												<ul>
													<li class="none-separator">
													<?php if ($block_visible) { ?>
													<a href="javascript:blockAction('d','HIDE','<?php echo(rawurlencode($key)); ?>')"<?php echo " class=\"status_yes\"" ?>><?php echo Kohana::lang('ui_main.visible');?></a>
													<?php } else { ?>
													<a href="javascript:blockAction('a','SHOW','<?php echo(rawurlencode($key)); ?>')"<?php echo " class=\"status_yes\"" ?>><?php echo Kohana::lang('ui_main.hidden');?></a>
													<?php } ?>
													</li>
												</ul>
											</td>
										</tr>
										<?php
										$i++;									
									}
									?>
								</tbody>
							</table>
						</div>
					<?php print form::close(); ?>
				</div>

			</div>
