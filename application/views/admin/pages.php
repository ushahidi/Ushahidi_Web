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
					<a href="<?php echo url::base() . 'admin/manage' ?>">Categories</a>
					<a href="<?php echo url::base() . 'admin/manage/forms' ?>">Forms</a>
					<a href="<?php echo url::base() . 'admin/manage/organizations' ?>">Organizations</a>
					<a href="<?php echo url::base() . 'admin/manage/pages' ?>" class="active">Pages</a>
					<span>(<a href="#add">Add New</a>)</span>
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
						<h3>The Page Has Been <?php echo $form_action; ?></h3>
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
										<th class="col-2">Page</th>
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
									foreach ($pages as $page)
									{
										$page_id = $page->id;
										$page_title = $page->page_title;
										$page_tab = $page->page_tab;
										$page_description = $page->page_description;
										$page_description_short = strip_tags(text::limit_chars($page_description, "100", "..."));
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
	'<?php echo(rawurlencode($page_title)); ?>',
	'<?php echo(rawurlencode($page_tab)); ?>',
	'<?php echo(rawurlencode($page_description)); ?>')">Edit</a></li>
	<li class="none-separator"><a href="javascript:pageAction('v','SHOW/HIDE','<?php echo(rawurlencode($page_id)); ?>')"<?php if ($page_active) echo " class=\"status_yes\"" ?>>Visible</a></li>
													<li><a href="javascript:pageAction('d','DELETE','<?php echo(rawurlencode($page_id)); ?>')" class="del">Delete</a></li>
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
						<li><a href="#" class="active">Add/Edit</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'pageMain',
						 	'name' => 'pageMain')); ?>
						<input type="hidden" id="page_id" 
							name="page_id" value="<?php echo $form['page_id']; ?>" />
						<input type="hidden" name="action" 
							id="action" value="a"/>							
						<div class="tab_form_item2">
							<strong>Page Title:</strong><br />
							<?php print form::input('page_title', $form['page_title'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Page Tab Name:</strong><br />
							<?php print form::input('page_tab', $form['page_tab'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Page Description:</strong><br />
							<?php print form::textarea('page_description', $form['page_description'], ' rows="12" cols="60" '); ?>
						</div>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>
				
			</div>
