<?php 
/**
 * Organizations view page.
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
					<a href="<?php echo url::base() . 'admin/manage' ?>">Categories</a>
					<a href="<?php echo url::base() . 'admin/manage/forms' ?>">Forms</a>
					<a href="<?php echo url::base() . 'admin/manage/organizations' ?>" class="active">Organizations</a>
					<span>(<a href="#add">Add New</a>)</span>
					<a href="<?php echo url::base() . 'admin/manage/pages' ?>">Pages</a>
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
						<h3>The Organization Has Been <?php echo $form_action; ?></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'orgListing',
					 	'name' => 'orgListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="organization_id" id="org_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2">Organization</th>
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
									foreach ($organizations as $organization)
									{
										$organization_id = $organization->id;
										$organization_name =
										 $organization->organization_name;
										$organization_description = $organization->organization_description;
										$organization_description_short = substr($organization->organization_description, 0, 150);
										$organization_website = 
											$organization->organization_website;
										$organization_active = $organization->organization_active;
										$organization_email = $organization->organization_email;
										$organization_phone1 = $organization->organization_phone1;
										$organization_phone2 = $organization->organization_phone2;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $organization_name; ?></h4>
													<p><?php echo $organization_description_short; ?>...</p>
												</div>
											</td>
											
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields(
	'<?php echo(rawurlencode($organization_id)); ?>',
	'<?php echo(rawurlencode($organization_name)); ?>',
	'<?php echo(rawurlencode($organization_website)); ?>',
	'<?php echo(rawurlencode($organization_description)); ?>',
	'<?php echo(rawurlencode($organization_email)); ?>',
	'<?php echo(rawurlencode($organization_phone1)); ?>',
	'<?php echo(rawurlencode($organization_phone2)); ?>')">Edit</a></li>
	<li class="none-separator"><a href="javascript:orgAction('v','SHOW/HIDE','<?php echo(rawurlencode($organization_id)); ?>')"<?php if ($organization_active) echo " class=\"status_yes\"" ?>>Visible</a></li>
													<li><a href="javascript:orgAction('d','DELETE','<?php echo(rawurlencode($organization_id)); ?>')" class="del">Delete</a></li>
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
						<?php print form::open(NULL,array('id' => 'orgMain',
						 	'name' => 'orgMain')); ?>
						<input type="hidden" id="organization_id" 
							name="organization_id" value="<?php echo $form['organization_id']; ?>" />
						<input type="hidden" name="action" 
							id="action" value="a"/>							
						<div class="tab_form_item2">
							<strong>Organization Name:</strong><br />
							<?php print form::input('organization_name', $form['organization_name'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Organization Website:</strong><br />
							<?php print form::input('organization_website', $form['organization_website'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Organization Description:</strong><br />
							<?php print form::textarea('organization_description', $form['organization_description'], ' rows="12" cols="60" '); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Organization Email:</strong><br />
							<?php print form::input('organization_email', $form['organization_email'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Organization Phone 1:</strong><br />
							<?php print form::input('organization_phone1', $form['organization_phone1'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong>Organization Phone 2:</strong><br />
							<?php print form::input('organization_phone2', $form['organization_phone2'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>
				
			</div>
