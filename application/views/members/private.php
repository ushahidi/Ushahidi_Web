<?php 
/**
 * Private Messages view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Private Messages View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php members::private_subtabs("view"); ?>
				</h2>

				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::site()."members/private/index/"; ?>?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.inbox');?></a></li>
						<li><a href="<?php echo url::site()."members/private/index/"; ?>?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.outbox');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php
						if ($type != '2')
						{
							?>
							<ul>
								<li><a href="#" onClick="messagesAction('d', 'DELETE', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
								<li><a href="#" onClick="messagesAction('r', 'MARK READ', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.mark_read'));?></a></li>
							</ul>
							<?php
						}
						?>
					</div>
				</div>
				<?php
				if ($form_error) {
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_main.messages');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'messageMain', 'name' => 'messageMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="level"  id="level"  value="">
					<input type="hidden" name="message_id[]" id="message_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"><input id="checkall" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'message_id[]' )" /></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.message_details');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.date');?></th>
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
								foreach ($messages as $priv_message)
								{
									$message_id = $priv_message->id;
									$parent_id = ($priv_message->parent_id) ? $priv_message->parent_id : $message_id;
									if ($priv_message->user_id == $user_id)
									{
										$message_from_user = ORM::factory("user", $priv_message->from_user_id);
									}
									else
									{
										$message_from_user = ORM::factory("user", $priv_message->user_id);
									}
									
									if ($message_from_user->loaded)
									{
										$message_from = $message_from_user->name;
									}
									else
									{
										$message_from = Kohana::lang('ui_admin.unknown');
									}
									$subject = $priv_message->private_subject;
									$message = text::auto_link($priv_message->private_message);
									$message_preview = text::limit_chars(strip_tags($message), 150, "...", true);
									$message_date = date('Y-m-d', strtotime($priv_message->private_message_date));
									$message_new = $priv_message->private_message_new;
									?>
									<tr>
										<td class="col-1"><input name="message_id[]" id="message" value="<?php echo $message_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<p><?php echo $subject; ?></p>
												<p><a href="javascript:preview('message_preview_<?php echo $message_id?>')"><?php echo Kohana::lang('ui_main.preview_message');?></a></p>
												<div id="message_preview_<?php echo $message_id?>" class="preview_div">
													<?php echo $message; ?>
												</div>
											</div>
											<ul class="info">
												<?php
												if ($type == 2)
												{
													?><li class="none-separator">To: <strong><?php echo $message_from; ?></strong><?php
												}
												else
												{
													?><li class="none-separator">From: <strong><a href="<?php echo  url::site()."members/private/send?to=".urlencode($message_from)."&p=".$parent_id ;?>"><?php echo $message_from; ?></a></strong><?php
												}
												?>
											</ul>
										</td>
										<td class="col-3"><?php echo $message_date; ?></td>
										<td class="col-4">
											<?php
											if ($type != '2')
											{
												?>
												<ul>
													<li class="none-separator"><a href="javascript:messagesAction('r','MARK READ','<?php echo(rawurlencode($message_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.mark_read');?></a></li>
													<li><a href="javascript:messagesAction('d','DELETE','<?php echo(rawurlencode($message_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
												</ul>
												<?php
											}
											else
											{
												echo "&nbsp;";
											}
											?>
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