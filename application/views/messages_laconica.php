<?php 
/**
 * Messages view page. 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Messages Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
				<div class="bg">
					<h2><?php echo $title; ?> <a href="<?php print url::base() ?>admin/messages">SMS</a> <a href="<?php print url::base() ?>admin/messages/twitter">Twitter</a> <a href="<?php print url::base() ?>admin/messages/laconica">Laconica</a> </h2>
					<!-- tabs -->
					<div class="tabs">
						<!-- tabset -->
						<ul class="tabset">
							<li><a href="?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>>Inbox</a></li>
							<li><a href="?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>>Outbox</a></li>
						</ul>
						<!-- tab -->
						<div class="tab">
							<ul>
								<li><!-- <a href="#" onClick="submitIds()">DELETE</a> --> <a href="#">DELETE DISABLED</a></li>
							</ul>
						</div>
					</div>
					<?php 
					if ($form_error) {
					?>
						<!-- red-box -->
						<div class="red-box">
							<h3>Error!</h3>
							<ul>Please verify that you have checked an item</ul>
						</div>
					<?php
					}
				
					if ($form_saved) {
					?>
						<!-- green-box -->
						<div class="green-box" id="submitStatus">
							<h3>Messages <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
						</div>
					<?php
					}
					?>
					<!-- report-table -->
					<?php  
						print form::open(NULL, array('id' => 'messagesMain', 'name' => 'messagesMain')); ?>
						<input type="hidden" name="action" id="action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1"><input id="checkallincidents" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'message_id[]' )" /></th>
										<th class="col-2">Message Details</th>
										<th class="col-3">Date</th>
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
									
									foreach ($laconica_mesgs as $laconica_mesg)
									{
										$laconica_mesg_id = $laconica_mesg->id;
										$laconica_mesg_from = $laconica_mesg->laconica_mesg_from;
										$incident_id = $laconica_mesg->incident_id;
										$laconica_mesg_link = $laconica_mesg->laconica_mesg_link;
										$laconica_mesg_description = $laconica_mesg->laconica_mesg;
										$laconica_mesg_date = date('Y-m-d', 
										    strtotime($laconica_mesg->laconica_mesg_date));
										?>
										<tr>
											<td class="col-1"><input name="message_id[]" id="message_id" value="<?php echo $laconica_mesg_id; ?>" type="checkbox" class="check-box"/></td>
											<td class="col-2">
												<div class="post">
													<p><?php echo $laconica_mesg_description; ?></p>
												</div>
												<ul class="info">
													<li class="none-separator">From: <strong><a href="<?php echo $laconica_mesg_link; ?>" target="_blank"><?php echo $laconica_mesg_from; ?></a></strong>
												</ul>
											</td>
											<td class="col-3"><?php echo $laconica_mesg_date; ?></td>
											<td class="col-4">
												<ul>
													<?php
													if ($incident_id != 0) {
														echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>View Report</strong></a></li>";
													}
													else
													{
														echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit?tid=' . $laconica_mesg_id ."\">Create Report?</a></li>";
													}
													?>
													<li>
				                                    <!-- <a href="<?php echo url::base().'admin/messages/delete/'.$laconica_mesg_id ?>" onclick="return confirm('Delete cannot be undone. Are you sure you want to continue?')" class="del">Delete</a> --></li>
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
		</div>
	</div>