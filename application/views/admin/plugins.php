<?php 
/**
 * Plugins view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Plugin Settings View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2><?php echo $title; ?> 
					<a href="<?php echo url::base() . 'admin/addons/plugins' . '" class="active">' . Kohana::lang('ui_main.plugins') . '</a>' ?>
					<a href="<?php echo url::base() . 'admin/addons/themes' . '">' . Kohana::lang('ui_main.themes') . '</a>' ?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?status=0" <?php if ($status !='i' && $status !='a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a></li>
						<li><a href="?status=i" <?php if ($status == 'i') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.inactive');?></a></li>
						<li><a href="?status=a" <?php if ($status == 'a') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.active');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onclick="pluginAction('a','ACTIVATE', '');"><?php echo strtoupper(Kohana::lang('ui_main.activate'));?></a></li>
							<li><a href="#" onclick="pluginAction('i','DEACTIVATE', '');"><?php echo strtoupper(Kohana::lang('ui_main.deactivate'));?></a></li>
						</ul>
					</div>
				</div>
				<?php
				if ($form_error)
				{
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved)
				{
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_admin.plugins'); ?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'pluginMain', 'name' => 'pluginMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="plugin_id[]" id="plugin_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkallplugins" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'plugin_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.plugins');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.version');?></th>
									<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
								</tr>
							</thead>
							<tfoot>
								<tr class="foot">
									<td colspan="4">
										&nbsp;
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

								foreach ($plugins as $plugin)
								{
									$plugin_id = $plugin->id;
									$plugin_active = $plugin->plugin_active;
									
									// Retrieve Plugin Header Information from readme.txt
									$plugin_meta = plugin::meta($plugin->plugin_name);

									// Do we have a settings page?
									$settings = plugin::settings($plugin->plugin_name);
									?>
									<tr <?php if ($plugin_active)
									{
										echo " class=\"addon_active\" ";
									}?>>
										<td class="col-1"><input name="plugin_id[]" id="plugin" value="<?php echo $plugin_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<h4>
												<?php echo $plugin_meta["plugin_name"]; ?><?php
												if ($plugin_active AND $settings)
												{
													echo "&nbsp;&nbsp;&nbsp;[<a href=\"".url::base()."admin/".$settings."\">".Kohana::lang('ui_admin.settings')."</a>]";
												}
												?></h4>
												<p><?php echo $plugin_meta["plugin_description"]; ?></p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.author');?>: <strong><?php echo $plugin_meta["plugin_author"]; ?></strong></li>
												<li><?php echo Kohana::lang('ui_main.plugin_url');?>: <strong><?php echo $plugin_meta["plugin_uri"]; ?></strong></li>
											</ul>
										</td>
										<td class="col-3"><?php echo $plugin_meta["plugin_version"]; ?></td>
										<td class="col-4">
											<ul>
												<li class="none-separator"><?php
												if ($plugin_active)
												{
													?><a href="#" class="status_no" onclick="pluginAction('i','DEACTIVATE', '<?php echo $plugin_id; ?>');"><?php echo Kohana::lang('ui_main.deactivate');?></a><?php
												}
												else
												{
													?><a href="#" class="status_yes" onclick="pluginAction('a','ACTIVATE', '<?php echo $plugin_id; ?>');"><?php echo Kohana::lang('ui_main.activate');?></a><?php
												}
												?></li>
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
