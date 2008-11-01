			<div class="bg">
				<h2><?php echo $title; ?></h2>
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
							<li><a href="#">DELETE</a></li>
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
									$message_description = $message->message;
									$message_date = date('Y-m-d', strtotime($message->message_date));
									?>
									<tr>
										<td class="col-1"><input name="message_id[]" id="message" value="<?php echo $message_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<p><?php echo $message_description; ?><br /><a href="<?php echo url::base() . 'admin/messages/view/' . $message_id; ?>" class="more">more</a></p>
											</div>
											<ul class="info">
												<li class="none-separator">From: <strong><?php echo $message_from; ?></strong>
											</ul>
										</td>
										<td class="col-3"><?php echo $message_date; ?></td>
										<td class="col-4">
											<ul>
												<li><a href="#" class="del" onclick="">Delete</a></li>
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