<?php
/**
 * External Apps view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     External Apps View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
			<div class="bg">
				<h2>
					<?php admin::settings_subtabs("externalapps"); ?>
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
						<h3><?php echo Kohana::lang('ui_main.saved');?>!</h3>
					</div>
				<?php
				}
				?>

				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active" onclick="show_addedit(true)"><?php echo Kohana::lang('ui_main.add');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab" id="addedit" style="display:none">
						<?php print form::open(NULL,array('enctype' => 'multipart/form-data', 'id' => 'externalappMain', 'name' => 'externalappMain')); ?>
						<input type="hidden" id="id" name="id" value="<?php echo $form['id']; ?>" />
						<input type="hidden" name="action" id="action" value="a"/>

						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.name');?>:</strong><br />
							<?php print form::input('name', $form['name'], ' class="text"'); ?>
						</div>

						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.url');?>:</strong><br />
							<?php print form::input('url', $form['url'], ' class="text"'); ?>
						</div>

						<div style="clear:both"></div>
						<div class="tab_form_item">
							<input type="submit" class="save-rep-btn" value="<?php echo Kohana::lang('ui_main.save');?>" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>

				<!-- externalapp-table -->
				<div>

					<div class="table-holder">
						<table class="table">
							<thead>
								<tr class="nodrag">
									<th class="col-1">&nbsp;</th>
									<th class="col-2" style="width:80px;"><?php echo Kohana::lang('ui_main.external_apps');?></th>
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
								foreach ($externalapps as $app)
								{
							?>
									<tr>
										<td class="col-1">&nbsp;</td>
										<td class="col-2" style="width:80px;">
											<?php
											// TODO: Put image here once it's in
											?>
										</td>
										<td class="col-3" style="width:600px;font-weight:normal;">
											<strong><?php echo $app->name; ?></strong>
											<br/><a href="<?php echo $app->url; ?>"><?php echo $app->url; ?></a>

										</td>
										<td class="col-4" style="width:120px;">

											<ul>
												<li><a href="javascript:appAction('d','<?php echo utf8::strtoupper(htmlspecialchars(Kohana::lang('ui_main.remove').' '.$app->name));?>','<?php echo rawurlencode($app->id); ?>')" class="del"><?php echo Kohana::lang('ui_main.remove');?></a></li>
											</ul>

										</td>
									</tr>
							<?php
								}
							?>
							</tbody>
						</table>
					</div>
				</div>

			</div>
