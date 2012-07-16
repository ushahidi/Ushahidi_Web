<?php 
/**
 * Messages view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php admin::messages_subtabs($service_id); ?>
				</h2>

<?php
	Event::run('ushahidi_action.admin_messages_custom_layout');
	// Kill the rest of the page if this event has been utilized by a plugin
	if( ! Event::has_run('ushahidi_action.admin_messages_custom_layout')){
?>

				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::site()."admin/messages/index/".$service_id; ?>?type=1" <?php if ($type == '1') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.inbox');?></a></li>
						<?php
						if ($service_id == 1)
						{
							?><li><a href="<?php echo url::site()."admin/messages/index/".$service_id; ?>?type=2" <?php if ($type == '2') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.outbox');?></a></li><?php
						}
						?>
						<?php if ($type == '1')
						{ ?>
							<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
							<li><a href="?type=<?php echo $type ?>&level=0" <?php if ($level == '0') echo "class=\"active2\""; ?>><?php echo Kohana::lang('ui_main.all');?> (<?php echo $count_all; ?>)</a></li>
							<li><a href="?type=<?php echo $type ?>&level=4" <?php if ($level == '4') echo "class=\"active2\""; ?>><?php echo Kohana::lang('ui_main.trusted'); ?> (<?php echo $count_trusted; ?>)</a></li>
							<li><a href="?type=<?php echo $type ?>&level=2" <?php if ($level == '2') echo "class=\"active2\""; ?>><?php echo Kohana::lang('ui_main.spam');?> (<?php echo $count_spam; ?>)</a></li>
						<?php } ?>
						<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
						<li><a href="<?php echo
						url::site()."admin/messages/reporters/index/".$service_id; ?>">Reporters(<?php echo $count_reporters; ?>)</a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onClick="messagesAction('d', 'DELETE', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
							<li><a href="#" onClick="messagesAction('s', 'SPAM', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.spam'));?></a></li>
							<li><a href="#" onClick="messagesAction('n', 'NOT SPAM', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.not_spam'));?></a></li>
						</ul>
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
								foreach ($messages as $message)
								{
									$message_id = $message->id;
									$message_from = strip_tags($message->reporter->service_account);
									$message_to = strip_tags($message->message_to);
									$incident_id = $message->incident_id;
									$message_description = text::auto_link(strip_tags($message->message));
									$message_detail = nl2br(text::auto_link(strip_tags($message->message_detail)));
									$message_date = date('Y-m-d  H:i', strtotime($message->message_date));
									$message_type = $message->message_type;
									$message_level = $message->message_level;
									$latitude = $message->latitude;
									$longitude = $message->longitude;

									$level_id = $message->reporter->level_id;
									?>
									<tr <?php if ($message_level == "99") {
										echo " class=\"spam_tr\"";
									} ?>>
										<td class="col-1"><input name="message_id[]" id="message" value="<?php echo $message_id; ?>" type="checkbox" class="check-box"/></td>
										<td class="col-2">
											<div class="post">
												<p><?php echo $message_description; ?></p>
												<?php
												if ($message_detail OR $message->media != null)
												{
													?>
													<p><a href="javascript:preview('message_preview_<?php echo $message_id?>')"><?php echo Kohana::lang('ui_main.preview_message');?></a></p>
													<div id="message_preview_<?php echo $message_id?>" style="display:none;">
														<?php echo $message_detail; ?>

														<?php
														// Retrieve Attachments if any
														foreach($message->media as $photo) 
														{
															if ($photo->media_type == 1)
															{
																print "<div class=\"attachment_thumbs\" id=\"photo_". $photo->id ."\">";

																$thumb = $photo->media_thumb;
																$photo_link = $photo->media_link;
																$prefix = url::base().Kohana::config('upload.relative_directory');
																print "<a class='photothumb' rel='lightbox-group".$message_id."' href='$prefix/$photo_link'>";
																print "<img src=\"$prefix/$thumb\" border=\"0\" >";
																print "</a>";
																print "</div>";
															}
														}
														?>
													</div>
													<?php
												}
												// Action::message_extra_admin	- Message Additional/Extra Stuff
												Event::run('ushahidi_action.message_extra_admin', $message_id);
												Event::run('ushahidi_action.message_from_admin', $message_from);
												?>

												<?php if($reply_to == TRUE) { ?>

												<?php
												if ($service_id == 1 && $message_type == 1)
												{
													?>
													<div id="replies">

													</div>
													<a href="javascript:showReply('reply_<?php echo $message_id; ?>')" class="more">+<?php echo Kohana::lang('ui_main.reply');?></a>
													<div id="reply_<?php echo $message_id; ?>" class="reply">
														<?php print form::open(url::site() . 'admin/messages/send/',array('id' => 'newreply_' . $message_id,
															'name' => 'newreply_' . $message_id)); ?>
														<div class="reply_can"><a href="javascript:cannedReply('1', 'message_<?php echo $message_id; ?>')">+<?php echo Kohana::lang('ui_main.request_location');?></a>&nbsp;&nbsp;&nbsp;<a href="javascript:cannedReply('2', 'message_<?php echo $message_id; ?>')">+<?php echo Kohana::lang('ui_main.request_information');?></a></div>
														<div id="replyerror_<?php echo $message_id; ?>" class="reply_error"></div>
														<div class="reply_input"><?php print form::input('message_' .  $message_id, '', ' class="text long2" onkeyup="limitChars(this.id, \'160\', \'replyleft_' . $message_id . '\')" '); ?></div>
														<div class="reply_input"><a href="javascript:sendMessage('<?php echo $message_id; ?>' , 'sending_<?php echo $message_id; ?>')" title="Submit Message"><img src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-send.gif" alt="Submit" border="0" /></a></div>
														<div class="reply_input" id="sending_<?php echo $message_id; ?>"></div>
														<div style="clear:both"></div>
														<?php print form::close(); ?>
														<div id="replyleft_<?php echo $message_id; ?>" class="replychars"></div>
													</div>
													<?php
												}
												?>

											<?php } ?>
											</div>
											<ul class="info">
												<?php
												if ($message_type == 2)
												{
													?><li class="none-separator"><?php echo Kohana::lang('ui_admin.to');?>: <strong><?php echo $message_to; ?></strong><?php
												}
												else
												{
													?><li class="none-separator"><?php echo Kohana::lang('ui_admin.from');?>: <a href="<?php echo url::site()."admin/messages/reporters/index/".$service_id."?k=".urlencode($message_from);?>"><strong class="reporters_<?php echo $level_id?>"><?php echo $message_from; ?></strong></a><?php
												}

												if ($latitude != NULL AND $longitude != NULL)
												{
													?><li class="none-separator"> @ <?php echo $latitude; ?>,<?php echo $longitude; ?></li><?php
												}
												?>
											</ul>
										</td>
										<td class="col-3"><?php echo $message_date; ?></td>
										<td class="col-4">
											<ul>
												<?php
												if ($incident_id != 0 && $message_type != 2) {
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit/' . $incident_id ."\" class=\"status_yes\"><strong>".Kohana::lang('ui_admin.view_report')."</strong></a></li>";
												}
												elseif ($message_type != 2)
												{
													echo "<li class=\"none-separator\"><a href=\"". url::base() . 'admin/reports/edit?mid=' . $message_id ."\">".Kohana::lang('ui_admin.create_report')."?</a></li>";
												}
												?>
												<li><a href="javascript:messagesAction('d','DELETE','<?php echo(rawurlencode($message_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
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
				<div class="tabs">
					<div class="tab">
						<ul>
							<li><a href="#" onClick="messagesAction('d', 'DELETE', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
							<li><a href="#" onClick="messagesAction('s', 'SPAM', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.spam'));?></a></li>
							<li><a href="#" onClick="messagesAction('n', 'NOT SPAM', '')"><?php echo utf8::strtoupper(Kohana::lang('ui_main.not_spam'));?></a></li>
						</ul>
					</div>
				</div>
			</div>

<?php
	}
?>
