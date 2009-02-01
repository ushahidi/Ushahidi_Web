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
							<li><a href="#" onClick="submitIds()">DELETE</a></li>
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
									$incident_id = $message->incident_id;
									$message_description = $message->message;
									$message_date = date('Y-m-d', strtotime($message->message_date));
									?>
									<tr>
										<td class="col-1"><input name="message_id[]" id="message_id" value="<?php echo $message_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<p><?php echo $message_description; ?></p>
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
											</div>
											<ul class="info">
												<li class="none-separator">From: <strong><?php echo $message_from; ?></strong>
											</ul>
										</td>
										<td class="col-3"><?php echo $message_date; ?></td>
										<td class="col-4">
											<ul>
												<?php
												if ($incident_id != 0) {
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>View Report</strong></a></li>";
												}
												else
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
