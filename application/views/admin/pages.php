<?php 
/**
 * Pages view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Pages View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php admin::manage_subtabs("pages"); ?>
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
						<h3><?php echo Kohana::lang('ui_main.page_has_been');?> <?php echo $form_action; ?></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'pageListing',
					 	'name' => 'pageListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="page_id" id="page_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2"><?php echo Kohana::lang('ui_main.page');?></th>
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
									foreach ($pages as $page)
									{
										$page_id = $page->id;
										$page_title = $page->page_title;
										$page_tab = $page->page_tab;
										$page_description = htmlspecialchars_decode($page->page_description);
										$page_description_short = text::limit_chars(strip_tags($page_description), "100", "...");
										$page_active = $page->page_active;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $page_title; ?></h4>
													<p><?php echo $page_description_short; ?></p>
												</div>
											</td>
											
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields(
	'<?php echo(rawurlencode($page_id)); ?>',
	'<?php echo(base64_encode($page_title)); ?>',
	'<?php echo(base64_encode($page_tab)); ?>',
	'<?php echo(base64_encode($page_description)); ?>')"><?php echo Kohana::lang('ui_main.edit');?></a></li>
	<li class="none-separator"><a class="status_yes" href="javascript:pageAction('v','SHOW/HIDE','<?php echo(rawurlencode($page_id)); ?>')"><?php if ($page_active) { echo Kohana::lang('ui_main.visible'); }else{ echo Kohana::lang('ui_main.hidden'); }?></a></li>
													<li><a href="javascript:pageAction('d','DELETE','<?php echo(rawurlencode($page_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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
				
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('enctype' => 'multipart/form-data', 'id' => 'pageMain',
						 	'name' => 'pageMain')); ?>					
						<input type="hidden" id="page_id" 
							name="page_id" value="<?php echo $form['page_id']; ?>" />
						<input type="hidden" name="action" 
							id="action" value="a"/>							
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.page_title');?>:</strong><br />
							<?php print form::input('page_title', $form['page_title'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.page_tab_name');?>:</strong><br />
							<?php print form::input('page_tab', $form['page_tab'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.page_description');?>:</strong><br />
							<?php print form::textarea('page_description', $form['page_description'], ' rows="12" cols="60" '); ?>
						</div>
						<?php
							// Action::page_form_admin - Runs just after the page description
							Event::run('ushahidi_action.page_form_admin');
						?>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>
				
			</div>
