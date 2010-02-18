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
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2><?php echo $title; ?>
				<?php
				foreach ($services as $service)
				{
					echo "<a href=\"" . url::base() . "admin/messages/index/".$service->id."\">".$service->service_name."</a>";
				}
				?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>>Inbox</a></li>
						<?php
						if ($service_id == 1)
						{
							?><li><a href="?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>>Outbox</a></li><?php
						}
						?>
						<li><a href="?type=<?php echo $type ?>&period=a" <?php if ($period == 'a') echo "class=\"active\""; ?>>All</a></li>
						<li><a href="?type=<?php echo $type ?>&period=d" <?php if ($period == 'd') echo "class=\"active\""; ?>>Yesterday</a></li>
						<li><a href="?type=<?php echo $type ?>&period=m" <?php if ($period == 'm') echo "class=\"active\""; ?>>Last Month</a></li>
						<li><a href="?type=<?php echo $type ?>&period=y" <?php if ($period == 'y') echo "class=\"active\""; ?>>Last Year</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onClick="submitIds()">DELETE</a></li>
							<?php foreach($levels as $level) { ?>
								<li><a href="#" onClick="itemAction('rank', 'Mark As <?php echo $level->level_title?>', '', <?php echo $level->id?>)"><?php echo $level->level_title?></a></li>
							<?php } ?>
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
				<?php print form::open(NULL, array('id' => 'messagesMain', 'name' => 'messagesMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="level"  id="level"  value="">
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
								foreach ($messages as $message)
								{
									$message_id = $message->id;
									$message_from = $message->message_from;
									$message_to = $message->message_to;
									$incident_id = $message->incident_id;
									$message_description = text::auto_link($message->message);
									$message_detail = text::auto_link($message->message_detail);
									$message_date = date('Y-m-d', strtotime($message->message_date));
									$message_type = $message->message_type;
									?>
									<tr>
										<td class="col-1"><input name="message_id[]" value="<?php echo $message_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<p><?php echo $message_description; ?></p>
												<?php
												if ($message_detail)
												{
													?>
													<p><a href="javascript:preview('message_preview_<?php echo $message_id?>')">Preview Message</a></p>
													<div id="message_preview_<?php echo $message_id?>" style="display:none;">
														<?php echo $message_detail; ?>
													</div>
													<?php
												}
												?>
												<?php
												if ($service_id == 1 && $message_type == 1)
												{
													?>
													<div id="replies">

													</div>
													<a href="javascript:showReply('reply_<?php echo $message_id; ?>')" class="more">+Reply</a>
													<div id="reply_<?php echo $message_id; ?>" class="reply">
														<?php print form::open(url::base() . 'admin/messages/send/',array('id' => 'newreply_' . $message_id,
														 	'name' => 'newreply_' . $message_id)); ?>
														<div class="reply_can"><a href="javascript:cannedReply('1', 'message_<?php echo $message_id; ?>')">+Request Location</a>&nbsp;&nbsp;&nbsp;<a href="javascript:cannedReply('2', 'message_<?php echo $message_id; ?>')">+Request More Information</a></div>
														<div id="replyerror_<?php echo $message_id; ?>" class="reply_error"></div>
														<div class="reply_input"><?php print form::input('message_' .  $message_id, '', ' class="text long2" onkeyup="limitChars(this.id, \'160\', \'replyleft_' . $message_id . '\')" '); ?></div>
														<div class="reply_input"><a href="javascript:sendMessage('<?php echo $message_id; ?>' , 'sending_<?php echo $message_id; ?>')" title="Submit Message"><img src="<?php echo url::base() ?>media/img/admin/btn-send.gif" alt="Submit" border="0" /></a></div>
														<div class="reply_input" id="sending_<?php echo $message_id; ?>"></div>
														<div style="clear:both"></div>
														<?php print form::close(); ?>
														<div id="replyleft_<?php echo $message_id; ?>" class="replychars"></div>
													</div>
													<?php
												}
												?>
											</div>
											<ul class="info">
												<?php
												if ($message_type == 2)
												{
													?><li class="none-separator">To: <strong><?php echo $message_to; ?></strong><?php
												}
												else
												{
													?><li class="none-separator">From: <strong><?php echo $message_from; ?></strong><?php
												}
												?>
											</ul>
										</td>
										<td class="col-3"><?php echo $message_date; ?></td>
										<td class="col-4">
											<ul>
												<?php
												if ($incident_id != 0 && $message_type != 2) {
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>View Report</strong></a></li>";
												}
												elseif ($message_type != 2)
												{
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit?mid=' . $message_id ."\">Create Report?</a></li>";
												}
												?>
												<li>
                                                <a href="<?php echo url::base().'admin/messages/delete/'.$message_id ?>" onclick="return confirm('Delete cannot be undone. Are you sure you want to continue?')" class="del">Delete</a></li>
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
